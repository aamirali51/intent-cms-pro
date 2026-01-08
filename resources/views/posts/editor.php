<?php $active = 'posts'; ?>
<?php $title = 'Edit Post - Intent CMS'; ?>
<?php ob_start(); ?>

<div class="flex flex-col h-full">
    <!-- Action Toolbar (Moved from Header) -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
            <a class="hover:text-primary transition-colors" href="/posts">Posts</a>
            <span class="material-icons-round mx-2 text-xs">chevron_right</span>
            <span class="text-slate-900 dark:text-white font-medium">Article Page</span>
        </div>
        <div class="flex items-center gap-3">
             <a href="/posts" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg transition-colors">
                Cancel
            </a>
            <button class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-violet-700 rounded-lg shadow-sm transition-colors flex items-center gap-2">
                <span class="material-icons-round text-sm">send</span>
                Publish
            </button>
        </div>
    </div>

    <!-- Main Editor -->
    <form class="grid grid-cols-1 lg:grid-cols-12 gap-6" onsubmit="event.preventDefault()">
        <div class="lg:col-span-8 space-y-6">
            <div class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700/60 p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Title</label>
                    <input class="block w-full text-2xl font-bold border-gray-300 dark:border-slate-600 rounded-lg shadow-sm focus:ring-primary focus:border-primary bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-gray-400 p-2.5" type="text" value="A Simple Guide On How To Create A High Converting Landing Page"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Slug</label>
                    <div class="flex rounded-md shadow-sm">
                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 dark:border-slate-600 bg-gray-50 dark:bg-slate-700 text-gray-500 dark:text-gray-400 sm:text-sm">
                            intentcms.com/blog/
                        </span>
                        <input class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md focus:ring-primary focus:border-primary sm:text-sm border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white" type="text" value="video-conversion-tips"/>
                    </div>
                </div>
            </div>

            <div class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700/60 flex flex-col min-h-[600px]">
                <div class="border-b border-slate-200 dark:border-slate-700/60 p-2 flex flex-wrap gap-1 items-center bg-gray-50 dark:bg-slate-800/50 rounded-t-xl sticky top-0 z-10">
                    <select class="text-sm border-none bg-transparent focus:ring-0 text-slate-700 dark:text-gray-200 font-medium cursor-pointer">
                        <option>Normal Text</option>
                        <option>Heading 1</option>
                        <option>Heading 2</option>
                        <option>Heading 3</option>
                    </select>
                    <div class="w-px h-5 bg-gray-300 dark:bg-slate-600 mx-2"></div>
                    <button type="button" class="p-1.5 rounded hover:bg-gray-200 dark:hover:bg-slate-700 text-slate-600 dark:text-gray-300"><span class="material-icons-round text-lg">format_bold</span></button>
                    <button type="button" class="p-1.5 rounded hover:bg-gray-200 dark:hover:bg-slate-700 text-slate-600 dark:text-gray-300"><span class="material-icons-round text-lg">format_italic</span></button>
                    <button type="button" class="p-1.5 rounded hover:bg-gray-200 dark:hover:bg-slate-700 text-slate-600 dark:text-gray-300"><span class="material-icons-round text-lg">format_underlined</span></button>
                    <div class="w-px h-5 bg-gray-300 dark:bg-slate-600 mx-2"></div>
                    <button type="button" class="p-1.5 rounded hover:bg-gray-200 dark:hover:bg-slate-700 text-slate-600 dark:text-gray-300"><span class="material-icons-round text-lg">link</span></button>
                    <button type="button" class="p-1.5 rounded hover:bg-gray-200 dark:hover:bg-slate-700 text-slate-600 dark:text-gray-300"><span class="material-icons-round text-lg">format_quote</span></button>
                    <button type="button" class="p-1.5 rounded hover:bg-gray-200 dark:hover:bg-slate-700 text-slate-600 dark:text-gray-300"><span class="material-icons-round text-lg">code</span></button>
                    <div class="w-px h-5 bg-gray-300 dark:bg-slate-600 mx-2"></div>
                    <button type="button" class="p-1.5 rounded hover:bg-gray-200 dark:hover:bg-slate-700 text-slate-600 dark:text-gray-300"><span class="material-icons-round text-lg">format_list_bulleted</span></button>
                    <button type="button" class="p-1.5 rounded hover:bg-gray-200 dark:hover:bg-slate-700 text-slate-600 dark:text-gray-300"><span class="material-icons-round text-lg">format_list_numbered</span></button>
                    <div class="w-px h-5 bg-gray-300 dark:bg-slate-600 mx-2"></div>
                    <button type="button" class="p-1.5 rounded hover:bg-gray-200 dark:hover:bg-slate-700 text-slate-600 dark:text-gray-300"><span class="material-icons-round text-lg">image</span></button>
                    <button type="button" class="ml-auto flex items-center gap-1 px-2 py-1 text-xs font-medium text-gray-500 border border-gray-300 dark:border-slate-600 rounded">
                        <span class="material-icons-round text-sm">fullscreen</span> Expand
                    </button>
                </div>
                <div class="p-6 lg:p-10 prose dark:prose-invert max-w-none editor-content">
                    <p class="text-gray-600 dark:text-gray-300 text-lg">
                        Certainly! In <strong>2024, as a marketer, embracing</strong> the power of video is crucial due to its unparalleled ability to engage and captivate audiences. Video content continues to dominate social media platforms, search engines, and various digital channels. It allows marketers to convey messages in a highly visual and compelling manner.
                    </p>
                    <p class="text-gray-600 dark:text-gray-300">
                        Moreover, the rise of platforms like <a class="text-primary underline" href="#">TikTok</a>, <a class="text-primary underline" href="#">Instagram Reels</a>, and YouTube Shorts underscores the growing preference for short-form video content, which is easily consumable and shareable.
                    </p>
                    <!-- Embedded Card Example -->
                    <div class="my-8 rounded-xl overflow-hidden shadow-lg relative bg-indigo-900 group cursor-pointer">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-900 to-indigo-800 opacity-90"></div>
                        <div class="relative z-10 p-12 flex flex-col items-center justify-center text-center">
                            <h2 class="text-6xl font-black text-orange-400 tracking-tight mb-2 font-display">50% OFF</h2>
                            <h3 class="text-3xl font-bold text-white uppercase tracking-widest mb-8">Landerlab 1st Month</h3>
                            <div class="bg-white rounded-lg shadow-2xl w-3/4 h-32 opacity-90 transform translate-y-4">
                                <div class="h-4 bg-gray-100 border-b flex items-center px-2 gap-1">
                                    <div class="w-2 h-2 rounded-full bg-red-400"></div>
                                    <div class="w-2 h-2 rounded-full bg-yellow-400"></div>
                                    <div class="w-2 h-2 rounded-full bg-green-400"></div>
                                </div>
                                <div class="p-4 grid grid-cols-3 gap-2">
                                    <div class="col-span-1 h-20 bg-blue-50 rounded"></div>
                                    <div class="col-span-2 h-20 bg-gray-50 rounded flex items-center justify-center text-gray-300 text-xs">Landing Page UI</div>
                                </div>
                            </div>
                        </div>
                        <div class="absolute inset-0 bg-black/40 hidden group-hover:flex items-center justify-center gap-3 transition-opacity">
                            <button class="bg-white text-slate-900 px-3 py-1.5 rounded text-sm font-medium shadow hover:bg-gray-100">Edit</button>
                            <button class="bg-red-500 text-white px-3 py-1.5 rounded text-sm font-medium shadow hover:bg-red-600">Delete</button>
                        </div>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300">
                        Furthermore, the advancements in technology, such as augmented reality (AR) and virtual reality (VR), are pushing the boundaries of interactive video experiences, offering innovative ways for brands to connect.
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700/60 p-5">
                <label class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">Excerpt</label>
                <p class="text-xs text-gray-500 mb-2">Add a short excerpt to summarize this post.</p>
                <textarea class="block w-full rounded-lg border-gray-300 dark:border-slate-600 shadow-sm focus:border-primary focus:ring-primary sm:text-sm bg-white dark:bg-slate-800 text-slate-900 dark:text-white p-3" placeholder="In 2024, video's impact on marketing is undeniable..." rows="4"></textarea>
            </div>
            
            <div class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700/60 p-5 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">Category</label>
                    <select class="block w-full rounded-lg border-gray-300 dark:border-slate-600 shadow-sm focus:border-primary focus:ring-primary sm:text-sm bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
                        <option>Select a category...</option>
                        <option selected="">Marketing</option>
                        <option>Development</option>
                    </select>
                    <div class="flex flex-wrap gap-2 mt-3">
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                            Health <button type="button" class="hover:text-indigo-900"><span class="material-icons-round text-xs">close</span></button>
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                            Gym <button type="button" class="hover:text-indigo-900"><span class="material-icons-round text-xs">close</span></button>
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-slate-700 dark:text-gray-300">
                            High-Converting <button type="button" class="hover:text-gray-900"><span class="material-icons-round text-xs">close</span></button>
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700/60 p-5">
                <label class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">Publish Date</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="material-icons-round text-gray-500">calendar_today</span>
                    </div>
                    <input class="block w-full pl-10 rounded-lg border-gray-300 dark:border-slate-600 shadow-sm focus:border-primary focus:ring-primary sm:text-sm bg-white dark:bg-slate-800 text-slate-900 dark:text-white" type="date" value="2024-10-01"/>
                </div>
            </div>

            <div class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700/60 p-5">
                <label class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">Cover Image</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-slate-600 border-dashed rounded-lg hover:border-primary dark:hover:border-primary transition-colors cursor-pointer group bg-gray-50 dark:bg-slate-800/50">
                    <div class="space-y-1 text-center">
                        <span class="material-icons-round text-4xl text-gray-400 group-hover:text-primary transition-colors">image</span>
                        <div class="flex text-sm text-gray-600 dark:text-gray-400 justify-center">
                            <label class="relative cursor-pointer rounded-md font-medium text-primary hover:text-primary-hover focus-within:outline-none" for="file-upload">
                                <span>Upload a file</span>
                                <input class="sr-only" id="file-upload" name="file-upload" type="file"/>
                            </label>
                            <p class="pl-1">or drag and drop</p>
                        </div>
                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
                    </div>
                </div>
            </div>

            <div class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700/60 p-5">
                <label class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">Author</label>
                <button class="relative w-full bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm" type="button">
                    <span class="flex items-center">
                        <img alt="" class="flex-shrink-0 h-6 w-6 rounded-full" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDgCouvgQBgY8rxXgd_1y5gxEsDN2UQVNn1W4gCRoJumBbTNPw1Ix7utTaVjwH6GPI3kdVjnA-WOkFk_FNtE85vKaxmab5Pmr9_3I0NZnRUIlrgrpcHtOhXkxTe2FCVz7icLGiyRSyuBQzhji8A7whMm0j9C-WTwpi0jKFS9Y9PYTwGmvP0rwwVtIIYpwsGxhFBHfDQUCvO9IXhW7lEPgSBHX-AWrH9zWWj5MAyNhzFMUGigQFe_UGLNPq561X1LA1m8gY8P9DhXhA"/>
                        <span class="ml-3 block truncate text-slate-900 dark:text-white">John Doe</span>
                        <span class="ml-2 text-gray-400 text-xs">Marketing</span>
                    </span>
                    <span class="ml-3 absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                        <span class="material-icons-round text-gray-400">expand_more</span>
                    </span>
                </button>
            </div>
        </div>
    </form>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/app.php'; ?>
