<?php $active = 'categories'; ?>
<?php $title = 'Categories - Intent CMS'; ?>
<?php ob_start(); ?>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white tracking-tight">Categories</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage content taxonomy and hierarchies.</p>
    </div>
    <div class="flex items-center gap-3">
        <button class="inline-flex items-center px-4 py-2 bg-white dark:bg-surface-dark border border-gray-300 dark:border-slate-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary shadow-sm transition-all">
            <span class="material-icons-round text-sm mr-2">file_download</span>
            Export
        </button>
        <button class="inline-flex items-center px-4 py-2 bg-primary hover:bg-violet-700 text-white text-sm font-medium rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all shadow-indigo-500/30">
            <span class="material-icons-round text-sm mr-2">add</span>
            New Category
        </button>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <!-- Categories List -->
    <div class="xl:col-span-2 bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-slate-100 dark:border-slate-700/60 flex flex-col">
        <div class="p-5 border-b border-slate-100 dark:border-slate-700/60 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <div class="relative">
                    <select class="block w-full pl-3 pr-10 py-2 text-sm border-gray-300 dark:border-slate-600 focus:outline-none focus:ring-primary focus:border-primary rounded-lg dark:bg-slate-800 dark:text-gray-200">
                        <option>Bulk Actions</option>
                        <option>Delete Selected</option>
                        <option>Merge Categories</option>
                    </select>
                </div>
                <button class="px-3 py-2 bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-300 rounded-lg text-sm hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors">Apply</button>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500 dark:text-gray-400">Sort by:</span>
                <select class="block w-32 pl-2 pr-8 py-1.5 text-sm border-none bg-transparent font-medium text-slate-700 dark:text-slate-200 focus:ring-0 cursor-pointer">
                    <option>Newest</option>
                    <option>Name (A-Z)</option>
                    <option>Post Count</option>
                </select>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700/60">
                <thead class="bg-gray-50 dark:bg-slate-800/50">
                    <tr>
                        <th class="px-6 py-3 text-left" scope="col">
                            <input class="rounded border-gray-300 text-primary focus:ring-primary dark:border-slate-600 dark:bg-slate-700 w-4 h-4" type="checkbox"/>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider" scope="col">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden md:table-cell" scope="col">Slug</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden lg:table-cell" scope="col">Description</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider" scope="col">Count</th>
                        <th class="relative px-6 py-3" scope="col">
                            <span class="sr-only">Edit</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-surface-light dark:bg-surface-dark divide-y divide-slate-100 dark:divide-slate-700/60">
                     <!-- Row 1 -->
                    <tr class="group hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input class="rounded border-gray-300 text-primary focus:ring-primary dark:border-slate-600 dark:bg-slate-700 w-4 h-4" type="checkbox"/>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-9 w-9 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-lg flex items-center justify-center">
                                    <span class="material-icons-round text-base">web</span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-slate-700 dark:text-slate-200">Technology</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-gray-400">
                                technology
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 hidden lg:table-cell max-w-xs truncate">
                            Latest news about gadgets and software.
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-slate-700 dark:text-slate-200">
                            <span class="font-semibold">42</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button class="text-gray-400 hover:text-primary dark:hover:text-primary-dark transition-colors mr-3">
                                <span class="material-icons-round text-lg">edit</span>
                            </button>
                            <button class="text-gray-400 hover:text-red-500 transition-colors">
                                <span class="material-icons-round text-lg">delete</span>
                            </button>
                        </td>
                    </tr>
                    <!-- Row 2 -->
                    <tr class="group hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input class="rounded border-gray-300 text-primary focus:ring-primary dark:border-slate-600 dark:bg-slate-700 w-4 h-4" type="checkbox"/>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-9 w-9 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg flex items-center justify-center">
                                    <span class="material-icons-round text-base">flight_takeoff</span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-slate-700 dark:text-slate-200">Travel</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-gray-400">
                                travel-guides
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 hidden lg:table-cell max-w-xs truncate">
                            Guides for digital nomads.
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-slate-700 dark:text-slate-200">
                            <span class="font-semibold">18</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button class="text-gray-400 hover:text-primary dark:hover:text-primary-dark transition-colors mr-3">
                                <span class="material-icons-round text-lg">edit</span>
                            </button>
                            <button class="text-gray-400 hover:text-red-500 transition-colors">
                                <span class="material-icons-round text-lg">delete</span>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="bg-surface-light dark:bg-surface-dark px-4 py-3 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between sm:px-6 rounded-b-xl">
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700 dark:text-gray-400">
                        Showing <span class="font-medium text-slate-700 dark:text-slate-200">1</span> to <span class="font-medium text-slate-700 dark:text-slate-200">2</span> of <span class="font-medium text-slate-700 dark:text-slate-200">12</span> results
                    </p>
                </div>
                <div>
                    <nav aria-label="Pagination" class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                        <a class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700" href="#">
                            <span class="sr-only">Previous</span>
                            <span class="material-icons-round text-sm">chevron_left</span>
                        </a>
                        <a aria-current="page" class="z-10 bg-primary/10 border-primary text-primary relative inline-flex items-center px-4 py-2 border text-sm font-medium" href="#">1</a>
                        <a class="bg-white dark:bg-slate-800 border-gray-300 dark:border-slate-600 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700 relative inline-flex items-center px-4 py-2 border text-sm font-medium" href="#">2</a>
                        <a class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700" href="#">
                            <span class="sr-only">Next</span>
                            <span class="material-icons-round text-sm">chevron_right</span>
                        </a>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar / Quick Add -->
    <div class="xl:col-span-1 space-y-6">
        <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700/60">
            <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-4">Quick Add</h3>
            <form action="#" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="categoryName">Name</label>
                    <input class="block w-full rounded-lg border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-800 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm" id="categoryName" name="categoryName" placeholder="e.g. Design" type="text"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="categorySlug">Slug</label>
                    <div class="flex rounded-md shadow-sm">
                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 dark:border-slate-600 bg-gray-50 dark:bg-slate-700 text-gray-500 dark:text-gray-400 sm:text-sm">/</span>
                        <input class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:border-primary focus:ring-primary sm:text-sm" id="categorySlug" name="categorySlug" placeholder="design" type="text"/>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="parentCategory">Parent Category</label>
                    <select class="block w-full rounded-lg border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-800 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm" id="parentCategory" name="parentCategory">
                        <option>None (Top Level)</option>
                        <option>Technology</option>
                        <option>Travel</option>
                    </select>
                </div>
                <div class="pt-2">
                    <button class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all" type="submit">
                        Create Category
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700/60">
            <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-4">Overview</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-gray-50 dark:bg-slate-800/50 p-4 rounded-lg">
                    <div class="text-gray-500 dark:text-gray-400 text-xs font-medium uppercase tracking-wider mb-1">Total</div>
                    <div class="text-2xl font-bold text-slate-800 dark:text-white">12</div>
                    <div class="text-xs text-green-500 mt-1 font-medium flex items-center">
                        <span class="material-icons-round text-xs mr-0.5">trending_up</span> +2 this week
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-slate-800/50 p-4 rounded-lg">
                    <div class="text-gray-500 dark:text-gray-400 text-xs font-medium uppercase tracking-wider mb-1">Empty</div>
                    <div class="text-2xl font-bold text-slate-800 dark:text-white">3</div>
                    <div class="text-xs text-gray-500 mt-1">No posts yet</div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/app.php'; ?>
