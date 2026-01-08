<?php $active = 'pages'; ?>
<?php $title = 'Pages List - Intent CMS'; ?>
<?php ob_start(); ?>

<div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">Pages</h2>
        <nav class="flex text-sm text-gray-500 dark:text-gray-400">
            <a class="hover:text-primary" href="/">Dashboard</a>
            <span class="mx-2">/</span>
            <span class="text-gray-800 dark:text-gray-200">Pages List</span>
        </nav>
    </div>
    <div class="mt-4 md:mt-0 flex gap-3">
        <button class="px-4 py-2 bg-white dark:bg-surface-dark border border-border-light dark:border-border-dark text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-sm font-medium flex items-center transition-colors">
            <span class="material-icons-round text-base mr-2">file_download</span>
            Export
        </button>
        <button class="px-4 py-2 bg-primary hover:bg-primaryHover text-white rounded-lg shadow-lg shadow-indigo-500/20 text-sm font-medium flex items-center transition-colors">
            <span class="material-icons-round text-base mr-2">add</span>
            Add New Page
        </button>
    </div>
</div>

<div class="bg-surface-light dark:bg-surface-dark rounded-t-xl p-4 border-b border-border-light dark:border-border-dark flex flex-col md:flex-row justify-between items-center gap-4 transition-colors duration-200">
    <div class="flex space-x-1 bg-gray-100 dark:bg-gray-800 p-1 rounded-lg">
        <button class="px-4 py-1.5 text-sm font-medium rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm transition-all">All <span class="ml-1 text-xs text-gray-400">(24)</span></button>
        <button class="px-4 py-1.5 text-sm font-medium rounded-md text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-all">Published <span class="ml-1 text-xs text-gray-400">(18)</span></button>
        <button class="px-4 py-1.5 text-sm font-medium rounded-md text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-all">Drafts <span class="ml-1 text-xs text-gray-400">(4)</span></button>
    </div>
    <div class="flex w-full md:w-auto gap-3">
        <div class="relative flex-1 md:w-64">
            <span class="material-icons-round absolute left-3 top-2.5 text-gray-400 text-lg">search</span>
            <input class="w-full pl-9 pr-4 py-2 text-sm bg-gray-50 dark:bg-gray-800 border border-border-light dark:border-border-dark rounded-lg text-gray-900 dark:text-gray-200 focus:ring-1 focus:ring-primary focus:border-primary" placeholder="Search pages..." type="text"/>
        </div>
        <button class="px-3 py-2 bg-white dark:bg-surface-dark border border-border-light dark:border-border-dark rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700">
            <span class="material-icons-round text-lg">filter_list</span>
        </button>
    </div>
</div>

