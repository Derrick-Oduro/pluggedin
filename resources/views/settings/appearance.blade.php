<x-app-layout>
    <section class="relative overflow-hidden py-20 bg-gradient-to-b from-white via-orange-50/45 to-white dark:from-dark dark:via-dark-secondary dark:to-dark">
        <div class="absolute inset-0 pointer-events-none opacity-70">
            <div class="absolute -top-20 left-1/4 h-64 w-64 rounded-full bg-orange/20 blur-3xl"></div>
            <div class="absolute -bottom-16 right-1/4 h-64 w-64 rounded-full bg-orange/15 blur-3xl"></div>
        </div>

        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange mb-3">Settings</p>
                <h1 class="text-5xl sm:text-6xl font-bold mb-2">Appearance</h1>
                <p class="text-gray-600 dark:text-text-secondary">Choose the color mode that fits your workflow.</p>
            </div>

            @if(session('success'))
                <div class="bg-green-500/20 border border-green-500 text-green-500 rounded-lg p-4 mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-dark-secondary rounded-2xl p-8 border border-gray-200/70 dark:border-gray-800 shadow-sm">
                <h2 class="text-2xl font-bold mb-2">Theme Preference</h2>
                <p class="text-gray-600 dark:text-text-secondary mb-6">Select how the interface should appear while you browse the platform.</p>

                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <button onclick="setTheme('light')"
                                id="theme-light"
                                class="theme-option border-2 border-gray-300 dark:border-gray-700 rounded-lg p-6 transition hover:border-orange">
                            <div class="mb-3 inline-flex items-center justify-center w-12 h-12 rounded-xl bg-orange/10 text-orange">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1.5m0 15V21m6.364-14.364-1.06 1.06M6.696 17.304l-1.06 1.06M21 12h-1.5m-15 0H3m15.364 5.304-1.06-1.06M6.696 6.696l-1.06-1.06M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-lg mb-2">Light Mode</h3>
                            <p class="text-sm text-gray-600 dark:text-text-secondary">Bright and clean interface</p>
                        </button>

                        <button onclick="setTheme('dark')"
                                id="theme-dark"
                                class="theme-option border-2 border-gray-300 dark:border-gray-700 rounded-lg p-6 transition hover:border-orange">
                            <div class="mb-3 inline-flex items-center justify-center w-12 h-12 rounded-xl bg-orange/10 text-orange">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0 1 18 15.75 9.75 9.75 0 0 1 8.25 6c0-1.33.266-2.598.748-3.752A9.75 9.75 0 1 0 21.752 15.002Z" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-lg mb-2">Dark Mode</h3>
                            <p class="text-sm text-gray-600 dark:text-text-secondary">Easy on the eyes</p>
                        </button>

                        <button onclick="setTheme('system')"
                                id="theme-system"
                                class="theme-option border-2 border-gray-300 dark:border-gray-700 rounded-lg p-6 transition hover:border-orange">
                            <div class="mb-3 inline-flex items-center justify-center w-12 h-12 rounded-xl bg-orange/10 text-orange">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 5.25h16.5A2.25 2.25 0 0 1 22.5 7.5v9a2.25 2.25 0 0 1-2.25 2.25H3.75A2.25 2.25 0 0 1 1.5 16.5v-9a2.25 2.25 0 0 1 2.25-2.25Zm0 13.5h16.5" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-lg mb-2">System</h3>
                            <p class="text-sm text-gray-600 dark:text-text-secondary">Match system settings</p>
                        </button>
                    </div>

                    <div class="mt-8 p-4 bg-gray-50 dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg transition-colors duration-200">
                        <p class="text-sm text-gray-600 dark:text-text-secondary">
                            Current theme: <span id="current-theme" class="font-semibold text-orange">Loading...</span>
                        </p>
                    </div>

                    <div class="mt-8">
                        <h3 class="text-xl font-bold mb-4">Preview</h3>
                        <div id="theme-preview" class="border-2 border-gray-300 dark:border-gray-700 rounded-lg p-6 bg-white dark:bg-dark-secondary transition-colors duration-200">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="bg-orange w-12 h-12 rounded-lg"></div>
                                <div>
                                    <p class="font-bold text-gray-900 dark:text-white">Sample Card</p>
                                    <p class="text-sm text-gray-600 dark:text-text-secondary">This is how content will appear</p>
                                </div>
                            </div>
                            <button class="bg-orange hover:bg-orange-light text-white px-6 py-2 rounded-lg font-semibold transition">
                                Sample Button
                            </button>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <p class="text-sm text-gray-500 dark:text-text-secondary">
                            Your theme preference is saved automatically and will persist across sessions.
                        </p>
                        <a href="{{ route('settings.index') }}" class="inline-flex items-center text-orange hover:text-orange-light font-semibold">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                            </svg>
                            Back to all settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Get theme from localStorage or default to dark
        function getTheme() {
            return localStorage.getItem('theme') || 'dark';
        }

        // Set theme
        function setTheme(theme) {
            localStorage.setItem('theme', theme);
            applyTheme(theme);
            updateThemeUI(theme);

            // Save to backend
            fetch('{{ route("settings.appearance.update") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ theme: theme })
            });
        }

        // Apply theme to document
        function applyTheme(theme) {
            const root = document.documentElement;

            if (theme === 'system') {
                const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                theme = systemPrefersDark ? 'dark' : 'light';
            }

            if (theme === 'dark') {
                root.classList.add('dark');
            } else {
                root.classList.remove('dark');
            }
        }

        // Update UI to show selected theme
        function updateThemeUI(theme) {
            // Remove active state from all options
            document.querySelectorAll('.theme-option').forEach(btn => {
                btn.classList.remove('border-orange', 'bg-orange/10');
            });

            // Add active state to selected option
            const selected = document.getElementById('theme-' + theme);
            if (selected) {
                selected.classList.add('border-orange', 'bg-orange/10');
            }

            // Update current theme text
            document.getElementById('current-theme').textContent =
                theme.charAt(0).toUpperCase() + theme.slice(1) + ' Mode';
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            const currentTheme = getTheme();
            applyTheme(currentTheme);
            updateThemeUI(currentTheme);
        });

        // Listen for system theme changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (getTheme() === 'system') {
                applyTheme('system');
            }
        });
    </script>
</x-app-layout>
