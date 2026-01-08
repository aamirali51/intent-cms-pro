<?php
$title = 'Settings';
ob_start();
?>

<div>
    <!-- Page Header with Save Button -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Settings</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Configure your site settings</p>
        </div>
        <button onclick="saveSettings()" id="save-btn" class="px-5 py-2.5 bg-primary hover:bg-primaryHover text-white rounded-lg font-medium inline-flex items-center transition-colors shadow-lg shadow-primary/20">
            <span class="material-icons-round text-lg mr-2">save</span>
            Save Changes
        </button>
    </div>

    <!-- Tabs -->
    <div class="border-b border-border-light dark:border-border-dark mb-6">
        <nav class="-mb-px flex space-x-6 overflow-x-auto" aria-label="Tabs">
            <button onclick="switchTab('general')" data-tab="general" class="tab-btn border-primary text-primary whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                General
            </button>
            <button onclick="switchTab('permalinks')" data-tab="permalinks" class="tab-btn border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Permalinks
            </button>
            <button onclick="switchTab('reading')" data-tab="reading" class="tab-btn border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Reading
            </button>
            <button onclick="switchTab('discussion')" data-tab="discussion" class="tab-btn border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Discussion
            </button>
            <button onclick="switchTab('media')" data-tab="media" class="tab-btn border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Media Sizes
            </button>
        </nav>
    </div>

    <!-- Tab Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content (2 cols) -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- General Tab -->
            <div id="tab-general" class="tab-content space-y-6">
                <!-- Site Identity Card -->
                <div class="bg-surface-light dark:bg-surface-dark shadow-sm rounded-xl border border-border-light dark:border-border-dark p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Site Identity</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Basic information about your website.</p>
                        </div>
                        <div class="h-10 w-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                            <span class="material-icons-round text-primary">badge</span>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="site_title">Site Title</label>
                            <input type="text" id="site_title" name="site_title" class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-gray-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="tagline">Tagline</label>
                            <input type="text" id="tagline" name="tagline" class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-gray-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm" />
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">In a few words, explain what this site is about.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="admin_email">Administration Email Address</label>
                            <div class="mt-1 relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="material-icons-round text-gray-400 text-lg">mail</span>
                                </span>
                                <input type="email" id="admin_email" name="admin_email" class="block w-full pl-10 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-gray-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm" />
                            </div>
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">This address is used for admin purposes.</p>
                        </div>
                    </div>
                </div>

                <!-- Regional Settings Card -->
                <div class="bg-surface-light dark:bg-surface-dark shadow-sm rounded-xl border border-border-light dark:border-border-dark p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Regional Settings</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Set the language and timezone for your site.</p>
                        </div>
                        <div class="h-10 w-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                            <span class="material-icons-round text-blue-500">public</span>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="language">Site Language</label>
                            <select id="language" name="language" class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-gray-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                                <option value="en_US">English (United States)</option>
                                <option value="es_ES">Spanish</option>
                                <option value="fr_FR">French</option>
                                <option value="de_DE">German</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="timezone">Timezone</label>
                                <select id="timezone" name="timezone" class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-gray-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                                    <option value="America/New_York">UTC-5 (Eastern Time)</option>
                                    <option value="UTC">UTC+0 (Coordinated Universal Time)</option>
                                    <option value="Europe/London">UTC+0 (London)</option>
                                    <option value="Europe/Paris">UTC+1 (Central European Time)</option>
                                    <option value="Asia/Karachi">UTC+5 (Pakistan)</option>
                                    <option value="Asia/Kolkata">UTC+5:30 (India)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="date_format">Date Format</label>
                                <select id="date_format" name="date_format" class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-gray-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                                    <option value="F j, Y">October 24, 2023 (F j, Y)</option>
                                    <option value="Y-m-d">2023-10-24 (Y-m-d)</option>
                                    <option value="m/d/Y">10/24/2023 (m/d/Y)</option>
                                    <option value="d/m/Y">24/10/2023 (d/m/Y)</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Week Starts On</label>
                            <div class="flex items-center space-x-6">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="week_starts_on" value="monday" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 dark:border-gray-600" />
                                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Monday</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="week_starts_on" value="sunday" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 dark:border-gray-600" />
                                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Sunday</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permalinks Tab -->
            <div id="tab-permalinks" class="tab-content hidden space-y-6">
                <div class="bg-surface-light dark:bg-surface-dark shadow-sm rounded-xl border border-border-light dark:border-border-dark p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Permalink Structure</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Configure how your URLs look.</p>
                        </div>
                        <div class="h-10 w-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                            <span class="material-icons-round text-indigo-500">link</span>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <label class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer">
                            <input type="radio" name="permalink_structure" value="/%postname%/" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 dark:border-gray-600" />
                            <span class="ml-3">
                                <span class="block text-sm font-medium text-gray-900 dark:text-white">Post Name</span>
                                <span class="block text-xs text-gray-500 dark:text-gray-400">yoursite.com/sample-post/</span>
                            </span>
                        </label>
                        <label class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer">
                            <input type="radio" name="permalink_structure" value="/%year%/%monthnum%/%postname%/" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 dark:border-gray-600" />
                            <span class="ml-3">
                                <span class="block text-sm font-medium text-gray-900 dark:text-white">Date and Name</span>
                                <span class="block text-xs text-gray-500 dark:text-gray-400">yoursite.com/2023/10/sample-post/</span>
                            </span>
                        </label>
                        <label class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer">
                            <input type="radio" name="permalink_structure" value="/?p=%post_id%" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 dark:border-gray-600" />
                            <span class="ml-3">
                                <span class="block text-sm font-medium text-gray-900 dark:text-white">Plain</span>
                                <span class="block text-xs text-gray-500 dark:text-gray-400">yoursite.com/?p=123</span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Reading Tab -->
            <div id="tab-reading" class="tab-content hidden space-y-6">
                <div class="bg-surface-light dark:bg-surface-dark shadow-sm rounded-xl border border-border-light dark:border-border-dark p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Reading Settings</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Configure what visitors see on your front page.</p>
                        </div>
                        <div class="h-10 w-10 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                            <span class="material-icons-round text-amber-500">auto_stories</span>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Your homepage displays</label>
                            <div class="space-y-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="show_on_front" value="posts" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 dark:border-gray-600" />
                                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Your latest posts</span>
                                </label>
                                <label class="inline-flex items-center ml-6">
                                    <input type="radio" name="show_on_front" value="page" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 dark:border-gray-600" />
                                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">A static page</span>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="posts_per_page">Blog pages show at most</label>
                            <div class="mt-1 flex items-center gap-2">
                                <input type="number" id="posts_per_page" name="posts_per_page" min="1" max="100" class="w-20 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm" />
                                <span class="text-sm text-gray-500 dark:text-gray-400">posts</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Discussion Tab -->
            <div id="tab-discussion" class="tab-content hidden space-y-6">
                <div class="bg-surface-light dark:bg-surface-dark shadow-sm rounded-xl border border-border-light dark:border-border-dark p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Discussion Settings</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Configure how comments are handled.</p>
                        </div>
                        <div class="h-10 w-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                            <span class="material-icons-round text-green-500">forum</span>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <input type="checkbox" id="comments_enabled" name="comments_enabled" value="1" class="h-4 w-4 mt-0.5 text-primary focus:ring-primary border-gray-300 dark:border-gray-600 rounded" />
                            <div class="ml-3">
                                <label class="text-sm font-medium text-gray-900 dark:text-white" for="comments_enabled">Allow comments on new posts</label>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Enable commenting functionality across your site.</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <input type="checkbox" id="comment_moderation" name="comment_moderation" value="1" class="h-4 w-4 mt-0.5 text-primary focus:ring-primary border-gray-300 dark:border-gray-600 rounded" />
                            <div class="ml-3">
                                <label class="text-sm font-medium text-gray-900 dark:text-white" for="comment_moderation">Comment must be manually approved</label>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Hold all comments for moderation before displaying.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Media Tab -->
            <div id="tab-media" class="tab-content hidden space-y-6">
                <div class="bg-surface-light dark:bg-surface-dark shadow-sm rounded-xl border border-border-light dark:border-border-dark p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Image Sizes</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Configure default image dimensions.</p>
                        </div>
                        <div class="h-10 w-10 rounded-lg bg-pink-100 dark:bg-pink-900/30 flex items-center justify-center">
                            <span class="material-icons-round text-pink-500">photo_size_select_large</span>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Thumbnail size</h4>
                            <div class="flex items-center gap-4">
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Width</label>
                                    <input type="number" id="thumbnail_size_w" name="thumbnail_size_w" class="w-24 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm" />
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Height</label>
                                    <input type="number" id="thumbnail_size_h" name="thumbnail_size_h" class="w-24 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm" />
                                </div>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Medium size</h4>
                            <div class="flex items-center gap-4">
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Max Width</label>
                                    <input type="number" id="medium_size_w" name="medium_size_w" class="w-24 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm" />
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Max Height</label>
                                    <input type="number" id="medium_size_h" name="medium_size_h" class="w-24 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm" />
                                </div>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Large size</h4>
                            <div class="flex items-center gap-4">
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Max Width</label>
                                    <input type="number" id="large_size_w" name="large_size_w" class="w-24 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm" />
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Max Height</label>
                                    <input type="number" id="large_size_h" name="large_size_h" class="w-24 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar (1 col) -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Membership Card -->
            <div class="bg-surface-light dark:bg-surface-dark shadow-sm rounded-xl border border-border-light dark:border-border-dark p-6">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Membership</h3>
                <div class="space-y-4">
                    <div class="flex items-start">
                        <input type="checkbox" id="allow_registration" name="allow_registration" value="1" class="h-4 w-4 mt-0.5 text-primary focus:ring-primary border-gray-300 dark:border-gray-600 rounded" />
                        <div class="ml-3">
                            <label class="text-sm font-medium text-gray-900 dark:text-white" for="allow_registration">Anyone can register</label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Allow new users to sign up from the login page.</p>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-gray-100 dark:border-gray-700">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="default_role">New User Default Role</label>
                        <select id="default_role" name="default_role" class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-gray-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                            <option value="subscriber">Subscriber</option>
                            <option value="contributor">Contributor</option>
                            <option value="author">Author</option>
                            <option value="editor">Editor</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Site Status Card -->
            <div class="bg-surface-light dark:bg-surface-dark shadow-sm rounded-xl border border-border-light dark:border-border-dark p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Site Status</h3>
                    <div id="site-status-indicator" class="flex items-center">
                        <span class="relative flex h-3 w-3 mr-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                        </span>
                        <span class="text-xs font-medium text-green-600 dark:text-green-400">Live</span>
                    </div>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    Put your site in maintenance mode to display a temporary page to visitors.
                </p>
                <button type="button" onclick="toggleMaintenance()" id="maintenance-btn" class="w-full inline-flex justify-center items-center px-4 py-2.5 border border-orange-300 dark:border-orange-600 shadow-sm text-sm font-medium rounded-lg text-orange-700 dark:text-orange-300 bg-orange-50 dark:bg-orange-900/20 hover:bg-orange-100 dark:hover:bg-orange-900/30 transition-colors">
                    <span class="material-icons-round mr-2 text-lg text-orange-500">build</span>
                    Enable Maintenance Mode
                </button>
                <input type="hidden" id="maintenance_mode" name="maintenance_mode" value="0" />
            </div>

            <!-- Danger Zone -->
            <div class="border border-red-200 dark:border-red-900/30 rounded-xl p-6 bg-red-50 dark:bg-red-900/10">
                <h3 class="text-base font-semibold text-red-600 dark:text-red-400 mb-2">Danger Zone</h3>
                <p class="text-sm text-red-600 dark:text-red-400/80 mb-4">
                    Be careful with these settings.
                </p>
                <button type="button" class="w-full inline-flex justify-center items-center px-4 py-2.5 border border-red-300 dark:border-red-700 text-sm font-medium rounded-lg text-white bg-red-500 hover:bg-red-600 shadow-sm transition-colors" onclick="App.showToast('This feature is not yet implemented.', 'warning')">
                    <span class="material-icons-round mr-2 text-lg">delete_forever</span>
                    Delete this Site
                </button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="mt-8 pt-6 border-t border-border-light dark:border-border-dark flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
        <p>&copy; <?= date('Y') ?> Intent CMS. All rights reserved.</p>
        <div class="flex gap-4">
            <a class="hover:text-primary" href="#">Documentation</a>
            <a class="hover:text-primary" href="#">Support</a>
            <span>Version 0.8.1</span>
        </div>
    </div>
