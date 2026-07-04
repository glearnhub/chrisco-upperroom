@php $currentRoute = Route::currentRouteName(); @endphp
<div class="mt-5 flex justify-center flex-wrap gap-1">

    @php
        $tabs = [
            ['route' => 'sermons.index',   'icon' => 'fas fa-bible',           'label' => 'Sermons'],
            ['route' => 'livestream',      'icon' => 'fas fa-broadcast-tower', 'label' => 'Livestream'],
            ['route' => 'teachings.index', 'icon' => 'fas fa-book-open',       'label' => 'Teachings'],
            ['route' => 'resources.index', 'icon' => 'fas fa-file-pdf',        'label' => 'Books & Articles'],
            ['route' => 'gallery.index',   'icon' => 'fas fa-images',          'label' => 'Gallery'],
            ['route' => 'apostle.index',   'icon' => 'fas fa-video',           'label' => 'Apostle Das Teachings'],
        ];
    @endphp

    @foreach($tabs as $tab)
        @if($currentRoute === $tab['route'])
            <span class="inline-flex items-center gap-1.5 px-4 py-1.5 text-sm font-semibold rounded-full"
                  style="background: #f0a500; color: #0a1f44;">
                <i class="{{ $tab['icon'] }} text-xs"></i>{{ $tab['label'] }}
            </span>
        @else
            <a href="{{ route($tab['route']) }}"
               class="inline-flex items-center gap-1.5 px-4 py-1.5 text-sm rounded-full transition-all"
               style="color: rgba(255,255,255,0.6); border: 1px solid rgba(255,255,255,0.15);"
               onmouseover="this.style.color='#f0a500'; this.style.borderColor='#f0a500';"
               onmouseout="this.style.color='rgba(255,255,255,0.6)'; this.style.borderColor='rgba(255,255,255,0.15)';">
                <i class="{{ $tab['icon'] }} text-xs"></i>{{ $tab['label'] }}
            </a>
        @endif
    @endforeach
</div>
