@props([
    'title' => 'Executive Portal',
    'searchPlaceholder' => 'Search records...',
    'showLinks' => true,
])

<header class="fixed top-0 right-0 left-0 lg:left-72 z-40 h-16 px-6 lg:px-8 bg-surface/80 backdrop-blur-xl border-b border-line">
    <div class="h-full max-w-7xl mx-auto flex items-center justify-between gap-4">
        <div class="flex items-center gap-4 lg:gap-8 flex-1">
            <h1 class="text-lg font-black tracking-tight text-content">{{ $title }}</h1>
            <div class="hidden md:flex relative w-full max-w-md">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-content-soft">search</span>
                <input
                    type="text"
                    placeholder="{{ $searchPlaceholder }}"
                    class="w-full rounded-full border-0 bg-surface-muted py-2 pl-10 pr-4 text-sm text-content-muted focus:ring-2 focus:ring-line-strong"
                >
            </div>
        </div>
        <div class="flex items-center gap-4 lg:gap-6">
            @if ($showLinks)
                <nav class="hidden lg:flex items-center gap-4 text-sm font-medium text-content-muted">
                    <a href="#" class="hover:text-content transition-colors">Help</a>
                    <a href="#" class="hover:text-content transition-colors">Docs</a>
                </nav>
            @endif
            <button class="text-content-muted hover:text-content-muted transition-colors" type="button">
                <span class="material-symbols-outlined">notifications</span>
            </button>
            <button class="text-content-muted hover:text-content-muted transition-colors" type="button">
                <span class="material-symbols-outlined">settings</span>
            </button>
            <div class="hidden sm:flex items-center gap-2 border-l border-line pl-4">
                <span class="text-sm font-semibold text-content-muted">Profile</span>
                <span class="h-8 w-8 rounded-full bg-primary text-content-inverse grid place-content-center text-xs font-bold">AC</span>
            </div>
        </div>
    </div>
</header>
