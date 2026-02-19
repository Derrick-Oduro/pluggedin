<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-4xl font-bold mb-8">⚙️ Settings</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Settings Navigation -->
            <div class="space-y-2">
                <a href="#appearance"
                   onclick="showSection('appearance')"
                   id="nav-appearance"
                   class="settings-nav-link flex items-center gap-3 px-4 py-3 rounded-lg transition bg-orange/20 text-orange border-l-4 border-orange">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                    </svg>
                    <span class="font-semibold">Appearance</span>
                </a>

                <a href="#account"
                   onclick="showSection('account')"
                   id="nav-account"
                   class="settings-nav-link flex items-center gap-3 px-4 py-3 rounded-lg transition text-gray-600 dark:text-text-secondary hover:bg-gray-100 dark:hover:bg-gray-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="font-semibold">Account</span>
                </a>

                <a href="#notifications"
                   onclick="showSection('notifications')"
                   id="nav-notifications"
                   class="settings-nav-link flex items-center gap-3 px-4 py-3 rounded-lg transition text-gray-600 dark:text-text-secondary hover:bg-gray-100 dark:hover:bg-gray-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="font-semibold">Notifications</span>
                </a>

                <a href="{{ route('profile.edit') }}"
                   class="settings-nav-link flex items-center gap-3 px-4 py-3 rounded-lg transition text-gray-600 dark:text-text-secondary hover:bg-gray-100 dark:hover:bg-gray-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <span class="font-semibold">Security</span>
                </a>
            </div>

            <!-- Settings Content -->
            <div class="md:col-span-2">
                @if(session('success'))
                    <div class="bg-green-500/20 border border-green-500 text-green-500 rounded-lg p-4 mb-6">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Appearance Section -->
                <div id="section-appearance" class="settings-section">
                    <div class="bg-white dark:bg-dark-secondary rounded-lg p-8 border border-gray-200 dark:border-gray-800">
                        <h2 class="text-2xl font-bold mb-6">Appearance</h2>

                        <div class="space-y-6">
                            <!-- Theme Options -->
                            <div>
                                <h3 class="text-lg font-semibold mb-4">Theme Preference</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- Light Mode -->
                                    <button onclick="setTheme('light')"
                                            id="theme-light"
                                            class="theme-option border-2 rounded-lg p-6 transition hover:border-orange">
                                        <div class="text-4xl mb-3">☀️</div>
                                        <h4 class="font-bold text-lg mb-2">Light Mode</h4>
                                        <p class="text-sm text-gray-500 dark:text-text-secondary">Bright and clean</p>
                                    </button>

                                    <!-- Dark Mode -->
                                    <button onclick="setTheme('dark')"
                                            id="theme-dark"
                                            class="theme-option border-2 rounded-lg p-6 transition hover:border-orange">
                                        <div class="text-4xl mb-3">🌙</div>
                                        <h4 class="font-bold text-lg mb-2">Dark Mode</h4>
                                        <p class="text-sm text-gray-500 dark:text-text-secondary">Easy on the eyes</p>
                                    </button>

                                    <!-- System Default -->
                                    <button onclick="setTheme('system')"
                                            id="theme-system"
                                            class="theme-option border-2 rounded-lg p-6 transition hover:border-orange">
                                        <div class="text-4xl mb-3">💻</div>
                                        <h4 class="font-bold text-lg mb-2">System</h4>
                                        <p class="text-sm text-gray-500 dark:text-text-secondary">Match system</p>
                                    </button>
                                </div>
                            </div>

                            <!-- Current Theme Display -->
                            <div class="p-4 bg-gray-50 dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg">
                                <p class="text-sm text-gray-600 dark:text-text-secondary">
                                    Current theme: <span id="current-theme" class="font-semibold text-orange">Loading...</span>
                                </p>
                            </div>

                            <!-- Preview -->
                            <div>
                                <h3 class="text-lg font-semibold mb-4">Preview</h3>
                                <div class="border-2 border-gray-300 dark:border-gray-700 rounded-lg p-6 bg-dark-secondary light:bg-gray-50">
                                    <div class="flex items-center gap-4 mb-4">
                                        <div class="bg-orange w-12 h-12 rounded-lg"></div>
                                        <div>
                                            <p class="font-bold">Sample Card</p>
                                            <p class="text-sm text-gray-500 dark:text-text-secondary">This is how content will appear</p>
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

                <!-- Account Section -->
                <div id="section-account" class="settings-section hidden">
                    <div class="bg-white dark:bg-dark-secondary rounded-lg p-8 border border-gray-200 dark:border-gray-800">
                        <h2 class="text-2xl font-bold mb-6">Account Settings</h2>
                        <p class="text-gray-600 dark:text-text-secondary mb-4">Manage your account information and preferences.</p>
                        <a href="{{ route('profile.edit') }}" class="text-orange hover:text-orange-light font-semibold">
                            Edit Profile →
                        </a>
                    </div>
                </div>

                <!-- Notifications Section -->
                <div id="section-notifications" class="settings-section hidden">
                    <div class="bg-white dark:bg-dark-secondary rounded-lg p-8 border border-gray-200 dark:border-gray-800">
                        <h2 class="text-2xl font-bold mb-6">Notification Preferences</h2>
                        <p class="text-gray-600 dark:text-text-secondary">Configure how you receive notifications about orders, bookings, and updates.</p>
                        <p class="text-sm text-gray-500 dark:text-text-secondary mt-4">(Coming soon)</p>
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
            document.querySelectorAll('.theme-option').forEach(btn => {
                btn.classList.remove('border-orange', 'bg-orange/10');
            });

            const selected = document.getElementById('theme-' + theme);
            if (selected) {
                selected.classList.add('border-orange', 'bg-orange/10');
            }

            const currentThemeEl = document.getElementById('current-theme');
            if (currentThemeEl) {
                currentThemeEl.textContent = theme.charAt(0).toUpperCase() + theme.slice(1) + ' Mode';
            }
        }

        // Show section
        function showSection(section) {
            // Hide all sections
            document.querySelectorAll('.settings-section').forEach(s => s.classList.add('hidden'));

            // Show selected section
            document.getElementById('section-' + section).classList.remove('hidden');

            // Update navigation
            document.querySelectorAll('.settings-nav-link').forEach(link => {
                link.classList.remove('bg-orange/20', 'text-orange', 'border-orange', 'border-l-4');
                link.classList.add('text-text-secondary');
            });

            const navLink = document.getElementById('nav-' + section);
            if (navLink) {
                navLink.classList.remove('text-text-secondary');
                navLink.classList.add('bg-orange/20', 'text-orange', 'border-l-4', 'border-orange');
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            const currentTheme = getTheme();
            applyTheme(currentTheme);
            updateThemeUI(currentTheme);

            // Check URL hash for section
            const hash = window.location.hash.substring(1);
            if (hash && ['appearance', 'account', 'notifications'].includes(hash)) {
                showSection(hash);
            }
        });

        // Listen for system theme changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (getTheme() === 'system') {
                applyTheme('system');
            }
        });
    </script>
</x-app-layout>