<div class="bg-surface-light dark:bg-surface-dark rounded-b-xl shadow-sm overflow-hidden transition-colors duration-200">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-gray-50 dark:bg-gray-800/50 uppercase tracking-wider border-b border-border-light dark:border-border-dark text-gray-500 dark:text-gray-400 font-medium">
                <tr>
                    <th class="px-6 py-4 w-12" scope="col">
                        <input class="rounded border-gray-300 text-primary focus:ring-primary bg-gray-50 dark:bg-gray-700 dark:border-gray-600" type="checkbox"/>
                    </th>
                    <th class="px-6 py-4" scope="col">Page Title</th>
                    <th class="px-6 py-4" scope="col">Author</th>
                    <th class="px-6 py-4" scope="col">Status</th>
                    <th class="px-6 py-4" scope="col">Date</th>
                    <th class="px-6 py-4 text-right" scope="col">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border-light dark:divide-border-dark">
                <!-- Example Row 1 -->
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group">
                    <td class="px-6 py-4">
                        <input class="rounded border-gray-300 text-primary focus:ring-primary bg-gray-50 dark:bg-gray-700 dark:border-gray-600" type="checkbox"/>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-semibold text-gray-900 dark:text-white">About Us</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">/about-company</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <img alt="Author" class="h-6 w-6 rounded-full object-cover mr-2" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDntOd9ir2gfndbmuWpxQdyBQNo17vTWJnyhG07g0XUIJ9unnyJ-uGe548P9g8ncoRdj3d_2ui58STPUDHlNiAsBaCZxwbrid-6r0NwMmv9_fNR59w4JjJqLPghCbv0g4ZNAGXp6lQhJvC7lOxxUVrW0pl2TkhflOVEBLhkEbarCrPLwLXiKGCiLG5CjW0sAXHrSqKYA3uXKAkE6l6UG0mun69JuvnkqO5hg2he72SLYHWddbWPfDWlvtP7_HsYCOrwGy87RmjVaQw"/>
                            <span class="text-gray-700 dark:text-gray-300">Alesia K.</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                            Published
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-500 dark:text-gray-400">Oct 24, 2023</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button class="p-1 text-gray-400 hover:text-primary transition-colors" title="Edit">
                                <span class="material-icons-round text-lg">edit</span>
                            </button>
                            <button class="p-1 text-gray-400 hover:text-red-500 transition-colors" title="Delete">
                                <span class="material-icons-round text-lg">delete</span>
                            </button>
                            <button class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" title="View">
                                <span class="material-icons-round text-lg">visibility</span>
                            </button>
                        </div>
                    </td>
                </tr>
                 <!-- Example Row 2 -->
                 <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group">
                    <td class="px-6 py-4">
                        <input class="rounded border-gray-300 text-primary focus:ring-primary bg-gray-50 dark:bg-gray-700 dark:border-gray-600" type="checkbox"/>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-semibold text-gray-900 dark:text-white">Contact Support</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">/contact-us</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <img alt="Author" class="h-6 w-6 rounded-full object-cover mr-2" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDkY9DmH_TyTx2Hx06qbOnFr3qtUjgtDHrkJ1sBWiI86ue6aADGkZ8mr6-2lbsL-dvlrH_fnKnhB4LoIm37I7jcED1ta0gv6jlnuxRV858wJ1B1wFqqTbQqY8i4tsCe6z1WG96BQW-QytfvqCmvcRJW_rvxudA1Xxih4n-J17g0TiEWT8CVJuXThiaDAxJ7RUwvqCr8zksKN1gXK6HzyXO4kCv8kgkGHWPdEXql7VdI4myzuYb-Cluf8fGfPTD-VDAx2v3SYC19nMc"/>
                            <span class="text-gray-700 dark:text-gray-300">Josh D.</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                            Published
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-500 dark:text-gray-400">Oct 22, 2023</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button class="p-1 text-gray-400 hover:text-primary transition-colors">
                                <span class="material-icons-round text-lg">edit</span>
                            </button>
                            <button class="p-1 text-gray-400 hover:text-red-500 transition-colors">
                                <span class="material-icons-round text-lg">delete</span>
                            </button>
                            <button class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <span class="material-icons-round text-lg">visibility</span>
                            </button>
                        </div>
                    </td>
                </tr>
                 <!-- Example Row 3 -->
                 <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group">
                    <td class="px-6 py-4">
                        <input class="rounded border-gray-300 text-primary focus:ring-primary bg-gray-50 dark:bg-gray-700 dark:border-gray-600" type="checkbox"/>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-semibold text-gray-900 dark:text-white">Privacy Policy</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">/privacy</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="h-6 w-6 rounded-full bg-primary flex items-center justify-center text-xs text-white mr-2">M</div>
                            <span class="text-gray-700 dark:text-gray-300">System</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                            Draft
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-500 dark:text-gray-400">Oct 15, 2023</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button class="p-1 text-gray-400 hover:text-primary transition-colors">
                                <span class="material-icons-round text-lg">edit</span>
                            </button>
                            <button class="p-1 text-gray-400 hover:text-red-500 transition-colors">
                                <span class="material-icons-round text-lg">delete</span>
                            </button>
                            <button class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <span class="material-icons-round text-lg">visibility</span>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="bg-surface-light dark:bg-surface-dark px-6 py-4 border-t border-border-light dark:border-border-dark flex items-center justify-between transition-colors duration-200">
        <span class="text-sm text-gray-500 dark:text-gray-400">
            Showing <span class="font-medium text-gray-900 dark:text-white">1</span> to <span class="font-medium text-gray-900 dark:text-white">3</span> of <span class="font-medium text-gray-900 dark:text-white">24</span> results
        </span>
        <div class="flex space-x-2">
            <button class="px-3 py-1 rounded-md border border-border-light dark:border-border-dark text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50" disabled="">
                Previous
            </button>
            <button class="px-3 py-1 rounded-md border border-border-light dark:border-border-dark text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700">
                Next
            </button>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/app.php'; ?>
