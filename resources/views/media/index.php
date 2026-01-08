<?php $active = 'media'; ?>
<?php $title = 'Media Library - Intent CMS'; ?>
<?php ob_start(); ?>

<div class="flex-1 flex flex-col h-full min-w-0">
    <div class="pb-6 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Media Library</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage all your images, documents, and videos.</p>
            </div>
            <button class="bg-primary hover:bg-violet-700 text-white px-4 py-2.5 rounded-lg flex items-center shadow-sm transition-all text-sm font-medium">
                <span class="material-icons-round mr-2 text-lg">cloud_upload</span>
                Upload New
            </button>
        </div>
        
        <!-- Toolbar -->
        <div class="bg-surface-light dark:bg-surface-dark border border-slate-100 dark:border-slate-700/60 rounded-lg p-3 flex flex-wrap items-center justify-between gap-3 shadow-sm">
            <div class="flex items-center gap-3 flex-1">
                <div class="flex items-center pl-2">
                    <input class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded dark:bg-slate-700 dark:border-slate-600" type="checkbox"/>
                </div>
                <div class="relative flex-1 max-w-xs">
                    <input class="w-full pl-8 pr-3 py-1.5 text-sm border border-gray-300 dark:border-slate-600 rounded-md bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:ring-1 focus:ring-primary focus:border-primary" placeholder="Search media..." type="text"/>
                    <span class="material-icons-round absolute left-2.5 top-2 text-gray-400 text-base">search</span>
                </div>
                <select class="text-sm border-gray-300 dark:border-slate-600 rounded-md bg-white dark:bg-slate-800 text-slate-700 dark:text-gray-300 py-1.5 focus:ring-primary focus:border-primary">
                    <option>All Types</option>
                    <option>Images</option>
                    <option>Videos</option>
                    <option>Documents</option>
                </select>
            </div>
            <div class="flex items-center border border-gray-200 dark:border-slate-700 rounded-md overflow-hidden">
                <button class="p-1.5 bg-gray-100 dark:bg-slate-700 text-slate-600 dark:text-white">
                    <span class="material-icons-round text-lg">grid_view</span>
                </button>
                <button class="p-1.5 bg-white dark:bg-slate-800 text-gray-400 hover:text-slate-600 dark:hover:text-gray-200">
                    <span class="material-icons-round text-lg">view_list</span>
                </button>
            </div>
        </div>
        
        <!-- Upload Area -->
        <div class="mb-8 border-2 border-dashed border-gray-300 dark:border-slate-600 rounded-xl bg-gray-50 dark:bg-slate-800/50 flex flex-col items-center justify-center py-10 hover:border-primary hover:bg-primary/5 transition-colors cursor-pointer group">
            <div class="h-12 w-12 bg-white dark:bg-slate-700 rounded-full shadow-sm flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <span class="material-icons-round text-primary text-2xl">file_upload</span>
            </div>
            <p class="text-sm font-medium text-slate-900 dark:text-white">Click to upload or drag and drop</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">SVG, PNG, JPG or GIF (max. 800x400px)</p>
        </div>

        <!-- Media Grid -->
        <div>
            <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Recently Added</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                <!-- Item 1 -->
                <div class="group relative aspect-square bg-white dark:bg-slate-800 rounded-lg border-2 border-primary shadow-sm overflow-hidden cursor-pointer">
                    <img alt="Mountain Landscape" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAUit54QaQkrm59s5e4U-Vc70M9iJwmpQi25XhO430JhP0gC5pPrdq2qAwLhsa9t35rO-ead2lZlbaSx64Os1-2wzf6YwFiiRPPtT0D0zwyWV20xk75ViSd6VPeHYQGgTsCaqoo9ucdNppzA8BX7RqZxEX23XOlN1sPgtIOLdbUaKanhdk6fH4p09ydpxLxRobLByurIS-4K8ppFp4fBiKqq0mCS0vQcVz2rwHM4ok5nHUmcPAoXJdWgtkqWn7KgNiZ4pwy2tuNTdY"/>
                    <div class="absolute inset-0 bg-black/10 group-hover:bg-black/20 transition-colors"></div>
                    <div class="absolute top-2 left-2">
                        <input checked="" class="h-5 w-5 text-primary rounded border-gray-300 focus:ring-primary" type="checkbox"/>
                    </div>
                    <div class="absolute bottom-0 inset-x-0 bg-white dark:bg-slate-800 p-2 text-xs truncate border-t border-gray-200 dark:border-slate-700">
                        hero-mountains.jpg
                    </div>
                </div>
                 <!-- Item 2 -->
                 <div class="group relative aspect-square bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden cursor-pointer hover:border-primary transition-colors">
                    <img alt="Coding Setup" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCgi1qkdU6sYupA-Zty_bnZao0Jqzo4GVWyJQeRm3JQd_b9CJdtdbojiKnkZbEpc5tReDiHLkRjGTKo77h1X8N8SAAo4oeHyUZNcrHgjr0Qs6lMxQOUI6pSR1gZSoeABaasFz7fvIOHf46iEbej8RAbhEcZHeojutr6aP5TJcU9lna0etTptaK-v7pyl-gBmxBGEoPfWJyXA4m4db4rxEzKSEd_4GUfoMkFhIhxtFy6-euJVIAwnAqlT0FMJbswp-iF_QBXn7BFz98"/>
                    <div class="absolute bottom-0 inset-x-0 bg-white dark:bg-slate-800 p-2 text-xs truncate border-t border-gray-200 dark:border-slate-700">
                        coding-setup.jpg
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-8 flex justify-center">
            <button class="text-sm text-gray-500 hover:text-primary dark:text-gray-400 dark:hover:text-white font-medium">Load More...</button>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/app.php'; ?>
