<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-4xl font-bold mb-8">⚙️ Appearance Settings</h1>

        @if(session('success'))
            <div class="bg-green-500/20 border border-green-500 text-green-500 rounded-lg p-4 mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white dark:bg-dark-secondary rounded-lg p-8 border border-gray-200 dark:border-gray-800">
            <h2 class="text-2xl font-bold mb-6">Theme Preference</h2>

            <div class="space-y-6">
                <!-- Theme Options -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Light Mode -->
                    <button onclick="setTheme('light')"
                            id="theme-light"
                            class="theme-option border-2 border-gray-300 dark:border-gray-700 rounded-lg p-6 transition hover:border-orange">
                        <div class="text-4xl mb-3">☀️</div>
                        <h3 class="font-bold text-lg mb-2">Light Mode</h3>
                        <p class="text-sm text-gray-600 dark:text-text-secondary">Bright and clean interface</p>
                    </button>

                    <!-- Dark Mode -->
                    <button onclick="setTheme('dark')"
                            id="theme-dark"
                            class="theme-option border-2 border-gray-300 dark:border-gray-700 rounded-lg p-6 transition hover:border-orange">
                        <div class="text-4xl mb-3">🌙</div>
                        <h3 class="font-bold text-lg mb-2">Dark Mode</h3>
                        <p class="text-sm text-gray-600 dark:text-text-secondary">Easy on the eyes</p>
                    </button>

                    <!-- System Default -->
                    <button onclick="setTheme('system')"
                            id="theme-system"
                            class="theme-option border-2 border-gray-300 dark:border-gray-700 rounded-lg p-6 transition hover:border-orange">
                        <div class="text-4xl mb-3">💻</div>
                        <h3 class="font-bold text-lg mb-2">System</h3>
                        <p class="text-sm text-gray-600 dark:text-text-secondary">Match system settings</p>
                    </button>
                </div>

                <!-- Current Theme Display -->
                <div class="mt-8 p-4 bg-gray-50 dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg transition-colors duration-200">
                    <p class="text-sm text-gray-600 dark:text-text-secondary">
                        Current theme: <span id="current-theme" class="font-semibold text-orange">Loading...</span>
                    </p>
                </div>

                <!-- Preview -->
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
            </div>
        </div>
    </div>

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
