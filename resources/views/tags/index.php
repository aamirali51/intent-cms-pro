<?php $active = 'tags'; ?>
<?php $title = 'Tags - Intent CMS'; ?>
<?php ob_start(); ?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Tags Management</h1>
        <div class="flex text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">
            <a class="hover:text-primary transition-colors" href="/">Dashboard</a>
            <span class="mx-2">/</span>
            <span class="text-primary">Tags</span>
        </div>
    </div>
    <button class="inline-flex items-center justify-center gap-2 px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all">
        <span class="material-icons-round text-lg">add</span>
        Add New Tag
    </button>
</div>

<div class="bg-surface-light dark:bg-surface-dark shadow rounded-xl border border-slate-200 dark:border-slate-700/60 overflow-hidden transition-colors duration-200">
    <div class="p-4 border-b border-slate-200 dark:border-slate-700/60 flex flex-col sm:flex-row gap-4 justify-between items-center bg-gray-50/50 dark:bg-slate-800/50">
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <button class="inline-flex items-center px-3 py-2 border border-slate-200 dark:border-slate-600 shadow-sm text-sm font-medium rounded-lg text-slate-700 dark:text-gray-200 bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700/80 focus:outline-none">
                <span class="material-icons-round text-base mr-2 text-gray-400">filter_list</span>
                Filter
            </button>
            <button class="hidden sm:inline-flex items-center px-3 py-2 border border-slate-200 dark:border-slate-600 shadow-sm text-sm font-medium rounded-lg text-red-600 dark:text-red-400 bg-white dark:bg-slate-800 hover:bg-red-50 dark:hover:bg-red-900/20 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed" disabled="">
                <span class="material-icons-round text-base mr-2">delete</span>
                Bulk Delete
            </button>
        </div>
        <div class="text-sm text-gray-500 dark:text-gray-400 hidden sm:block">
            Showing <span class="font-medium text-slate-900 dark:text-white">1</span> to <span class="font-medium text-slate-900 dark:text-white">6</span> of <span class="font-medium text-slate-900 dark:text-white">24</span> results
        </div>
        <div class="relative sm:hidden w-full">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="material-icons-round text-gray-400 text-sm">search</span>
            </div>
            <input class="block w-full pl-9 pr-3 py-2 border border-slate-200 dark:border-slate-600 rounded-lg leading-5 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary text-sm" placeholder="Search tags..." type="text"/>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700/60">
            <thead class="bg-gray-50 dark:bg-slate-900/50">
                <tr>
                    <th class="px-6 py-3 text-left w-10" scope="col">
                        <div class="flex items-center">
                            <input class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded dark:bg-slate-800 dark:border-slate-600" id="select-all" type="checkbox"/>
                        </div>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider" scope="col">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider" scope="col">Slug</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden md:table-cell" scope="col">Description</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider" scope="col">Post Count</th>
                    <th class="relative px-6 py-3" scope="col"><span class="sr-only">Actions</span></th>
                </tr>
            </thead>
            <tbody class="bg-surface-light dark:bg-surface-dark divide-y divide-slate-200 dark:divide-slate-700/60">
                <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors group">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <input class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded dark:bg-slate-800 dark:border-slate-600" type="checkbox"/>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-slate-900 dark:text-white">Technology</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-mono rounded bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-300">technology</span>
                    </td>
                    <td class="px-6 py-4 hidden md:table-cell">
                        <div class="text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">Latest news and trends in tech industry</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">128</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button class="text-primary hover:text-violet-700 dark:hover:text-violet-400" title="Edit">
                                <span class="material-icons-round text-lg">edit</span>
                            </button>
                            <button class="text-gray-400 hover:text-red-600 dark:hover:text-red-400" title="Delete">
                                <span class="material-icons-round text-lg">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                 <!-- More rows -->
                 <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors group">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <input class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded dark:bg-slate-800 dark:border-slate-600" type="checkbox"/>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-slate-900 dark:text-white">Design</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-mono rounded bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-300">design</span>
                    </td>
                    <td class="px-6 py-4 hidden md:table-cell">
                        <div class="text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">UI/UX tutorials, inspiration and assets</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">84</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button class="text-primary hover:text-violet-700 dark:hover:text-violet-400" title="Edit">
                                <span class="material-icons-round text-lg">edit</span>
                            </button>
                            <button class="text-gray-400 hover:text-red-600 dark:hover:text-red-400" title="Delete">
                                <span class="material-icons-round text-lg">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/app.php'; ?>