</div>

<script>
let settings = {};
let maintenanceMode = false;

document.addEventListener('DOMContentLoaded', async () => {
    await App.loadCsrfToken();
    await loadSettings();
});

async function loadSettings() {
    try {
        settings = await App.api('/settings') || {};
        
        // Populate text/email inputs
        ['site_title', 'tagline', 'admin_email', 'posts_per_page', 
         'thumbnail_size_w', 'thumbnail_size_h', 'medium_size_w', 'medium_size_h',
         'large_size_w', 'large_size_h'].forEach(key => {
            const el = document.getElementById(key);
            if (el && settings[key] !== undefined) el.value = settings[key];
        });
        
        // Populate selects
        ['language', 'timezone', 'date_format', 'default_role'].forEach(key => {
            const el = document.getElementById(key);
            if (el && settings[key]) el.value = settings[key];
        });
        
        // Populate radio buttons
        ['week_starts_on', 'permalink_structure', 'show_on_front'].forEach(key => {
            if (settings[key]) {
                const radio = document.querySelector(`input[name="${key}"][value="${settings[key]}"]`);
                if (radio) radio.checked = true;
            }
        });
        
        // Populate checkboxes
        ['allow_registration', 'comments_enabled', 'comment_moderation'].forEach(key => {
            const el = document.getElementById(key);
            if (el) el.checked = settings[key] === '1';
        });
        
        // Maintenance mode
        maintenanceMode = settings.maintenance_mode === '1';
        updateMaintenanceUI();
        
    } catch (e) {
        console.error('Failed to load settings:', e);
    }
}

