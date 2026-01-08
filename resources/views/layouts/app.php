<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Intent CMS' ?></title>
    <link href="/assets/css/app.css" rel="stylesheet">
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-600 dark:text-slate-300 font-sans transition-colors duration-200">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-surface-light dark:bg-surface-dark border-r border-slate-200 dark:border-slate-700 flex flex-col flex-shrink-0 transition-colors duration-200 hidden md:flex">
            <div class="h-16 flex items-center px-6 border-b border-slate-100 dark:border-slate-700/50">
                <div class="bg-primary text-white p-1 rounded mr-3">
                    <span class="material-icons-round text-xl">grid_view</span>
                </div>
                <span class="text-xl font-bold text-slate-800 dark:text-white">Intent CMS</span>
            </div>
            
            <div class="flex-1 overflow-y-auto py-4 px-3 custom-scrollbar">
                <nav class="space-y-1 mb-8">
                    <a class="flex items-center px-3 py-2.5 <?= $active === 'dashboard' ? 'bg-slate-100 dark:bg-slate-700/50 text-primary dark:text-primary' : 'text-slate-600 dark:text-slate-300 hover:text-primary dark:hover:text-primary' ?> rounded-lg transition-colors group" href="/">
                        <span class="material-icons-round mr-3 text-slate-400 group-hover:text-primary">dashboard</span>
                        <span class="font-medium">Dashboard</span>
                    </a>
                    
                    <a class="flex items-center px-3 py-2.5 <?= $active === 'pages' ? 'bg-slate-100 dark:bg-slate-700/50 text-primary dark:text-primary' : 'text-slate-600 dark:text-slate-300 hover:text-primary dark:hover:text-primary' ?> rounded-lg transition-colors group" href="/pages">
                        <span class="material-icons-round mr-3 text-slate-400 group-hover:text-primary">article</span>
                        <span class="font-medium">Pages</span>
                    </a>
                    
                    <a class="flex items-center px-3 py-2.5 <?= $active === 'posts' ? 'bg-slate-100 dark:bg-slate-700/50 text-primary dark:text-primary' : 'text-slate-600 dark:text-slate-300 hover:text-primary dark:hover:text-primary' ?> rounded-lg transition-colors group" href="/posts">
                        <span class="material-icons-round mr-3 text-slate-400 group-hover:text-primary">post_add</span>
                        <span class="font-medium">Posts</span>
                    </a>

                    <a class="flex items-center px-3 py-2.5 <?= $active === 'categories' ? 'bg-slate-100 dark:bg-slate-700/50 text-primary dark:text-primary' : 'text-slate-600 dark:text-slate-300 hover:text-primary dark:hover:text-primary' ?> rounded-lg transition-colors group" href="/categories">
                        <span class="material-icons-round mr-3 text-slate-400 group-hover:text-primary">category</span>
                        <span class="font-medium">Categories</span>
                    </a>

                    <a class="flex items-center px-3 py-2.5 <?= $active === 'tags' ? 'bg-slate-100 dark:bg-slate-700/50 text-primary dark:text-primary' : 'text-slate-600 dark:text-slate-300 hover:text-primary dark:hover:text-primary' ?> rounded-lg transition-colors group" href="/tags">
                        <span class="material-icons-round mr-3 text-slate-400 group-hover:text-primary">label</span>
                        <span class="font-medium">Tags</span>
                    </a>

                    <a class="flex items-center px-3 py-2.5 <?= $active === 'media' ? 'bg-slate-100 dark:bg-slate-700/50 text-primary dark:text-primary' : 'text-slate-600 dark:text-slate-300 hover:text-primary dark:hover:text-primary' ?> rounded-lg transition-colors group" href="/media">
                        <span class="material-icons-round mr-3 text-slate-400 group-hover:text-primary">folder_open</span>
                        <span class="font-medium">Media Library</span>
                    </a>

                    <a class="flex items-center px-3 py-2.5 <?= $active === 'comments' ? 'bg-slate-100 dark:bg-slate-700/50 text-primary dark:text-primary' : 'text-slate-600 dark:text-slate-300 hover:text-primary dark:hover:text-primary' ?> rounded-lg transition-colors group" href="/comments">
                        <span class="material-icons-round mr-3 text-slate-400 group-hover:text-primary">chat_bubble_outline</span>
                        <span class="font-medium">Comments</span>
                    </a>

                    <a class="flex items-center px-3 py-2.5 <?= $active === 'users' ? 'bg-slate-100 dark:bg-slate-700/50 text-primary dark:text-primary' : 'text-slate-600 dark:text-slate-300 hover:text-primary dark:hover:text-primary' ?> rounded-lg transition-colors group" href="/users">
                        <span class="material-icons-round mr-3 text-slate-400 group-hover:text-primary">people</span>
                        <span class="font-medium">Users</span>
                    </a>
                    
                    <a class="flex items-center px-3 py-2.5 <?= $active === 'settings' ? 'bg-slate-100 dark:bg-slate-700/50 text-primary dark:text-primary' : 'text-slate-600 dark:text-slate-300 hover:text-primary dark:hover:text-primary' ?> rounded-lg transition-colors group" href="/settings">
                        <span class="material-icons-round mr-3 text-slate-400 group-hover:text-primary">settings</span>
                        <span class="font-medium">Settings</span>
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
            <!-- Header -->
            <header class="h-16 bg-surface-light dark:bg-surface-dark border-b border-slate-200 dark:border-slate-700 flex items-center justify-between px-6 flex-shrink-0 transition-colors duration-200 z-10">
                <div class="flex items-center flex-1">
                    <button class="mr-4 md:hidden text-slate-500">
                        <span class="material-icons-round">menu</span>
                    </button>
                    <div class="relative w-full max-w-xs hidden sm:block">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="material-icons-round text-slate-400 text-xl">search</span>
                        </span>
                        <input class="block w-full pl-10 pr-3 py-2 border-none rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 sm:text-sm transition-colors" placeholder="Search..." type="text"/>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <button class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 relative">
                        <span class="material-icons-round">notifications</span>
                        <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white dark:border-slate-800"></span>
                    </button>
                    <div class="flex items-center space-x-3 border-l border-slate-200 dark:border-slate-700 pl-4">
                        <div class="w-9 h-9 rounded-full bg-slate-200 flex items-center justify-center text-slate-500">
                            <span class="material-icons-round">person</span>
                        </div>
                        <div class="hidden sm:block text-left">
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">Admin</p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto p-6 scroll-smooth">
                <?= $content ?>
            </main>
        </div>
    </div>
</body>
</html>
