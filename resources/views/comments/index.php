<?php $active = 'comments'; ?>
<?php $title = 'Comments - Intent CMS'; ?>
<?php ob_start(); ?>

<div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Comments</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage and moderate user comments.</p>
    </div>
    <div class="mt-4 md:mt-0 flex space-x-3">
        <button class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-md shadow-sm text-sm font-medium text-slate-700 dark:text-white bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
            <span class="material-icons-round mr-2 text-lg">settings</span>
            Settings
        </button>
        <button class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
            <span class="material-icons-round mr-2 text-lg">delete_sweep</span>
            Empty Trash
        </button>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-surface-light dark:bg-surface-dark overflow-hidden rounded-lg shadow-sm border border-slate-100 dark:border-slate-700/60 p-4 flex items-center transition-colors">
        <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 mr-4">
            <span class="material-icons-round">all_inclusive</span>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Comments</p>
            <p class="text-2xl font-semibold text-slate-800 dark:text-white">12,450</p>
        </div>
    </div>
    <div class="bg-surface-light dark:bg-surface-dark overflow-hidden rounded-lg shadow-sm border border-slate-100 dark:border-slate-700/60 p-4 flex items-center transition-colors">
        <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900/40 text-yellow-600 dark:text-yellow-400 mr-4">
            <span class="material-icons-round">pending_actions</span>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending</p>
            <p class="text-2xl font-semibold text-slate-800 dark:text-white">45</p>
        </div>
    </div>
    <div class="bg-surface-light dark:bg-surface-dark overflow-hidden rounded-lg shadow-sm border border-slate-100 dark:border-slate-700/60 p-4 flex items-center transition-colors">
        <div class="p-3 rounded-full bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-400 mr-4">
            <span class="material-icons-round">check_circle</span>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Approved</p>
            <p class="text-2xl font-semibold text-slate-800 dark:text-white">12,201</p>
        </div>
    </div>
    <div class="bg-surface-light dark:bg-surface-dark overflow-hidden rounded-lg shadow-sm border border-slate-100 dark:border-slate-700/60 p-4 flex items-center transition-colors">
        <div class="p-3 rounded-full bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400 mr-4">
            <span class="material-icons-round">report_problem</span>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Spam</p>
            <p class="text-2xl font-semibold text-slate-800 dark:text-white">204</p>
        </div>
    </div>
</div>