async function saveSettings() {
    const btn = document.getElementById('save-btn');
    btn.disabled = true;
    btn.innerHTML = '<span class="material-icons-round animate-spin text-lg mr-2">refresh</span> Saving...';
    
    try {
        const data = {};
        
        // Collect text/email/number inputs
        ['site_title', 'tagline', 'admin_email', 'posts_per_page',
         'thumbnail_size_w', 'thumbnail_size_h', 'medium_size_w', 'medium_size_h',
         'large_size_w', 'large_size_h'].forEach(key => {
            const el = document.getElementById(key);
            if (el) data[key] = el.value;
        });
        
        // Collect selects
        ['language', 'timezone', 'date_format', 'default_role'].forEach(key => {
            const el = document.getElementById(key);
            if (el) data[key] = el.value;
        });
        
        // Collect radio buttons
        ['week_starts_on', 'permalink_structure', 'show_on_front'].forEach(key => {
            const checked = document.querySelector(`input[name="${key}"]:checked`);
            if (checked) data[key] = checked.value;
        });
        
        // Collect checkboxes
        ['allow_registration', 'comments_enabled', 'comment_moderation'].forEach(key => {
            const el = document.getElementById(key);
            if (el) data[key] = el.checked ? '1' : '0';
        });
        
        // Maintenance mode
        data.maintenance_mode = maintenanceMode ? '1' : '0';
        
        const result = await App.api('/settings', 'PUT', data);
        
        if (result && result.message) {
            btn.innerHTML = '<span class="material-icons-round text-lg mr-2">check</span> Saved!';
            App.showToast('Settings saved successfully!', 'success');
            setTimeout(() => {
                btn.innerHTML = '<span class="material-icons-round text-lg mr-2">save</span> Save Changes';
                btn.disabled = false;
            }, 2000);
        } else {
            throw new Error('Failed to save');
        }
    } catch (e) {
        console.error('Save failed:', e);
        btn.innerHTML = '<span class="material-icons-round text-lg mr-2">error</span> Error';
        App.showToast('Failed to save settings: ' + e.message, 'error');
        setTimeout(() => {
            btn.innerHTML = '<span class="material-icons-round text-lg mr-2">save</span> Save Changes';
            btn.disabled = false;
        }, 2000);
    }
}

