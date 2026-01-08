<?php $active = 'posts'; ?>
<?php $title = 'Posts List - Intent CMS'; ?>
<?php ob_start(); ?>

<div class="sm:flex sm:items-center sm:justify-between mb-8">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Posts</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-gray-400">Manage your blog content, filter by status, or create new articles.</p>
    </div>
    <div class="mt-4 sm:mt-0 flex space-x-3">
        <button class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-surface-dark hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" type="button">
            <span class="material-icons-round text-lg mr-2">file_download</span>
            Export
        </button>
        <a href="/posts/new" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
            <span class="material-icons-round text-lg mr-2">add</span>
            Add New Post
        </a>
    </div>
</div>

<!-- Filters -->
<div class="bg-surface-light dark:bg-surface-dark rounded-lg shadow-sm border border-border-light dark:border-border-dark mb-6 p-4 transition-colors duration-200">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
        <div class="md:col-span-4">
            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1" for="post-search">Search Posts</label>
            <div class="relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <span class="material-icons-round text-gray-400 text-lg">search</span>
                </div>
                <input class="focus:ring-primary focus:border-primary block w-full pl-10 sm:text-sm border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" id="post-search" placeholder="Title, slug, or summary" type="text"/>
            </div>
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1" for="category-filter">Category</label>
            <select class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" id="category-filter">
                <option>All Categories</option>
                <option>Technology</option>
                <option>Marketing</option>
                <option>Design</option>
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1" for="status-filter">Status</label>
            <select class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" id="status-filter">
                <option>Any Status</option>
                <option>Published</option>
                <option>Draft</option>
                <option>Scheduled</option>
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1" for="author-filter">Author</label>
            <select class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" id="author-filter">
                <option>All Authors</option>
                <option>John Doe</option>
                <option>Jane Smith</option>
            </select>
        </div>
        <div class="md:col-span-2 flex justify-end">
            <button class="text-sm text-primary hover:text-violet-700 font-medium flex items-center">
                <span class="material-icons-round text-sm mr-1">filter_list</span>
                More Filters
            </button>
        </div>
    </div>
</div>

<!-- Table -->
<div class="bg-surface-light dark:bg-surface-dark shadow-sm border border-border-light dark:border-border-dark rounded-lg overflow-hidden transition-colors duration-200">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider" scope="col">
                        <div class="flex items-center">
                            <input class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600" type="checkbox"/>
                        </div>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider" scope="col">Title &amp; Slug</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider" scope="col">Author</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider" scope="col">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider" scope="col">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider" scope="col">Date</th>
                    <th class="relative px-6 py-3" scope="col"><span class="sr-only">Edit</span></th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-surface-dark divide-y divide-gray-200 dark:divide-gray-700">
                 <!-- Row 1 -->
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <input class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600" type="checkbox"/>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                <span class="material-icons text-gray-400">image</span>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white line-clamp-1 max-w-xs">A Simple Guide On How To Create A High Converting Landing Page</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">/video-conversion-tips</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-8 w-8">
                                <img alt="John Doe" class="h-8 w-8 rounded-full" src="https://lh3.googleusercontent.com/aida-public/AB6AXuACgwx80QB8uDBXw3BQgYImcW3GEErmnkjW3P09pBF8TMuTjmIf8vyPkAQj5YnT2S8-BqWN4Usatp-uDXNLt6feLcM3IKYLGW6EpYDqQU7vBCLHoCjPhF6owbH3_543eC-WPuve8eb2t_nVMINQFSLWF03Oix0se-iOVgFbhkmwM81Hbz-pWYIQXmwlUkz_rLNTLJkAAb3Xm9uMpOJwCIVYYuUF45n7zc968SoXpvY-am8hdRfIBEPiqOAjUq3atEwsTXtJiwaj91c"/>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">John Doe</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300 mr-1">Marketing</span>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">Growth</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">Published</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">Oct 1, 2024</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end space-x-2">
                            <a href="/posts/new" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                <span class="material-icons-round text-lg">edit</span>
                            </a>
                            <button class="text-gray-400 hover:text-red-600 dark:text-gray-500 dark:hover:text-red-400">
                                <span class="material-icons-round text-lg">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                <!-- Row 2 -->
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <input class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600" type="checkbox"/>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg flex items-center justify-center text-indigo-500">
                                <span class="material-icons text-gray-400">code</span>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white line-clamp-1 max-w-xs">Understanding React Hooks: A Deep Dive</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">/react-hooks-guide</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-8 w-8">
                                <img alt="Sarah Connor" class="h-8 w-8 rounded-full" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCFRDyLd_kGXnFgxSR1OFaZkfkNBGSz9IBvjHEKWQ9oa0zhjIcIYZ82GzfWH6BeQhecqAeDylX2hVYSIVY5oA9H3l-9VHZHF49mlZm9Tas8pPO52lsSRwFfB_CXRenAhuzzUd2F4MYZi-RSNz0pZtlMXeEQl25qo2SxOLCeksmuDnAgTdqIMZwoYv2t-hdP2CnYrRLg925HhfDMs-WtwRcCIf8dvQeHwxz4vAKcpzoWrJVqby9yN1P6olM3h-_1PIwfyzbZ9A1jrw8"/>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">Sarah Connor</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">Development</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">Draft</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">Sep 28, 2024</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end space-x-2">
                             <button class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                <span class="material-icons-round text-lg">edit</span>
                            </button>
                            <button class="text-gray-400 hover:text-red-600 dark:text-gray-500 dark:hover:text-red-400">
                                <span class="material-icons-round text-lg">delete</span>
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
            Showing <span class="font-medium text-slate-800 dark:text-white">1</span> to <span class="font-medium text-slate-800 dark:text-white">4</span> of <span class="font-medium text-slate-800 dark:text-white">24</span> results
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
