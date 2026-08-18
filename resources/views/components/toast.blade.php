<div
    x-data="{
        show: false,
        type: 'success',
        title: '',
        message: '',
    }"
    x-on:toast.window="
        let d = $event.detail;
        if (Array.isArray(d)) d = d[0] || {};
        type = d.type || 'success';
        title = d.title || '';
        message = d.message || '';
        show = true;
        if (window._toastTimer) clearTimeout(window._toastTimer);
        window._toastTimer = setTimeout(() => { show = false }, 4000);
    "
    x-show="show"
    x-cloak
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-2"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-2"
    class="fixed bottom-4 right-4 z-[999] max-w-sm"
>
    <div class="rounded-xl border px-4 py-3 shadow-lg"
         :class="{
             'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-700 dark:bg-emerald-950 dark:text-emerald-200': type === 'success',
             'border-red-200 bg-red-50 text-red-800 dark:border-red-700 dark:bg-red-950 dark:text-red-200': type === 'error'
         }">
        <div class="flex items-start gap-3">
            <svg x-show="type === 'success'" class="mt-0.5 h-5 w-5 shrink-0 text-emerald-500 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            <svg x-show="type === 'error'" class="mt-0.5 h-5 w-5 shrink-0 text-red-500 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <div class="min-w-0 flex-1">
                <p x-show="title" x-text="title" class="text-sm font-semibold"></p>
                <p x-show="message" x-text="message" class="mt-0.5 text-sm opacity-80"></p>
            </div>
            <button @click="show = false" class="shrink-0 opacity-50 hover:opacity-100">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>
</div>
