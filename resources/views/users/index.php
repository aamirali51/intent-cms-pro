<?php $active = 'users'; ?>
<?php $title = 'Users - Intent CMS'; ?>
<?php ob_start(); ?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">User Management</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage your team members and their permissions.</p>
    </div>
    <div class="flex space-x-3">
        <button class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg shadow-sm text-sm font-medium text-slate-700 dark:text-gray-200 bg-white dark:bg-surface-dark hover:bg-gray-50 dark:hover:bg-slate-700 focus:outline-none transition-colors">
            <span class="material-icons-round mr-2 text-base">download</span>
            Export
        </button>
        <button class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
            <span class="material-icons-round mr-2 text-base">add</span>
            Add New User
        </button>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700/60 flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Users</p>
            <p class="text-3xl font-bold text-slate-800 dark:text-white mt-1">2,543</p>
            <span class="text-xs text-green-500 font-medium flex items-center mt-2">
                <span class="material-icons-round text-sm mr-1">trending_up</span> +12% this month
            </span>
        </div>
        <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
            <span class="material-icons-round text-blue-500 text-2xl">group</span>
        </div>
    </div>
    <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700/60 flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Now</p>
            <p class="text-3xl font-bold text-slate-800 dark:text-white mt-1">126</p>
            <span class="text-xs text-green-500 font-medium flex items-center mt-2">
                <span class="material-icons-round text-sm mr-1">fiber_manual_record</span> Online
            </span>
        </div>
        <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
            <span class="material-icons-round text-green-500 text-2xl">wifi</span>
        </div>
    </div>
    <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700/60 flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Approvals</p>
            <p class="text-3xl font-bold text-slate-800 dark:text-white mt-1">8</p>
            <span class="text-xs text-orange-500 font-medium flex items-center mt-2">
                <span class="material-icons-round text-sm mr-1">priority_high</span> Action needed
            </span>
        </div>
        <div class="p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
            <span class="material-icons-round text-orange-500 text-2xl">hourglass_empty</span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <!-- Users Table -->
    <div class="xl:col-span-2 bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-slate-100 dark:border-slate-700/60 overflow-hidden">
        <div class="p-6 border-b border-slate-100 dark:border-slate-700/60 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-800 dark:text-white">All Users</h2>
            <div class="flex items-center space-x-2">
                <button class="p-1.5 rounded text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                    <span class="material-icons-round">filter_list</span>
                </button>
                <button class="p-1.5 rounded text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                    <span class="material-icons-round">more_vert</span>
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-slate-700/60">
                <thead class="bg-gray-50 dark:bg-slate-800/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider" scope="col">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider" scope="col">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider" scope="col">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider" scope="col">Last Active</th>
                        <th class="relative px-6 py-3" scope="col"><span class="sr-only">Edit</span></th>
                    </tr>
                </thead>
                <tbody class="bg-surface-light dark:bg-surface-dark divide-y divide-gray-100 dark:divide-slate-700/60">
                    <!-- User Row 1 -->
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 relative">
                                    <img alt="Jane Cooper" class="h-10 w-10 rounded-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBBn3S0ZptqdooePe-nv_pX5m5eN834wa_fcSLPNv3L8O9f5-TZcmXr3rLeYU0aVM1Uq5L05T01kILIZBST5I8dkKbDF8ZAglJIzt16_gGYPJT-qwsfQ-n1bbNDddkPrIydmQu0uJmDW9X1UOE0Wvhjli2wVFi73KUWCM3Z7T7sS4fPmGWezkI5fkFQhAby_yj3zow3sJ8XpK5bmd_dWgLI01vB2KORmLBUwFIXyB2EHbZmbUIWrgrfX7brGy375wswGsD3UmNXyYQ"/>
                                    <span class="absolute bottom-0 right-0 block h-2.5 w-2.5 rounded-full ring-2 ring-white dark:ring-gray-900 bg-green-400"></span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-slate-800 dark:text-white">Jane Cooper</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">jane.cooper@example.com</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300">Admin</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Active</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">Just now</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button class="text-primary hover:text-violet-700">Edit</button>
                        </td>
                    </tr>
                     <!-- User Row 2 -->
                     <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 relative">
                                    <img alt="Cody Fisher" class="h-10 w-10 rounded-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBhJto3D4S7rMJD5ohSrKIjtc7ejxchJrr86FSBca5GURdfutqQsgJ4fMHQXHWn0r1G2ssDlA1aNhBm1ef6ONM6lQpzmwgzFfaPuXF2XFGIXZksGhs3tL0aOHpFZ8s6vd_dhnPdezvLiwXmT2Ye4revwsTqULcwNity_o3Wt3_-EoABp9JwBhMRc1RouoEyYLhEfvdt5snyiteGlSvmNU3ia737z_p7KcBscn5h003Ztv_fMbonB-hcnIgnHiVRKRE2YFHcMVh75EA"/>
                                    <span class="absolute bottom-0 right-0 block h-2.5 w-2.5 rounded-full ring-2 ring-white dark:ring-gray-900 bg-gray-300"></span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-slate-800 dark:text-white">Cody Fisher</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">cody.fisher@example.com</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">Author</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Away</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">25 mins ago</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button class="text-primary hover:text-violet-700">Edit</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="bg-surface-light dark:bg-surface-dark px-4 py-3 border-t border-gray-100 dark:border-slate-700/60 sm:px-6">
            <div class="flex items-center justify-between">
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Showing <span class="font-medium text-slate-800 dark:text-white">1</span> to <span class="font-medium text-slate-800 dark:text-white">10</span> of <span class="font-medium text-slate-800 dark:text-white">97</span> results
                        </p>
                    </div>
                    <div>
                        <nav aria-label="Pagination" class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                            <a class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700" href="#">
                                <span class="sr-only">Previous</span>
                                <span class="material-icons-round text-lg">chevron_left</span>
                            </a>
                            <a aria-current="page" class="z-10 bg-primary/10 border-primary text-primary relative inline-flex items-center px-4 py-2 border text-sm font-medium" href="#">1</a>
                            <a class="bg-white dark:bg-slate-800 border-gray-300 dark:border-slate-600 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700 relative inline-flex items-center px-4 py-2 border text-sm font-medium" href="#">2</a>
                            <a class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700" href="#">
                                <span class="sr-only">Next</span>
                                <span class="material-icons-round text-lg">chevron_right</span>
                            </a>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Add User Form -->
    <div class="space-y-6">
        <div class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-slate-100 dark:border-slate-700/60 p-6">
            <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-4">Quick Add User</h3>
            <form action="#" class="space-y-4" method="POST">
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400" for="name">Full Name</label>
                    <input class="mt-1 block w-full rounded-md border-gray-300 dark:border-slate-600 shadow-sm focus:border-primary focus:ring-primary sm:text-sm bg-gray-50 dark:bg-slate-800 text-slate-800 dark:text-white" id="name" name="name" placeholder="John Doe" type="text"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400" for="email">Email Address</label>
                    <input class="mt-1 block w-full rounded-md border-gray-300 dark:border-slate-600 shadow-sm focus:border-primary focus:ring-primary sm:text-sm bg-gray-50 dark:bg-slate-800 text-slate-800 dark:text-white" id="email" name="email" placeholder="john@example.com" type="email"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400" for="role">Role</label>
                    <select class="mt-1 block w-full rounded-md border-gray-300 dark:border-slate-600 shadow-sm focus:border-primary focus:ring-primary sm:text-sm bg-gray-50 dark:bg-slate-800 text-slate-800 dark:text-white" id="role" name="role">
                        <option>Contributor</option>
                        <option>Author</option>
                        <option>Editor</option>
                        <option>Admin</option>
                    </select>
                </div>
                <div class="flex items-center">
                    <input checked="" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded" id="notify" name="notify" type="checkbox"/>
                    <label class="ml-2 block text-sm text-gray-500 dark:text-gray-400" for="notify">
                        Send invite via email
                    </label>
                </div>
                <button class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors" type="submit">
                    Send Invite
                </button>
            </form>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/app.php'; ?>
