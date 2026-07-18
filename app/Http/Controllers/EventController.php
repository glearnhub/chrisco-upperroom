<?php

namespace App\Http\Controllers;

use App\Mail\EventOtpMail;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::upcoming()
            ->orderByRaw("CASE WHEN LOWER(title) LIKE '%sunday service%' THEN 0 ELSE 1 END")
            ->paginate(9);
        return view('events.index', compact('events'));
    }

    public function show(Event $event)
    {
        $event->load('registrations');
        $registrationCount = $event->registrations()->where('status', '!=', 'cancelled')->count();
        return view('events.show', compact('event', 'registrationCount'));
    }

    /**
     * AJAX: send OTP to email for event registration verification.
     */
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email', 'event_id' => 'required|exists:events,id']);

        $email = strtolower(trim($request->email));
        $rateLimiterKey = 'otp_send_' . sha1($email);

        if (RateLimiter::tooManyAttempts($rateLimiterKey, 3)) {
            $seconds = RateLimiter::availableIn($rateLimiterKey);
            return response()->json(['success' => false, 'message' => "Too many attempts. Try again in {$seconds} seconds."], 429);
        }

        RateLimiter::hit($rateLimiterKey, 600);

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $event = Event::findOrFail($request->event_id);

        Cache::put('event_otp_' . sha1($email), [
            'code'     => $otp,
            'attempts' => 0,
        ], now()->addMinutes(10));

        try {
            Mail::to($email)->send(new EventOtpMail($otp, $event->title));
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Failed to send email. Please check your email address and try again.'], 500);
        }

        return response()->json(['success' => true]);
    }

    /**
     * AJAX: verify OTP entered by user.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate(['email' => 'required|email', 'otp' => 'required|string|size:6']);

        $email = strtolower(trim($request->email));
        $cacheKey = 'event_otp_' . sha1($email);
        $data = Cache::get($cacheKey);

        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Code expired. Please request a new one.']);
        }

        if ($data['attempts'] >= 5) {
            Cache::forget($cacheKey);
            return response()->json(['success' => false, 'message' => 'Too many incorrect attempts. Please request a new code.']);
        }

        if ($data['code'] !== $request->otp) {
            $data['attempts']++;
            Cache::put($cacheKey, $data, now()->addMinutes(10));
            $remaining = 5 - $data['attempts'];
            return response()->json(['success' => false, 'message' => "Incorrect code. {$remaining} attempt(s) remaining."]);
        }

        // OTP correct — store verified token (30 min window to complete registration)
        Cache::forget($cacheKey);
        $token = bin2hex(random_bytes(16));
        Cache::put('event_otp_verified_' . sha1($email), $token, now()->addMinutes(30));

        return response()->json(['success' => true, 'token' => $token]);
    }

    /**
     * AJAX: look up a member by email, return their details for form pre-fill.
     */
    public function lookupEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['found' => false]);
        }

        // Map the user's office/role to a registration category
        $category = $this->deriveCategory($user);

        return response()->json([
            'found'     => true,
            'full_name' => $user->full_name,
            'phone'     => $user->phone ?? '',
            'email'     => $user->email,
            'category'  => $category,
            'member_id' => $user->id, // used only for form pre-fill, not exposed publicly
        ]);
    }

    public function register(Request $request, Event $event)
    {
        $validated = $request->validate([
            'full_name'      => 'required|string|max:255',
            'phone'          => 'required|string|max:20',
            'email'          => 'nullable|email|max:255',
            'category'       => 'required|in:presbyter,pastor,elder,deacon,deaconess,member,visitor',
            'member_id'      => 'nullable|exists:users,id',
            'otp_token'      => 'nullable|string',
        ]);

        // Verify OTP token if email was provided
        if (!empty($validated['email'])) {
            $email = strtolower(trim($validated['email']));
            $storedToken = Cache::get('event_otp_verified_' . sha1($email));
            if (!$storedToken || $storedToken !== $request->otp_token) {
                return back()->with('error', 'Email verification required. Please verify your email before registering.');
            }
        }

        // Prevent duplicate registration by email for the same event
        if (!empty($validated['email'])) {
            $exists = EventRegistration::where('event_id', $event->id)
                ->where('email', $validated['email'])
                ->where('status', '!=', 'cancelled')
                ->exists();

            if ($exists) {
                return back()->with('error', 'This email is already registered for this event.');
            }
        }

        // Also prevent duplicate by member_id
        if (!empty($validated['member_id'])) {
            $exists = EventRegistration::where('event_id', $event->id)
                ->where('member_id', $validated['member_id'])
                ->where('status', '!=', 'cancelled')
                ->exists();

            if ($exists) {
                return back()->with('error', 'This member is already registered for this event.');
            }
        }

        if ($event->registration_required && $event->isFull()) {
            return back()->with('error', 'Sorry, this event is fully booked.');
        }

        EventRegistration::create([
            'event_id'  => $event->id,
            'user_id'   => auth()->id() ?? null,
            'member_id' => $validated['member_id'] ?? null,
            'full_name' => $validated['full_name'],
            'phone'     => $validated['phone'],
            'email'     => $validated['email'] ?? null,
            'category'  => $validated['category'],
            'status'    => 'registered',
        ]);

        return back()->with('success', 'You have been successfully registered for ' . $event->title . '!');
    }

    private function deriveCategory(User $user): string
    {
        $office = strtolower($user->office ?? '');
        $role   = strtolower($user->role ?? '');

        if (str_contains($office, 'presbyter')) return 'presbyter';
        if (str_contains($office, 'pastor') || str_contains($role, 'pastor')) return 'pastor';
        if (str_contains($office, 'elder')) return 'elder';
        if (str_contains($office, 'deaconess')) return 'deaconess';
        if (str_contains($office, 'deacon')) return 'deacon';
        if ($user->role === 'member') return 'member';

        return 'member';
    }
}