<div class="bg-surface-light dark:bg-surface-dark shadow-sm rounded-lg border border-slate-100 dark:border-slate-700/60 flex flex-col transition-colors">
    <div class="p-4 border-b border-slate-100 dark:border-slate-700/60 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex space-x-1 overflow-x-auto no-scrollbar">
            <button class="px-3 py-2 text-sm font-medium text-primary bg-primary/10 rounded-md whitespace-nowrap">All (12,450)</button>
            <button class="px-3 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-slate-800 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-slate-700/50 rounded-md whitespace-nowrap transition-colors">Pending (45)</button>
            <button class="px-3 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-slate-800 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-slate-700/50 rounded-md whitespace-nowrap transition-colors">Approved (12,201)</button>
            <button class="px-3 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-slate-800 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-slate-700/50 rounded-md whitespace-nowrap transition-colors">Spam (204)</button>
            <button class="px-3 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-slate-800 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-slate-700/50 rounded-md whitespace-nowrap transition-colors">Trash (12)</button>
        </div>
        <div class="flex items-center space-x-2">
            <select class="block w-full sm:w-auto pl-3 pr-10 py-2 text-base border-gray-300 dark:border-slate-600 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md bg-white dark:bg-slate-700 text-slate-800 dark:text-white transition-colors">
                <option>Bulk Actions</option>
                <option>Approve</option>
                <option>Unapprove</option>
                <option>Mark as Spam</option>
                <option>Move to Trash</option>
            </select>
            <button class="px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-primary bg-primary/10 hover:bg-primary/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                Apply
            </button>
            <div class="relative rounded-md shadow-sm ml-2">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <span class="material-icons-round text-gray-400 text-sm">search</span>
                </div>
                <input class="focus:ring-primary focus:border-primary block w-full pl-10 sm:text-sm border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-md text-slate-800 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-colors" placeholder="Search comments" type="text"/>
            </div>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700/60">
            <thead class="bg-gray-50 dark:bg-slate-800/50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-10" scope="col">
                        <input class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded dark:bg-slate-700 dark:border-slate-600 accent-primary" type="checkbox"/>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider" scope="col">Author</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-1/3" scope="col">Comment</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider" scope="col">In Response To</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider" scope="col">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider" scope="col">Submitted On</th>
                </tr>
            </thead>
            <tbody class="bg-surface-light dark:bg-surface-dark divide-y divide-slate-100 dark:divide-slate-700/60">
                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30 transition-colors group">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <input class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded dark:bg-slate-700 dark:border-slate-600 accent-primary" type="checkbox"/>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <img alt="" class="h-10 w-10 rounded-full" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDsxAJKypmRUIkVUq264hL3tGleE3fHzhPvAllP9Qv6v38l9og9KbJpD_yckAITFC5w4wPMwG8lKmOxp9g4kufg0_qj0kCV5BOftJigOuFlvJngtzuBIKHTHZn1vHYF9mvQeE3WUhNZC3zIyJei-08LXQ0gobuK5uQUsd3e2tmM6EuDiBjjmX4EvBP5WfOwbyykc9zLwnYTqBn6elnvQQUucANofu5RQpdcSH-JpMDcnztIXFUBBU-PHhzjaf084KJh2V9p1w0rUvY"/>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-slate-800 dark:text-white">Michael Foster</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">michael@example.com</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">192.168.1.1</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-slate-800 dark:text-white line-clamp-2">
                            Great article on landing pages! I've been struggling with conversion rates for months. One question: how often do you recommend A/B testing the headline?
                        </div>
                        <div class="mt-2 flex space-x-2 opacity-0 group-hover:opacity-100 transition-opacity text-xs font-medium">
                            <button class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 hover:underline">Approve</button>
                            <span class="text-gray-300 dark:text-slate-600">|</span>
                            <button class="text-primary hover:text-primary/80 hover:underline">Reply</button>
                            <span class="text-gray-300 dark:text-slate-600">|</span>
                            <button class="text-gray-500 dark:text-gray-400 hover:text-slate-800 dark:hover:text-white hover:underline">Edit</button>
                            <span class="text-gray-300 dark:text-slate-600">|</span>
                            <button class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 hover:underline">Spam</button>
                            <span class="text-gray-300 dark:text-slate-600">|</span>
                            <button class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 hover:underline">Trash</button>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-primary hover:underline cursor-pointer">A Simple Guide On How To Create A High...</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 flex items-center">
                            <span class="bg-gray-100 dark:bg-slate-700/50 px-1.5 py-0.5 rounded text-[10px] mr-1">24</span> comments
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300 border border-yellow-200 dark:border-yellow-700/50">
                            Pending
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        Oct 24, 2024<br/>10:45 AM
                    </td>
                </tr>
                 <!-- More rows would go here, simplified for brevity -->
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
     <div class="bg-surface-light dark:bg-surface-dark px-4 py-3 flex items-center justify-between border-t border-slate-100 dark:border-slate-700/60 sm:px-6 rounded-b-lg">
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Showing <span class="font-medium text-slate-800 dark:text-white">1</span> to <span class="font-medium text-slate-800 dark:text-white">4</span> of <span class="font-medium text-slate-800 dark:text-white">12,450</span> results
                </p>
            </div>
            <div>
                <nav aria-label="Pagination" class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                    <a class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm font-medium text-gray-500 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors" href="#">
                        <span class="sr-only">Previous</span>
                        <span class="material-icons-round text-sm">chevron_left</span>
                    </a>
                    <a aria-current="page" class="z-10 bg-primary/10 border-primary text-primary relative inline-flex items-center px-4 py-2 border text-sm font-medium" href="#">1</a>
                    <a class="bg-white dark:bg-slate-800 border-gray-300 dark:border-slate-600 text-gray-500 hover:bg-gray-50 dark:hover:bg-slate-700 relative inline-flex items-center px-4 py-2 border text-sm font-medium transition-colors" href="#">2</a>
                    <a class="bg-white dark:bg-slate-800 border-gray-300 dark:border-slate-600 text-gray-500 hover:bg-gray-50 dark:hover:bg-slate-700 hidden md:inline-flex relative items-center px-4 py-2 border text-sm font-medium transition-colors" href="#">3</a>
                    <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm font-medium text-gray-700 dark:text-gray-300 transition-colors">...</span>
                    <a class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm font-medium text-gray-500 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors" href="#">
                        <span class="sr-only">Next</span>
                        <span class="material-icons-round text-sm">chevron_right</span>
                    </a>
                </nav>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/app.php'; ?>
