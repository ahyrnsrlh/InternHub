<div
    x-data="{ show: false, message: 'Berhasil disimpan', type: 'success' }"
    x-on:notify.window="show = true; message = $event.detail.message || message; type = $event.detail.type || 'success'; setTimeout(() => show = false, 2500)"
    x-show="show"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 translate-y-4"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-2"
    class="pointer-events-none fixed bottom-6 right-6 z-50"
>
    <div class="pointer-events-auto flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-white shadow-lg"
         :class="type === 'error' ? 'bg-red-500' : 'bg-green-500'">
        <svg x-show="type !== 'error'" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <svg x-show="type === 'error'" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        <span x-text="message"></span>
    </div>
</div>