function switchTab(tabName) {
    // Hide all tab content
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    // Show selected tab
    document.getElementById('tab-' + tabName).classList.remove('hidden');
    
    // Update tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        if (btn.dataset.tab === tabName) {
            btn.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            btn.classList.add('border-primary', 'text-primary');
        } else {
            btn.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            btn.classList.remove('border-primary', 'text-primary');
        }
    });
}

function toggleMaintenance() {
    maintenanceMode = !maintenanceMode;
    updateMaintenanceUI();
}

function updateMaintenanceUI() {
    const indicator = document.getElementById('site-status-indicator');
    const btn = document.getElementById('maintenance-btn');
    
    if (maintenanceMode) {
        indicator.innerHTML = `
            <span class="relative flex h-3 w-3 mr-2">
                <span class="relative inline-flex rounded-full h-3 w-3 bg-yellow-500"></span>
            </span>
            <span class="text-xs font-medium text-yellow-600 dark:text-yellow-400">Maintenance</span>
        `;
        btn.innerHTML = '<span class="material-icons-round mr-2 text-lg">public</span> Go Live';
    } else {
        indicator.innerHTML = `
            <span class="relative flex h-3 w-3 mr-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
            </span>
            <span class="text-xs font-medium text-green-600 dark:text-green-400">Live</span>
        `;
        btn.innerHTML = '<span class="material-icons-round mr-2 text-lg">build</span> Enable Maintenance Mode';
    }
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
