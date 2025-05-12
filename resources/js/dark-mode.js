export default () => ({
    isDarkMode: false,
    init() {
        this.isDarkMode = localStorage.getItem('theme') === 'dark' ||
            (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);
        this.applyTheme();

        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
            if (!('theme' in localStorage)) {
                this.isDarkMode = e.matches;
                this.applyTheme();
            }
        });
    },
    toggleDarkMode() {
        this.isDarkMode = !this.isDarkMode;
        localStorage.setItem('theme', this.isDarkMode ? 'dark' : 'light');
        this.applyTheme();
    },
    applyTheme() {
        if (this.isDarkMode) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }
});
