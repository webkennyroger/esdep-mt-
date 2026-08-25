@props(['model', 'placeholder' => 'Digite um e-mail e pressione Enter'])

<div
    x-data="{
        raw: $wire.entangle('{{ $model }}'),
        input: '',
        get emails() {
            return (this.raw ?? '').split(',').map(e => e.trim()).filter(e => e !== '');
        },
        addEmails() {
            const parts = this.input.split(',').map(p => p.trim()).filter(p => p !== '');
            const current = this.emails;
            const leftover = [];

            parts.forEach(part => {
                if (/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(part) && !current.includes(part)) {
                    current.push(part);
                } else if (part !== '') {
                    leftover.push(part);
                }
            });

            this.raw = current.join(', ');
            this.input = leftover.join(', ');
        },
        removeEmail(email) {
            this.raw = this.emails.filter(e => e !== email).join(', ');
        },
    }"
    class="flex w-full flex-wrap items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-sm focus-within:border-green-500 focus-within:ring-2 focus-within:ring-green-500/20 dark:border-[#16352a] dark:bg-[#0a1a14]"
>
    <template x-for="email in emails" :key="email">
        <span class="inline-flex items-center gap-x-1.5 rounded-full bg-green-100 py-1 ps-3 pe-1.5 text-xs font-medium text-green-800 dark:bg-[#00a477]/20 dark:text-[#4fd1a5]">
            <span x-text="email"></span>
            <button type="button" @click="removeEmail(email)"
                class="inline-flex size-4 shrink-0 items-center justify-center rounded-full hover:bg-green-200 focus:bg-green-200 focus:outline-none dark:hover:bg-[#00a477]/40 dark:focus:bg-[#00a477]/40">
                <span class="sr-only">Remover e-mail</span>
                <svg class="size-3 shrink-0" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </span>
    </template>

    <input type="text" x-model="input"
        @keydown.enter.prevent="addEmails()"
        @keydown.,.prevent="addEmails()"
        @blur="addEmails()"
        placeholder="{{ $placeholder }}"
        {{ $attributes }}
        class="min-w-[180px] flex-1 border-none bg-transparent px-1 py-1 text-sm text-slate-900 outline-none focus:ring-0 dark:text-[#e6f2ec] dark:placeholder:text-[#5a7068]">
</div>
