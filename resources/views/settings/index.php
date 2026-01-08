<?php $active = 'settings'; ?>
<?php $title = 'Settings - Intent CMS'; ?>
<?php ob_start(); ?>

<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="border-b border-gray-200 dark:border-slate-700 w-full">
            <nav aria-label="Tabs" class="-mb-px flex space-x-6 overflow-x-auto">
                <a class="border-primary text-primary whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" href="#">
                    General
                </a>
                <a class="border-transparent text-gray-500 dark:text-gray-400 hover:text-slate-800 dark:hover:text-white hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" href="#">
                    Permalinks
                </a>
                <a class="border-transparent text-gray-500 dark:text-gray-400 hover:text-slate-800 dark:hover:text-white hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" href="#">
                    Reading
                </a>
                <a class="border-transparent text-gray-500 dark:text-gray-400 hover:text-slate-800 dark:hover:text-white hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" href="#">
                    Discussion
                </a>
                <a class="border-transparent text-gray-500 dark:text-gray-400 hover:text-slate-800 dark:hover:text-white hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" href="#">
                    Media Sizes
                </a>
            </nav>
        </div>
        <div class="flex-shrink-0 pt-2 sm:pt-0">
            <button class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary dark:focus:ring-offset-gray-900 transition-colors" type="button">
                <span class="material-icons-round mr-2 -ml-1 text-lg">save</span>
                Save Changes
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <!-- Site Identity -->
            <div class="bg-surface-light dark:bg-surface-dark shadow-sm rounded-lg border border-gray-200 dark:border-slate-700/60 p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-base font-semibold leading-6 text-slate-900 dark:text-white">Site Identity</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Basic information about your website.</p>
                    </div>
                    <span class="material-icons-round text-gray-400">badge</span>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-gray-300" for="site-title">Site Title</label>
                        <div class="mt-1">
                            <input class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white rounded-md p-2.5 border" id="site-title" name="site-title" type="text" value="Intent Tech Blog"/>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-gray-300" for="tagline">Tagline</label>
                        <div class="mt-1">
                            <input class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white rounded-md p-2.5 border" id="tagline" name="tagline" type="text" value="Just another Intent CMS site"/>
                        </div>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">In a few words, explain what this site is about.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-gray-300" for="admin-email">Administration Email Address</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="material-icons-round text-gray-400 text-lg">mail</span>
                            </div>
                            <input class="focus:ring-primary focus:border-primary block w-full pl-10 sm:text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white rounded-md p-2.5 border" id="admin-email" name="admin-email" type="email" value="admin@intentcms.com"/>
                        </div>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">This address is used for admin purposes. If you change this, we will send you an email at your new address to confirm it.</p>
                    </div>
                </div>
            </div>

            <!-- Regional Settings -->
            <div class="bg-surface-light dark:bg-surface-dark shadow-sm rounded-lg border border-gray-200 dark:border-slate-700/60 p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-base font-semibold leading-6 text-slate-900 dark:text-white">Regional Settings</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Set the language and timezone for your site.</p>
                    </div>
                    <span class="material-icons-round text-gray-400">public</span>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-gray-300" for="language">Site Language</label>
                        <select class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md border" id="language" name="language">
                            <option selected="">English (United States)</option>
                            <option>Spanish</option>
                            <option>French</option>
                            <option>German</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-gray-300" for="timezone">Timezone</label>
                            <select class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md border" id="timezone" name="timezone">
                                <option>UTC-5 (Eastern Time)</option>
                                <option selected="">UTC+0 (Coordinated Universal Time)</option>
                                <option>UTC+1 (Central European Time)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-gray-300" for="date-format">Date Format</label>
                            <select class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md border" id="date-format" name="date-format">
                                <option selected="">October 24, 2023 (F j, Y)</option>
                                <option>2023-10-24 (Y-m-d)</option>
                                <option>10/24/2023 (m/d/Y)</option>
                                <option>24/10/2023 (d/m/Y)</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2">Week Starts On</label>
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <input checked="" class="focus:ring-primary h-4 w-4 text-primary border-gray-300 dark:border-slate-600 dark:bg-slate-700" id="monday" name="week-start" type="radio"/>
                                <label class="ml-2 block text-sm text-gray-500 dark:text-gray-400" for="monday">Monday</label>
                            </div>
                            <div class="flex items-center">
                                <input class="focus:ring-primary h-4 w-4 text-primary border-gray-300 dark:border-slate-600 dark:bg-slate-700" id="sunday" name="week-start" type="radio"/>
                                <label class="ml-2 block text-sm text-gray-500 dark:text-gray-400" for="sunday">Sunday</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1 space-y-6">
            <!-- Membership -->
            <div class="bg-surface-light dark:bg-surface-dark shadow-sm rounded-lg border border-gray-200 dark:border-slate-700/60 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white">Membership</h3>
                </div>
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="flex h-5 items-center">
                            <input class="focus:ring-primary h-4 w-4 text-primary border-gray-300 dark:border-slate-600 rounded dark:bg-slate-700" id="membership" name="membership" type="checkbox"/>
                        </div>
                        <div class="ml-3 text-sm">
                            <label class="font-medium text-slate-900 dark:text-white" for="membership">Anyone can register</label>
                            <p class="text-gray-500 dark:text-gray-400 text-xs mt-1">Allow new users to sign up from the login page.</p>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-gray-100 dark:border-slate-700">
                        <label class="block text-sm font-medium text-slate-900 dark:text-white mb-1" for="role">New User Default Role</label>
                        <select class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md border" id="role" name="role">
                            <option selected="">Subscriber</option>
                            <option>Contributor</option>
                            <option>Author</option>
                            <option>Editor</option>
                            <option>Administrator</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Site Status -->
            <div class="bg-surface-light dark:bg-surface-dark shadow-sm rounded-lg border border-gray-200 dark:border-slate-700/60 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white">Site Status</h3>
                    <div class="flex items-center">
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
                <button class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-slate-600 shadow-sm text-sm font-medium rounded-md text-slate-700 dark:text-white bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary dark:focus:ring-offset-gray-900 transition-colors" type="button">
                    <span class="material-icons-round mr-2 text-lg">build</span>
                    Enable Maintenance Mode
                </button>
            </div>

            <!-- Danger Zone -->
            <div class="border border-red-200 dark:border-red-900/30 rounded-lg p-6 bg-red-50 dark:bg-red-900/10">
                <h3 class="text-base font-semibold text-red-600 dark:text-red-400 mb-2">Danger Zone</h3>
                <p class="text-sm text-red-600 dark:text-red-400/80 mb-4">
                    Be careful with these settings.
                </p>
                <button class="text-sm text-red-700 dark:text-red-400 underline hover:no-underline font-medium">Delete this Site</button>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/app.php'; ?>
