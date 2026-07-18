@extends('layouts.admin')

@section('title', 'Edit Child')
@section('page-title', 'Members (Children)')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold" style="color:#0a1f44;">Edit: {{ $child->full_name }}</h1>
    <a href="{{ route('admin.children.show', $child) }}" class="btn-navy px-4 py-2 text-sm">
        <i class="fas fa-arrow-left mr-2"></i>Back
    </a>
</div>

<form method="POST" action="{{ route('admin.children.update', $child) }}" class="space-y-6">
@csrf @method('PUT')

{{-- Personal Information --}}
<div class="bg-white rounded-xl shadow p-6">
    <h2 class="font-bold text-lg mb-4 pb-2 border-b" style="color:#0a1f44;">Personal Information</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
            <input type="text" name="first_name" value="{{ old('first_name', $child->first_name) }}" required
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Middle Name</label>
            <input type="text" name="middle_name" value="{{ old('middle_name', $child->middle_name) }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
            <input type="text" name="last_name" value="{{ old('last_name', $child->last_name) }}" required
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Gender</label>
            <select name="gender" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none">
                <option value="">— Select —</option>
                <option value="male"   {{ old('gender', $child->gender) === 'male'   ? 'selected' : '' }}>Male</option>
                <option value="female" {{ old('gender', $child->gender) === 'female' ? 'selected' : '' }}>Female</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Date of Birth</label>
            <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $child->date_of_birth?->format('Y-m-d')) }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
    </div>
</div>

