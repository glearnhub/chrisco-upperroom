<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password — Chrisco Upper Room</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: linear-gradient(135deg, #0a1f44 0%, #1a3a6b 100%); min-height: 100vh; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

<div class="w-full max-w-md">

    {{-- Logo / Header --}}
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full mb-4" style="background:rgba(255,255,255,0.1);">
            <i class="fas fa-key text-yellow-400 text-2xl"></i>
        </div>
        <h1 class="text-white text-2xl font-bold">Set Your Password</h1>
        <p class="text-blue-200 text-sm mt-2">Your account requires a new password before you can continue.</p>
    </div>

    {{-- Card --}}
    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100" style="background:#0a1f44;">
            <p class="text-white text-sm font-semibold flex items-center gap-2">
                <i class="fas fa-user-shield text-yellow-400"></i>
                Logged in as: <span class="text-yellow-300">{{ auth()->user()->email }}</span>
            </p>
        </div>

        <form method="POST" action="{{ route('admin.password.force.update') }}" class="px-6 py-6 space-y-5">
            @csrf @method('PATCH')

            @if($errors->any())
            <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg text-sm">
                <i class="fas fa-exclamation-circle mr-1"></i>
                {{ $errors->first() }}
            </div>
            @endif

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    New Password <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="password" name="password" id="pw1" autofocus
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm pr-10 focus:outline-none focus:ring-2 focus:ring-blue-400"
                           placeholder="At least 8 characters">
                    <button type="button" onclick="togglePw('pw1','eye1')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <i id="eye1" class="fas fa-eye text-sm"></i>
                    </button>
                </div>

                {{-- Password strength bar --}}
                <div class="mt-2 h-1.5 w-full bg-gray-200 rounded-full overflow-hidden">
                    <div id="strength-bar" class="h-full rounded-full transition-all duration-300" style="width:0%;background:#c0392b;"></div>
                </div>
                <p id="strength-label" class="text-xs text-gray-400 mt-1"></p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Confirm New Password <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="password" name="password_confirmation" id="pw2"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm pr-10 focus:outline-none focus:ring-2 focus:ring-blue-400"
                           placeholder="Repeat your new password">
                    <button type="button" onclick="togglePw('pw2','eye2')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <i id="eye2" class="fas fa-eye text-sm"></i>
                    </button>
                </div>
                <p id="match-label" class="text-xs mt-1 hidden"></p>
            </div>

            <button type="submit"
                    class="w-full py-2.5 rounded-lg text-sm font-bold text-white transition hover:opacity-90"
                    style="background:#0a1f44;">
                <i class="fas fa-check mr-2"></i>Set Password & Continue
            </button>
        </form>

        <div class="px-6 pb-5 text-center">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-xs text-gray-400 hover:text-red-500 transition">
                    <i class="fas fa-sign-out-alt mr-1"></i>Log out instead
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function togglePw(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    input.type  = input.type === 'password' ? 'text' : 'password';
    icon.classList.toggle('fa-eye');
    icon.classList.toggle('fa-eye-slash');
}

const pw1 = document.getElementById('pw1');
const pw2 = document.getElementById('pw2');

pw1.addEventListener('input', function () {
    const val = this.value;
    let score = 0;
    if (val.length >= 8)              score++;
    if (/[A-Z]/.test(val))            score++;
    if (/[0-9]/.test(val))            score++;
    if (/[^A-Za-z0-9]/.test(val))     score++;

    const bar   = document.getElementById('strength-bar');
    const label = document.getElementById('strength-label');
    const levels = [
        { pct: '0%',   color: '#c0392b', text: '' },
        { pct: '25%',  color: '#c0392b', text: 'Weak' },
        { pct: '50%',  color: '#e67e22', text: 'Fair' },
        { pct: '75%',  color: '#f0a500', text: 'Good' },
        { pct: '100%', color: '#27ae60', text: 'Strong' },
    ];
    const lvl = val.length === 0 ? levels[0] : levels[score];
    bar.style.width     = lvl.pct;
    bar.style.background = lvl.color;
    label.textContent   = lvl.text;
    label.style.color   = lvl.color;

    checkMatch();
});

pw2.addEventListener('input', checkMatch);

function checkMatch() {
    const lbl = document.getElementById('match-label');
    if (!pw2.value) { lbl.classList.add('hidden'); return; }
    lbl.classList.remove('hidden');
    if (pw1.value === pw2.value) {
        lbl.textContent   = '✓ Passwords match';
        lbl.style.color   = '#27ae60';
    } else {
        lbl.textContent   = '✗ Passwords do not match';
        lbl.style.color   = '#c0392b';
    }
}
</script>

</body>
</html>
