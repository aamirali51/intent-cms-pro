<?php $active = 'dashboard'; ?>
<?php ob_start(); ?>

<div class="max-w-7xl mx-auto space-y-6">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        <!-- New Pages Card -->
        <div class="bg-surface-light dark:bg-surface-dark p-5 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700/60 relative group hover:shadow-md transition-shadow">
            <div class="flex items-start mb-4">
                <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg text-blue-500 dark:text-blue-400 mr-3">
                    <span class="material-icons-round text-xl">description</span>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-800 dark:text-slate-100 text-sm">Total Pages</h3>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-800 dark:text-white mb-1">12</p>
            <div class="flex items-center text-xs text-slate-400">
                <span class="text-green-500 font-medium flex items-center mr-1">
                    <span class="material-icons-round text-sm">arrow_upward</span> 2
                </span>
                <span>since last week</span>
            </div>
        </div>

        <!-- Categories Card -->
        <div class="bg-surface-light dark:bg-surface-dark p-5 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700/60 relative group hover:shadow-md transition-shadow">
            <div class="flex items-start mb-4">
                <div class="p-2 bg-orange-50 dark:bg-orange-900/20 rounded-lg text-orange-500 dark:text-orange-400 mr-3">
                    <span class="material-icons-round text-xl">category</span>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-800 dark:text-slate-100 text-sm">Categories</h3>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-800 dark:text-white mb-1">5</p>
             <div class="flex items-center text-xs text-slate-400">
                <span>Active categories</span>
            </div>
        </div>

        <!-- Users Card -->
        <div class="bg-surface-light dark:bg-surface-dark p-5 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700/60 relative group hover:shadow-md transition-shadow">
            <div class="flex items-start mb-4">
                <div class="p-2 bg-red-50 dark:bg-red-900/20 rounded-lg text-red-500 dark:text-red-400 mr-3">
                    <span class="material-icons-round text-xl">person</span>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-800 dark:text-slate-100 text-sm">Total Users</h3>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-800 dark:text-white mb-1">158</p>
             <div class="flex items-center text-xs text-slate-400">
                <span class="text-green-500 font-medium flex items-center mr-1">
                    <span class="material-icons-round text-sm">arrow_upward</span> 12
                </span>
                <span>since last month</span>
            </div>
        </div>

        <!-- Posts Card -->
        <div class="bg-surface-light dark:bg-surface-dark p-5 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700/60 relative group hover:shadow-md transition-shadow">
            <div class="flex items-start mb-4">
                <div class="p-2 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg text-emerald-500 dark:text-emerald-400 mr-3">
                    <span class="material-icons-round text-xl">post_add</span>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-800 dark:text-slate-100 text-sm">Published Posts</h3>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-800 dark:text-white mb-1">42</p>
            <div class="flex items-center text-xs text-slate-400">
                <span class="text-emerald-500 font-medium flex items-center mr-1">
                    <span class="material-icons-round text-sm">check_circle</span>
                </span>
                <span>All systems operational</span>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Main Chart -->
        <div class="xl:col-span-2 bg-surface-light dark:bg-surface-dark p-6 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700/60">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-slate-800 dark:text-white mb-2 sm:mb-0">Traffic Overview</h2>
            </div>
            <!-- Mock Chart Area -->
            <div class="h-64 w-full bg-slate-50 dark:bg-slate-800/50 rounded-lg flex items-center justify-center border border-dashed border-slate-200 dark:border-slate-700">
                <p class="text-slate-400 dark:text-slate-500 text-sm">Chart Visualization Placeholder</p>
            </div>
        </div>

        <!-- Stats Panel -->
        <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700/60 flex flex-col">
            <h2 class="text-lg font-bold text-slate-800 dark:text-white mb-6">System Health</h2>
             
            <div class="space-y-6">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-slate-600 dark:text-slate-400">CPU Usage</span>
                        <span class="font-bold text-slate-800 dark:text-white">12%</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2">
                        <div class="bg-primary h-2 rounded-full" style="width: 12%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-slate-600 dark:text-slate-400">Memory</span>
                        <span class="font-bold text-slate-800 dark:text-white">45%</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2">
                        <div class="bg-emerald-500 h-2 rounded-full" style="width: 45%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-slate-600 dark:text-slate-400">Disk Space</span>
                        <span class="font-bold text-slate-800 dark:text-white">28%</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2">
                        <div class="bg-orange-500 h-2 rounded-full" style="width: 28%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>

<?php include BASE_PATH . '/resources/views/layouts/app.php'; ?>