{{-- Parent 1 --}}
<div class="bg-white rounded-xl shadow p-6">
    <h2 class="font-bold text-lg mb-4 pb-2 border-b" style="color:#0a1f44;">
        <i class="fas fa-user mr-2" style="color:#c0392b;"></i>Parent / Guardian 1
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Select Registered Member</label>
            <select name="parent1_id" id="parent1_id" onchange="fillParent(1, this)"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none">
                <option value="">— Not a registered member —</option>
                @foreach($members as $m)
                    <option value="{{ $m->id }}" data-name="{{ $m->name }}" data-phone="{{ $m->phone }}"
                            {{ old('parent1_id', $child->parent1_id) == $m->id ? 'selected' : '' }}>
                        {{ $m->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Parent 1 Name</label>
            <input type="text" name="parent1_name" id="parent1_name"
                   value="{{ old('parent1_name', $child->parent1_name) }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Parent 1 Contact</label>
            <input type="text" name="parent1_contact" id="parent1_contact"
                   value="{{ old('parent1_contact', $child->parent1_contact) }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
    </div>
</div>

{{-- Parent 2 --}}
<div class="bg-white rounded-xl shadow p-6">
    <h2 class="font-bold text-lg mb-4 pb-2 border-b" style="color:#0a1f44;">
        <i class="fas fa-user mr-2" style="color:#f0a500;"></i>Parent / Guardian 2 <span class="text-gray-400 text-sm font-normal">(optional)</span>
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Select Registered Member</label>
            <select name="parent2_id" id="parent2_id" onchange="fillParent(2, this)"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none">
                <option value="">— Not a registered member —</option>
                @foreach($members as $m)
                    <option value="{{ $m->id }}" data-name="{{ $m->name }}" data-phone="{{ $m->phone }}"
                            {{ old('parent2_id', $child->parent2_id) == $m->id ? 'selected' : '' }}>
                        {{ $m->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Parent 2 Name</label>
            <input type="text" name="parent2_name" id="parent2_name"
                   value="{{ old('parent2_name', $child->parent2_name) }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Parent 2 Contact</label>
            <input type="text" name="parent2_contact" id="parent2_contact"
                   value="{{ old('parent2_contact', $child->parent2_contact) }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
    </div>
</div>

{{-- Church --}}
<div class="bg-white rounded-xl shadow p-6">
    <h2 class="font-bold text-lg mb-4 pb-2 border-b" style="color:#0a1f44;">Church Information</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Sunday School Class Level</label>
            <select name="sunday_school_class" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none">
                <option value="">— Select class —</option>
                @foreach($classes as $key => $label)
                    <option value="{{ $key }}" {{ old('sunday_school_class', $child->sunday_school_class) === $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Notes</label>
            <input type="text" name="notes" value="{{ old('notes', $child->notes) }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
    </div>
</div>

<div class="flex gap-3">
    <button type="submit" class="btn-red px-8 py-2 font-semibold">
        <i class="fas fa-save mr-2"></i>Save Changes
    </button>
    <a href="{{ route('admin.children.show', $child) }}" class="btn-navy px-6 py-2">Cancel</a>
</div>

</form>

{{-- Face Enrollment Card (outside main form, uses its own AJAX) --}}
<div class="bg-white rounded-xl shadow p-6 mt-6" id="face-enrollment-section">
    <h2 class="font-bold text-lg mb-1 pb-2 border-b" style="color:#0a1f44;">
        <i class="fas fa-id-card mr-2" style="color:#c0392b;"></i>Face Enrollment for Attendance
    </h2>
    <p class="text-sm text-gray-500 mb-4">Upload or capture a clear front-facing photo to enable face recognition attendance.</p>

    <div class="flex flex-col md:flex-row gap-6">
        {{-- Current photo --}}
        <div class="flex-shrink-0 text-center">
            <div id="current-photo-wrap" class="w-36 h-36 rounded-full border-4 border-gray-200 overflow-hidden mx-auto mb-2 flex items-center justify-center bg-gray-100">
                @if($child->photo)
                    <img id="enrolled-photo" src="{{ $child->photo_url }}" class="w-full h-full object-cover">
                @else
                    <i class="fas fa-user text-5xl text-gray-300" id="enrolled-placeholder"></i>
                @endif
            </div>
            @if($child->face_descriptor)
            <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-semibold">
                <i class="fas fa-check-circle"></i> Enrolled
            </span>
            @else
            <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 font-semibold" id="enroll-status-badge">
                <i class="fas fa-times-circle"></i> Not enrolled
            </span>
            @endif
        </div>

        {{-- Controls --}}
        <div class="flex-1 space-y-4">
            {{-- Upload --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Upload Photo</label>
                <input type="file" id="photo-upload" accept="image/*"
                       class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>

            {{-- Camera --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Or Use Camera</label>
                <div class="flex gap-2">
                    <button type="button" onclick="startCamera()" class="px-3 py-1.5 text-sm rounded border border-gray-300 hover:bg-gray-50">
                        <i class="fas fa-camera mr-1"></i> Open Camera
                    </button>
                    <button type="button" id="capture-btn" onclick="captureCamera()" class="px-3 py-1.5 text-sm rounded bg-blue-600 text-white hidden">
                        <i class="fas fa-circle mr-1"></i> Capture
                    </button>
                    <button type="button" id="stop-camera-btn" onclick="stopCamera()" class="px-3 py-1.5 text-sm rounded border border-gray-300 hidden">
                        <i class="fas fa-stop mr-1"></i> Stop
                    </button>
                </div>
            </div>

            <div id="camera-wrap" class="hidden">
                <video id="camera-video" autoplay playsinline class="w-48 h-36 rounded border border-gray-300 object-cover"></video>
                <canvas id="camera-canvas" class="hidden"></canvas>
            </div>

            <div id="preview-wrap" class="hidden">
                <img id="photo-preview" class="w-48 h-36 rounded border border-gray-300 object-cover">
            </div>

            <div id="model-status" class="text-xs text-amber-600 flex items-center gap-1">
                <i class="fas fa-spinner fa-spin"></i> Loading face detection models…
            </div>

            <div id="enroll-progress" class="hidden text-sm text-blue-600">
                <i class="fas fa-spinner fa-spin mr-1"></i> <span id="enroll-msg">Processing…</span>
            </div>

            <button type="button" id="enroll-btn" onclick="enrollFace()" disabled
                    class="px-5 py-2 text-sm rounded text-white font-semibold disabled:opacity-40"
                    style="background:#0a1f44;">
                <i class="fas fa-fingerprint mr-1"></i> Enroll Face
            </button>
        </div>
    </div>
</div>

@push('scripts')
{{-- face-api.js --}}
<script src="{{ asset('face-models/face-api.min.js') }}"></script>
<script>
let capturedBlob  = null;
let cameraStream  = null;
let modelsLoaded  = false;
let modelsResolve = null;
let modelsReject  = null;

const modelsPromise = new Promise((res, rej) => {
    modelsResolve = res;
    modelsReject  = rej;
});

async function loadModels() {
    const statusEl = document.getElementById('model-status');
    try {
        const base = '{{ asset('face-models') }}';
        const timeout = new Promise((_, rej) =>
            setTimeout(() => rej(new Error('timeout')), 30000)
        );
        await Promise.race([
            Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri(base),
                faceapi.nets.faceLandmark68Net.loadFromUri(base),
                faceapi.nets.faceRecognitionNet.loadFromUri(base),
            ]),
            timeout,
        ]);
        modelsLoaded = true;
        if (statusEl) statusEl.remove();
        if (capturedBlob) document.getElementById('enroll-btn').disabled = false;
        modelsResolve();
    } catch (e) {
        console.error('face-api models failed to load:', e);
        if (statusEl) {
            statusEl.innerHTML = `
                <i class="fas fa-exclamation-triangle text-red-500"></i>
                <span class="text-red-600">Models failed to load (check internet connection).</span>
                <button onclick="retryModels()" class="ml-2 underline text-blue-600 text-xs">Retry</button>`;
        }
        modelsReject(e);
    }
}

function retryModels() {
    const statusEl = document.getElementById('model-status');
    if (statusEl) {
        statusEl.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span class="text-amber-600">Retrying…</span>';
    }
    // Reset the promise
    Object.assign(window, { modelsPromise: new Promise((res, rej) => { modelsResolve = res; modelsReject = rej; }) });
    loadModels();
}

loadModels();

function fillParent(n, select) {
    const opt = select.options[select.selectedIndex];
    if (opt.value) {
        document.getElementById('parent' + n + '_name').value    = opt.dataset.name || '';
        document.getElementById('parent' + n + '_contact').value = opt.dataset.phone || '';
    }
}

// Upload
document.getElementById('photo-upload').addEventListener('change', function() {
    if (!this.files[0]) return;
    const reader = new FileReader();
    reader.onload = async e => {
        document.getElementById('photo-preview').src = e.target.result;
        document.getElementById('preview-wrap').classList.remove('hidden');
        capturedBlob = e.target.result;
        // Only enable button once models are ready
        await modelsPromise;
        document.getElementById('enroll-btn').disabled = false;
    };
    reader.readAsDataURL(this.files[0]);
});

// Camera
async function startCamera() {
    cameraStream = await navigator.mediaDevices.getUserMedia({ video: true });
    const video = document.getElementById('camera-video');
    video.srcObject = cameraStream;
    document.getElementById('camera-wrap').classList.remove('hidden');
    document.getElementById('capture-btn').classList.remove('hidden');
    document.getElementById('stop-camera-btn').classList.remove('hidden');
}

async function captureCamera() {
    const video = document.getElementById('camera-video');
    const canvas = document.getElementById('camera-canvas');
    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);
    capturedBlob = canvas.toDataURL('image/jpeg', 0.9);
    document.getElementById('photo-preview').src = capturedBlob;
    document.getElementById('preview-wrap').classList.remove('hidden');
    stopCamera();
    await modelsPromise;
    document.getElementById('enroll-btn').disabled = false;
}

function stopCamera() {
    if (cameraStream) {
        cameraStream.getTracks().forEach(t => t.stop());
        cameraStream = null;
    }
    document.getElementById('camera-wrap').classList.add('hidden');
    document.getElementById('capture-btn').classList.add('hidden');
    document.getElementById('stop-camera-btn').classList.add('hidden');
}

async function enrollFace() {
    if (!capturedBlob) return alert('Please select or capture a photo first.');
    if (!modelsLoaded) {
        setProgress('Waiting for models to load…');
        try { await modelsPromise; } catch(e) {
            hideProgress();
            return alert('Face detection models could not be loaded. Check your internet connection and retry.');
        }
    }

    setProgress('Detecting face…');

    const img = new Image();
    img.src = capturedBlob;
    await new Promise(r => img.onload = r);

    const detection = await faceapi
        .detectSingleFace(img, new faceapi.TinyFaceDetectorOptions({ scoreThreshold: 0.3 }))
        .withFaceLandmarks()
        .withFaceDescriptor();

    if (!detection) {
        hideProgress();
        return alert('No face detected in the photo. Please use a clear, well-lit front-facing photo.');
    }

    setProgress('Saving…');

    const descriptor = Array.from(detection.descriptor); // Float32Array → plain array

    const res = await fetch('{{ route('admin.children.save-descriptor', $child) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify({ descriptor, photo: capturedBlob }),
    });

    const json = await res.json();
    hideProgress();

    if (json.success) {
        // Update UI
        let wrap = document.getElementById('current-photo-wrap');
        wrap.innerHTML = `<img src="${json.photo_url}?t=${Date.now()}" class="w-full h-full object-cover">`;
        const badge = document.getElementById('enroll-status-badge');
        if (badge) {
            badge.className = 'inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-semibold';
            badge.innerHTML = '<i class="fas fa-check-circle"></i> Enrolled';
        }
        alert('Face enrolled successfully! This child will now be recognized during attendance scanning.');
    } else {
        alert('Failed to save. Please try again.');
    }
}

function setProgress(msg) {
    document.getElementById('enroll-msg').textContent = msg;
    document.getElementById('enroll-progress').classList.remove('hidden');
    document.getElementById('enroll-btn').disabled = true;
}
function hideProgress() {
    document.getElementById('enroll-progress').classList.add('hidden');
    document.getElementById('enroll-btn').disabled = false;
}
</script>
@endpush

@endsection


