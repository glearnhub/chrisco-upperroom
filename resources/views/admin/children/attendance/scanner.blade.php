@extends('layouts.admin')
@section('title', 'Attendance Scanner')

@push('styles')
<style>
#video-wrap { position:relative; display:block; width:100%; }
#video      { display:block; width:100%; height:auto; max-height:280px; object-fit:cover; background:#111; border-radius:0.75rem; }
#overlay    { position:absolute; top:0; left:0; width:100%; height:100%; }
.match-card { transition: all 0.3s; }
.match-card.new-match { animation: pop 0.4s ease; }
@keyframes pop { 0%{transform:scale(0.8);opacity:0} 60%{transform:scale(1.08)} 100%{transform:scale(1);opacity:1} }
#scan-status { transition: background 0.3s; }
</style>
@endpush

@section('content')
<div class="p-3 sm:p-6">

    {{-- Header --}}
    <div class="flex items-start justify-between gap-3 mb-5">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold" style="color:#0a1f44;">Attendance Scanner</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Face recognition — {{ \Carbon\Carbon::parse($date)->format('l, d M Y') }}</p>
        </div>
        <div class="flex gap-2 flex-shrink-0">
            <a href="{{ route('admin.children.attendance.history') }}"
               class="inline-flex items-center gap-1 px-3 py-2 text-xs sm:text-sm rounded border border-gray-300 bg-white text-gray-700">
                <i class="fas fa-history"></i> <span class="hidden sm:inline">View History</span>
            </a>
            <a href="{{ route('admin.children.index') }}"
               class="inline-flex items-center gap-1 px-3 py-2 text-xs sm:text-sm rounded border border-gray-300 bg-white text-gray-700">
                <i class="fas fa-arrow-left"></i> <span class="hidden sm:inline">Children</span>
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded flex items-center justify-between">
        <span><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
        <button onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
    </div>
    @endif

    @if($enrolled === 0)
    <div class="bg-yellow-50 border border-yellow-300 rounded-xl p-6 text-center">
        <i class="fas fa-exclamation-triangle text-yellow-500 text-3xl mb-3 block"></i>
        <p class="font-semibold text-yellow-800">No face profiles enrolled yet</p>
        <p class="text-sm text-yellow-700 mt-1">
            Open each child's record and upload a clear face photo first.
            Once saved, the system computes their face profile automatically.
        </p>
        <a href="{{ route('admin.children.index') }}"
           class="inline-block mt-3 px-4 py-2 rounded text-white text-sm font-semibold" style="background:#0a1f44;">
            Go to Children Records
        </a>
    </div>
    @else

    {{-- Settings bar --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wider">Date</label>
                <input type="date" id="att-date" value="{{ $date }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wider">Class Filter</label>
                <select id="att-class" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                    <option value="">All Classes ({{ $enrolled }} enrolled)</option>
                    @foreach($classes as $key => $label)
                    <option value="{{ $key }}" {{ $class == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wider">Match Sensitivity</label>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-400">Strict</span>
                    <input type="range" id="threshold" min="40" max="80" value="55" class="w-28 sm:w-36">
                    <span class="text-xs text-gray-400">Lenient</span>
                    <span id="threshold-label" class="text-sm font-semibold text-gray-700 ml-1">55%</span>
                </div>
            </div>
            <button id="load-btn"
                    class="px-4 py-2 text-sm rounded text-white font-semibold" style="background:#0a1f44;">
                <i class="fas fa-sync-alt mr-1"></i> Load Profiles
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Left: Camera / Upload --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div id="scan-status" class="mb-3 px-3 py-2 rounded text-sm font-semibold text-center bg-gray-100 text-gray-600">
                <i class="fas fa-circle-notch fa-spin mr-1 hidden" id="spinner"></i>
                <span id="status-text">Loading face recognition models…</span>
            </div>

            {{-- Mode tabs --}}
            <div class="flex rounded-lg border border-gray-200 mb-4 overflow-hidden">
                <button id="tab-camera" onclick="setMode('camera')"
                        class="flex-1 py-2 text-sm font-semibold bg-navy text-white" style="background:#0a1f44;">
                    <i class="fas fa-camera mr-1"></i> Live Camera
                </button>
                <button id="tab-upload" onclick="setMode('upload')"
                        class="flex-1 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-50">
                    <i class="fas fa-upload mr-1"></i> Upload Photo
                </button>
            </div>

            {{-- Camera panel --}}
            <div id="panel-camera">
                <div class="mb-3">
                    <div id="video-wrap" class="rounded-xl overflow-hidden border-2 border-gray-200 w-full">
                        <video id="video" autoplay muted playsinline></video>
                        <canvas id="overlay"></canvas>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <button id="start-camera" onclick="startCamera()"
                            class="py-2 text-sm rounded text-white font-semibold" style="background:#059669;">
                        <i class="fas fa-play mr-1"></i> <span class="hidden sm:inline">Start </span>Camera
                    </button>
                    <button id="scan-btn" onclick="scanFrame()" disabled
                            class="py-2 text-sm rounded text-white font-semibold bg-gray-400 cursor-not-allowed">
                        <i class="fas fa-search mr-1"></i> Scan Now
                    </button>
                    <button id="auto-btn" onclick="toggleAuto()"
                            class="py-2 text-sm rounded border border-gray-300 text-gray-600 hover:bg-gray-50">
                        <i class="fas fa-redo mr-1"></i> Auto
                    </button>
                </div>
            </div>

            {{-- Upload panel --}}
            <div id="panel-upload" class="hidden">
                <label class="block w-full border-2 border-dashed border-gray-300 rounded-xl p-8 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition" id="drop-zone">
                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2 block"></i>
                    <p class="text-sm text-gray-600">Drop a photo here or <span class="text-blue-600 font-semibold">click to browse</span></p>
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG — group or individual photos supported</p>
                    <input type="file" id="photo-upload" accept="image/*" class="hidden" onchange="handleUpload(this)">
                </label>
                <div id="upload-preview" class="mt-4 hidden">
                    <img id="upload-img" class="max-w-full rounded-xl border border-gray-200 mx-auto block" style="max-height:300px;">
                    <canvas id="upload-canvas" class="hidden"></canvas>
                </div>
                <div class="mt-3 text-center hidden" id="scan-upload-btn-wrap">
                    <button onclick="scanUploadedImage()"
                            class="px-5 py-2 text-sm rounded text-white font-semibold" style="background:#0a1f44;">
                        <i class="fas fa-search mr-1"></i> Detect Faces in Photo
                    </button>
                </div>
            </div>
        </div>

        {{-- Right: Matched children --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-5 flex flex-col">
            <div class="flex items-center justify-between mb-3 gap-2">
                <h2 class="font-bold text-gray-800 text-sm sm:text-base">
                    <i class="fas fa-user-check mr-1 text-green-600"></i>
                    Detected Today
                    <span id="match-count" class="ml-1 text-sm font-normal text-gray-500">(0)</span>
                </h2>
                <button onclick="saveAll()"
                        class="px-3 py-2 text-xs sm:text-sm rounded text-white font-semibold disabled:opacity-40 flex-shrink-0" style="background:#059669;"
                        id="save-btn" disabled>
                    <i class="fas fa-save mr-1"></i> Save
                </button>
            </div>

            {{-- Already saved today --}}
            @if($alreadyMarked->count())
            <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded-lg text-xs sm:text-sm text-green-800">
                <i class="fas fa-check-circle mr-1"></i>
                <strong>{{ $alreadyMarked->count() }}</strong> already saved:
                {{ $alreadyMarked->pluck('child.first_name')->implode(', ') }}
            </div>
            @endif

            <div id="matches-grid" class="grid grid-cols-2 gap-2 sm:gap-3 flex-1 overflow-y-auto" style="max-height:320px;">
                <div class="col-span-2 text-center text-gray-400 py-10" id="empty-state">
                    <i class="fas fa-face-smile text-3xl mb-2 block"></i>
                    <p class="text-sm">Matched children will appear here</p>
                </div>
            </div>

            {{-- Manual add --}}
            <div class="mt-3 border-t border-gray-100 pt-3">
                <p class="text-xs text-gray-500 mb-2 font-semibold uppercase tracking-wider">Add manually</p>
                <div class="flex gap-2">
                    <select id="manual-select" class="flex-1 border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none min-w-0">
                        <option value="">Select child…</option>
                    </select>
                    <button onclick="addManual()"
                            class="px-3 py-1.5 rounded text-white text-sm font-semibold flex-shrink-0" style="background:#0a1f44;">
                        Add
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
{{-- face-api.js from CDN --}}
<script src="{{ asset('face-models/face-api.min.js') }}"></script>

<script>
const MODELS_URL = '{{ asset('face-models') }}';
const SAVE_URL   = @json(route('admin.children.attendance.save'));
const DESC_URL   = @json(route('admin.children.attendance.descriptors'));
const CSRF       = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

var labeledDescriptors = [];
var allProfiles        = [];
var matchedMap         = {};
var videoStream        = null;
var autoInterval       = null;
var modelsReady        = false;

// ── Status helper ─────────────────────────────────────────────────
function setStatus(text, type = 'info') {
    const el = document.getElementById('scan-status');
    const colors = { info: 'bg-blue-50 text-blue-700', success: 'bg-green-50 text-green-700',
                     error: 'bg-red-50 text-red-700',  scanning: 'bg-yellow-50 text-yellow-700' };
    el.className = 'mb-3 px-3 py-2 rounded text-sm font-semibold text-center ' + (colors[type] || colors.info);
    document.getElementById('status-text').textContent = text;
    document.getElementById('spinner').classList.toggle('hidden', type !== 'scanning');
}

// ── Load face-api models ──────────────────────────────────────────
async function loadModels() {
    try {
        setStatus('Loading model 1/3: face detector…', 'scanning');
        await faceapi.nets.tinyFaceDetector.loadFromUri(MODELS_URL);

        setStatus('Loading model 2/3: landmarks…', 'scanning');
        await faceapi.nets.faceLandmark68Net.loadFromUri(MODELS_URL);

        setStatus('Loading model 3/3: recognition (largest file)…', 'scanning');
        await faceapi.nets.faceRecognitionNet.loadFromUri(MODELS_URL);

        modelsReady = true;
        setStatus('Models ready — loading profiles…', 'scanning');
        await loadProfiles();
    } catch (e) {
        console.error('Model load error:', e);
        setStatus('Failed to load models: ' + e.message, 'error');
    }
}

// ── Load child profiles from server ──────────────────────────────
document.getElementById('load-btn').addEventListener('click', loadProfiles);

async function loadProfiles() {
    if (!modelsReady) return setStatus('Models not ready yet.', 'error');
    const cls = document.getElementById('att-class').value;
    setStatus('Loading child profiles…', 'scanning');
    try {
        const res  = await fetch(DESC_URL + '?class=' + encodeURIComponent(cls));
        const data = await res.json();
        allProfiles = data;

        if (!data.length) {
            setStatus('No enrolled children found for this class.', 'error');
            return;
        }

        labeledDescriptors = data.map(p => {
            const arr  = Array.isArray(p.descriptor) ? p.descriptor : Object.values(p.descriptor);
            const desc = new Float32Array(arr);
            console.log(`Profile loaded: ${p.name} (id=${p.id}) descriptor length=${desc.length}`);
            return new faceapi.LabeledFaceDescriptors(String(p.id), [desc]);
        });

        // Populate manual select
        const sel = document.getElementById('manual-select');
        sel.innerHTML = '<option value="">Select child…</option>';
        data.forEach(p => {
            const o = document.createElement('option');
            o.value = p.id; o.textContent = p.name;
            sel.appendChild(o);
        });

        setStatus(`${data.length} profile(s) loaded — ready to scan!`, 'success');
    } catch (e) {
        console.error('loadProfiles error:', e);
        setStatus('Failed to load profiles: ' + e.message, 'error');
    }
}

// ── Camera ───────────────────────────────────────────────────────
async function startCamera() {
    setStatus('Starting camera…', 'scanning');
    try {
        // Try with constraints first, fall back to bare {video:true} if driver rejects
        try {
            videoStream = await navigator.mediaDevices.getUserMedia({ video: { width: 640, height: 480, facingMode: 'user' } });
        } catch (constraintErr) {
            console.warn('Constraint getUserMedia failed, retrying bare:', constraintErr.name);
            videoStream = await navigator.mediaDevices.getUserMedia({ video: true });
        }
        var vid = document.getElementById('video');
        vid.srcObject = videoStream;
        // Sync overlay canvas size to actual video dimensions once metadata loads
        vid.onloadedmetadata = function() {
            var ov = document.getElementById('overlay');
            ov.width  = vid.videoWidth;
            ov.height = vid.videoHeight;
        };
        setMode('camera');
        document.getElementById('scan-btn').disabled = false;
        document.getElementById('scan-btn').className = 'py-2 text-sm rounded text-white font-semibold cursor-pointer';
        document.getElementById('scan-btn').style.background = '#0a1f44';
        setStatus('Camera active. Click "Scan Now" or enable Auto.', 'success');
    } catch (e) {
        console.warn('Camera error:', e.name, e.message);
        if (e.name === 'NotAllowedError' || e.name === 'PermissionDeniedError') {
            setMode('upload');
            setStatus('Camera permission denied — upload a photo below, or allow camera access in browser settings.', 'error');
        } else if (e.name === 'NotFoundError' || e.name === 'DevicesNotFoundError') {
            setStatus('No camera found on this device. Use Upload mode.', 'error');
            setMode('upload');
        } else if (e.name === 'NotReadableError' || e.name === 'TrackStartError') {
            setStatus('Camera is in use by another app. Close other camera apps or tabs, then click Start Camera again.', 'error');
        } else {
            setStatus('Camera error: ' + e.message + '. Try closing other apps using the camera.', 'error');
        }
    }
}

async function scanFrame() {
    if (!modelsReady) return setStatus('Models still loading, please wait…', 'scanning');
    if (!labeledDescriptors.length) {
        setStatus('No profiles loaded yet — click "Load Profiles" above.', 'error');
        return;
    }
    const video = document.getElementById('video');
    if (!videoStream || video.readyState < 2) {
        return setStatus('Camera not ready. Click "Start Camera" first.', 'error');
    }
    setStatus('Scanning…', 'scanning');
    const results = await detectAndMatch(video);
    if (!results) return;
    results.forEach(r => addMatch(r.id, r.name, r.confidence, 'face'));
    if (results.length) {
        setStatus(`Scan complete — ${results.length} face(s) matched!`, 'success');
    } else {
        setStatus('No match found. Try adjusting lighting or lowering the confidence threshold.', 'info');
    }
}

let autoOn = false;
function toggleAuto() {
    autoOn = !autoOn;
    const btn = document.getElementById('auto-btn');
    if (autoOn) {
        btn.style.background = '#c0392b'; btn.style.color = '#fff'; btn.textContent = '⏹ Stop Auto';
        autoInterval = setInterval(scanFrame, 2500);
    } else {
        btn.style.background = ''; btn.style.color = ''; btn.textContent = '↺ Auto';
        clearInterval(autoInterval);
    }
}

// ── Upload mode ───────────────────────────────────────────────────
function setMode(mode) {
    document.getElementById('panel-camera').classList.toggle('hidden', mode !== 'camera');
    document.getElementById('panel-upload').classList.toggle('hidden', mode !== 'upload');
    document.getElementById('tab-camera').style.background = mode === 'camera' ? '#0a1f44' : '';
    document.getElementById('tab-camera').style.color      = mode === 'camera' ? '#fff' : '';
    document.getElementById('tab-upload').style.background = mode === 'upload' ? '#0a1f44' : '';
    document.getElementById('tab-upload').style.color      = mode === 'upload' ? '#fff' : '';
}

function handleUpload(input) {
    const file = input.files[0]; if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        const img = document.getElementById('upload-img');
        img.src = e.target.result;
        document.getElementById('upload-preview').classList.remove('hidden');
        document.getElementById('scan-upload-btn-wrap').classList.remove('hidden');
    };
    reader.readAsDataURL(file);
}

// Drag and drop
const dz = document.getElementById('drop-zone');
dz.addEventListener('dragover', e => { e.preventDefault(); dz.classList.add('border-blue-500'); });
dz.addEventListener('dragleave', () => dz.classList.remove('border-blue-500'));
dz.addEventListener('drop', e => {
    e.preventDefault(); dz.classList.remove('border-blue-500');
    const file = e.dataTransfer.files[0];
    if (file) { document.getElementById('photo-upload').files = e.dataTransfer.files; handleUpload({ files: [file] }); }
});

async function scanUploadedImage() {
    if (!modelsReady) return setStatus('Models are still loading, please wait a moment…', 'scanning');
    if (!labeledDescriptors.length) return setStatus('No enrolled children found. Enroll a child\'s face first via Children → Edit.', 'error');
    const img = document.getElementById('upload-img');
    if (!img || !img.naturalWidth) return setStatus('Photo not loaded yet — please wait a second and try again.', 'error');
    setStatus('Analysing photo…', 'scanning');
    try {
        const results = await detectAndMatch(img);
        if (!results) return;
        if (results.length === 0) return; // status already set inside detectAndMatch
        results.forEach(r => addMatch(r.id, r.name, r.confidence, 'face'));
        setStatus(`Done — ${results.length} face(s) matched.`, 'success');
    } catch (err) {
        console.error('Detection error:', err);
        setStatus('Detection failed: ' + err.message, 'error');
    }
}

// ── Core detection & matching ─────────────────────────────────────
async function detectAndMatch(source) {
    if (!labeledDescriptors.length) { setStatus('Load profiles first.', 'error'); return null; }

    // For video: snapshot to canvas first (much faster than reading live frames)
    var inputEl = source;
    if (source.tagName === 'VIDEO') {
        var snap = document.createElement('canvas');
        snap.width  = source.videoWidth  || 400;
        snap.height = source.videoHeight || 300;
        snap.getContext('2d').drawImage(source, 0, 0, snap.width, snap.height);
        inputEl = snap;
    }

    const w = inputEl.naturalWidth || inputEl.width || inputEl.videoWidth;
    const h = inputEl.naturalHeight || inputEl.height || inputEl.videoHeight;
    console.log('Running detectAllFaces on', inputEl.tagName, w, 'x', h);
    if (!w || !h) {
        setStatus('Image has no dimensions — try a different photo.', 'error');
        return null;
    }

    // inputSize must be a multiple of 32; pick closest ≥ longest side, max 416
    const longest = Math.max(w, h);
    const sizes = [128, 160, 224, 320, 416];
    const inputSize = sizes.find(s => s >= longest) || 416;
    const opts = new faceapi.TinyFaceDetectorOptions({ inputSize, scoreThreshold: 0.2 });
    console.log('Using inputSize:', inputSize);
    const detections = await faceapi.detectAllFaces(inputEl, opts)
        .withFaceLandmarks()
        .withFaceDescriptors();

    console.log('Detections found:', detections.length);

    if (!detections.length) {
        setStatus('No face detected — ensure face fills the frame, good lighting, camera facing you.', 'info');
        return [];
    }

    const sliderVal    = parseInt(document.getElementById('threshold').value);
    const distThreshold = sliderVal / 100;
    console.log('Matching against', labeledDescriptors.length, 'profiles, distance threshold:', distThreshold);
    const matcher = new faceapi.FaceMatcher(labeledDescriptors, distThreshold);
    const results = [];

    detections.forEach((d, i) => {
        const match    = matcher.findBestMatch(d.descriptor);
        const distance = match.distance;
        const pct      = Math.round((1 - distance) * 100);
        console.log(`Face ${i+1}: best match="${match.label}" distance=${distance.toFixed(3)} confidence=${pct}%`);
        if (match.label !== 'unknown') {
            const profile = allProfiles.find(p => String(p.id) === match.label);
            if (profile) results.push({ id: profile.id, name: profile.name, confidence: pct });
        }
    });

    // Draw boxes on overlay (camera mode)
    const canvas = document.getElementById('overlay');
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    detections.forEach((d, i) => {
        const box = d.detection.box;
        ctx.strokeStyle = results[i] ? '#16a34a' : '#dc2626';
        ctx.lineWidth = 2;
        ctx.strokeRect(box.x, box.y, box.width, box.height);
        if (results[i]) {
            ctx.fillStyle = '#16a34a';
            ctx.font = '12px Arial';
            ctx.fillText(results[i].name, box.x, box.y - 4);
        }
    });

    return results;
}

// ── Match card management ─────────────────────────────────────────
function addMatch(id, name, confidence, method) {
    if (matchedMap[id]) {
        // Update confidence if better
        if (confidence > matchedMap[id].confidence) {
            matchedMap[id].confidence = confidence;
            document.getElementById('match-conf-' + id).textContent = confidence + '%';
        }
        return;
    }
    matchedMap[id] = { id, name, confidence, method };
    document.getElementById('empty-state').classList.add('hidden');

    const card = document.createElement('div');
    card.id = 'card-' + id;
    card.className = 'match-card new-match bg-green-50 border border-green-200 rounded-xl p-3 flex items-center gap-2';
    card.innerHTML = `
        <div class="w-8 h-8 rounded-full bg-green-200 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-user-check text-green-700 text-xs"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="font-semibold text-gray-800 text-sm truncate">${name}</p>
            <p class="text-xs text-gray-500">
                ${method === 'face' ? '<i class="fas fa-camera"></i>' : '<i class="fas fa-hand-pointer"></i>'}
                <span id="match-conf-${id}">${confidence}%</span> confidence
            </p>
        </div>
        <button onclick="removeMatch(${id})" class="text-red-400 hover:text-red-600 text-xs flex-shrink-0">
            <i class="fas fa-times"></i>
        </button>`;
    document.getElementById('matches-grid').appendChild(card);

    updateCount();
}

function removeMatch(id) {
    delete matchedMap[id];
    const card = document.getElementById('card-' + id);
    if (card) card.remove();
    if (!Object.keys(matchedMap).length) document.getElementById('empty-state').classList.remove('hidden');
    updateCount();
}

function updateCount() {
    const n = Object.keys(matchedMap).length;
    document.getElementById('match-count').textContent = '(' + n + ')';
    document.getElementById('save-btn').disabled = n === 0;
}

function addManual() {
    const sel = document.getElementById('manual-select');
    const id  = sel.value;
    const name = sel.options[sel.selectedIndex].text;
    if (!id) return;
    addMatch(parseInt(id), name, 100, 'manual');
    sel.value = '';
}

// ── Save attendance ───────────────────────────────────────────────
async function saveAll() {
    const matches = Object.values(matchedMap);
    if (!matches.length) return;
    const date = document.getElementById('att-date').value;

    setStatus('Saving attendance…', 'scanning');
    try {
        const res = await fetch(SAVE_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ date, matches }),
        });
        const data = await res.json();
        setStatus(`✓ ${data.saved} attendance record(s) saved for ${date}!`, 'success');
        // Reload after 1.5s to show updated "already saved" panel
        setTimeout(() => location.reload(), 1500);
    } catch (e) {
        setStatus('Failed to save. Please try again.', 'error');
    }
}

// ── Threshold slider ──────────────────────────────────────────────
document.getElementById('threshold').addEventListener('input', function () {
    document.getElementById('threshold-label').textContent = this.value + '%';
});

// ── Boot ─────────────────────────────────────────────────────────
loadModels();

// If the browser has no getUserMedia at all, default to upload
if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
    setMode('upload');
    setStatus('Camera not supported in this browser. Use Upload mode.', 'error');
}
</script>
@endpush
