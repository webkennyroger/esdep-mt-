<button type="button" id="theme-toggle"
    class="flex h-7.5 w-7.5 items-center justify-center border border-[#d8e2dd] text-[#003f32] transition hover:border-[#008b5c] hover:text-[#008b5c] dark:border-[#2c4a3e] dark:text-[#e6f2ec] dark:hover:border-[#4fd1a5] dark:hover:text-[#4fd1a5] sm:h-8.5 sm:w-8.5"
    aria-label="Alternar entre modo claro e escuro" title="Alternar tema">
    <svg class="h-3.5 w-3.5 dark:hidden sm:h-3.75 sm:w-3.75" fill="none" stroke="currentColor" stroke-width="1.8"
        viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="4.5"></circle>
        <path stroke-linecap="round"
            d="M12 2.5v2.5M12 19v2.5M2.5 12h2.5M19 12h2.5M5.3 5.3l1.8 1.8M16.9 16.9l1.8 1.8M18.7 5.3l-1.8 1.8M7.1 16.9l-1.8 1.8">
        </path>
    </svg>
    <svg class="hidden h-3.5 w-3.5 dark:block sm:h-3.75 sm:w-3.75" fill="none" stroke="currentColor"
        stroke-width="1.8" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round"
            d="M21 12.8A8.5 8.5 0 1 1 11.2 3a6.6 6.6 0 0 0 9.8 9.8Z"></path>
    </svg>
</button>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var root = document.documentElement;
        var stored = localStorage.getItem('esdep-theme');
        var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

        if (stored === 'dark' || (!stored && prefersDark)) {
            root.classList.add('dark');
        }

        var btn = document.getElementById('theme-toggle');
        if (btn) {
            btn.addEventListener('click', function() {
                var isDark = root.classList.toggle('dark');
                localStorage.setItem('esdep-theme', isDark ? 'dark' : 'light');
            });
        }
    });
</script>
