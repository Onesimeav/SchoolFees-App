<div
    x-data="{
        theme: localStorage.getItem('theme') || 'system',
        get isDark() {
            if (this.theme === 'dark') return true;
            if (this.theme === 'light') return false;
            return window.matchMedia('(prefers-color-scheme: dark)').matches;
        },
        toggle() {
            this.theme = this.isDark ? 'light' : 'dark';
            localStorage.setItem('theme', this.theme);
            $dispatch('theme-changed', this.theme);
        }
    }"
    class="flex items-center"
>
    <button
        type="button"
        x-on:click="toggle()"
        class="relative flex items-center justify-center rounded-lg p-1.5 text-gray-400 hover:bg-gray-500/10 hover:text-gray-600 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-200 transition-colors"
        :title="isDark ? 'Passer en mode clair' : 'Passer en mode sombre'"
    >
        {{-- Sun: shown when dark mode is active → click switches to light --}}
        <svg x-show="isDark" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
        </svg>
        {{-- Moon: shown when light mode is active → click switches to dark --}}
        <svg x-show="!isDark" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
        </svg>
    </button>
</div>