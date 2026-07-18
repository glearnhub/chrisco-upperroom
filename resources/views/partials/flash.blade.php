@if(session('success') && !request()->routeIs('events.show'))
    <div class="max-w-7xl mx-auto px-4 pt-4">
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded flex items-center justify-between">
            <span><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800 ml-4">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="max-w-7xl mx-auto px-4 pt-4">
        <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded flex items-center justify-between">
            <span><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800 ml-4">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
@endif
