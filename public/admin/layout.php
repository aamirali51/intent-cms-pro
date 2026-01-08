<?php
// public/admin/layout.php
// Shared layout file

// Ensure helpers and plugins are loaded
require_once __DIR__ . '/../../config/bootstrap.php';

// Start session if not already started
session();

// Check authentication - redirect to login if not authenticated
if (!Core\Auth::check()) {
    $loginUrl = '/login';
    // Handle subdirectory installations
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    $basePath = str_replace('/admin', '', $scriptDir);
    if ($basePath !== '/' && $basePath !== '') {
        $loginUrl = $basePath . $loginUrl;
    }
    header('Location: ' . $loginUrl);
    exit;
}

// Calculate base path for subdirectory support (e.g., /intent-cms-pro)
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
$basePath = str_replace('/admin', '', $scriptDir);
if ($basePath === '/') $basePath = '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title . ' - ' : '' ?>Intent CMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    
    <!-- Tailwind CSS via CDN - Full library with all utility classes -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#8b5cf6',
                        primaryHover: '#7c3aed',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    
    <!-- Local CSS for custom styles -->
    <link rel="stylesheet" href="<?= $basePath ?>/assets/css/app.css">

    <!-- Plugin Assets Hook -->
    <?php do_action('cms.admin.head'); ?>

    <!-- Editor.js & Plugins -->
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/header@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/list@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/image@latest"></script>

    <script>
        // Shared App Object (Simplified for Multi-Page)
        const App = {
            csrfToken: null,
            editor: null,

            async init() {
                await this.loadCsrfToken();
                this.bindThemeToggle();
                this.loadUser();
                this.loadStats();
            },

            async loadCsrfToken() {
                try {
                    // Use raw fetch here - cannot use this.api() as it requires csrfToken
                    const basePath = window.location.pathname.split('/admin/')[0];
                    const res = await fetch(basePath + '/api/csrf-token');
                    if (res.ok) {
                        const data = await res.json();
                        this.csrfToken = data.token;
                    }
                } catch (e) {
                    console.error('CSRF load failed:', e);
                }
            },

            async loadUser() {
                try {
                    const user = await this.api('/user');
                    if (user) {
                        const nameEl = document.getElementById('sidebar-name');
                        if (nameEl) nameEl.textContent = user.name || 'Admin';
                        const emailEl = document.getElementById('sidebar-email');
                        if (emailEl) emailEl.textContent = user.email || '';
                        const avatarEl = document.getElementById('sidebar-avatar');
                        if (avatarEl) avatarEl.textContent = (user.name || 'A')[0].toUpperCase();
                    }
                } catch (e) { }
            },

            async loadStats() {
                try {
                    const stats = await this.api('/dashboard/stats');
                    if (stats) {
                        const pCount = document.getElementById('posts-count');
                        if(pCount) pCount.textContent = stats.posts || 0;
                        const pgCount = document.getElementById('pages-count');
                        if(pgCount) pgCount.textContent = stats.pages || 0;
                    }
                } catch (e) { }
            },

            bindThemeToggle() {
                const btn = document.getElementById('theme-toggle');
                if(btn) {
                    btn.addEventListener('click', () => {
                        document.documentElement.classList.toggle('dark');
                    });
                }
            },

            async api(endpoint, method = 'GET', body = null) {
                try {
                    // Dynamic base path detection for subdirectory support
                    // e.g. /intent-cms-pro/admin/media.php -> /intent-cms-pro
                    const basePath = window.location.pathname.split('/admin/')[0];
                    const apiUrl = basePath + '/api' + endpoint;

                    const options = {
                        method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken
                        }
                    };
                    if (body) options.body = JSON.stringify(body);

                    const res = await fetch(apiUrl, options);
                    if (!res.ok) {
                        // Capture text response if JSON fails (e.g. 404 HTML)
                        const text = await res.text();
                        try {
                            const err = JSON.parse(text);
                            throw new Error(err.error || err.message || 'API Error');
                        } catch (e) {
                             console.error('API Error Response:', text.substring(0, 500)); // Log first 500 chars
                             throw new Error(`API Request Failed: ${res.status} ${res.statusText}`);
                        }
                    }
                    return await res.json();
                } catch (e) {
                    console.error('API Request Failed:', e);
                    if (method === 'GET' && endpoint.includes('?')) return [];
                    return null;
                }
            },

            // Modal System
            showModal({ title, body, actions }) {
                const modal = document.getElementById('global-modal');
                document.getElementById('modal-title').innerText = title;
                document.getElementById('modal-body').innerHTML = body;

                const actionsContainer = document.getElementById('modal-actions');
                actionsContainer.innerHTML = '';
                actions.forEach(btn => {
                    const b = document.createElement('button');
                    b.className = btn.class || 'mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 sm:mt-0 sm:w-auto';
                    if (btn.style) b.style.cssText = btn.style;
                    b.innerText = btn.text;
                    b.onclick = () => {
                        if (btn.onClick) btn.onClick();
                        if (btn.close !== false) this.closeModal();
                    };
                    actionsContainer.appendChild(b);
                });

                modal.classList.remove('hidden');
                modal.style.display = 'flex';

                const dialogDiv = modal.querySelector('.transform');
                if (dialogDiv) {
                    dialogDiv.classList.remove('sm:max-w-lg', 'sm:max-w-4xl', 'sm:max-w-6xl');
                    if (title.includes('Edit Post') || title.includes('Create New Post') || title.includes('Edit Page')) {
                        dialogDiv.classList.add('sm:max-w-6xl');
                    } else {
                        dialogDiv.classList.add('sm:max-w-lg');
                    }
                }
            },

            closeModal() {
                const modal = document.getElementById('global-modal');
                modal.classList.add('hidden');
                modal.style.display = 'none';
                if (this.editor) {
                    try { this.editor.destroy(); } catch (e) { }
                    this.editor = null;
                }
            },

            // Lightbox
            showLightbox(src) {
                const modal = document.getElementById('lightbox-modal');
                document.getElementById('lightbox-image').src = src;
                modal.classList.remove('hidden');
                modal.style.display = 'flex';
            },
            closeLightbox() {
                const modal = document.getElementById('lightbox-modal');
                modal.classList.add('hidden');
                modal.style.display = 'none';
            },

            // Global Toast Notification System
            showToast(message, type = 'success', action = null) {
                const existing = document.getElementById('toast-notification');
                if (existing) existing.remove();
                
                const colors = {
                    success: 'background: linear-gradient(135deg, #10b981, #059669); color: white;',
                    error: 'background: linear-gradient(135deg, #ef4444, #dc2626); color: white;',
                    info: 'background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white;',
                    warning: 'background: linear-gradient(135deg, #f59e0b, #d97706); color: white;'
                };
                
                const toast = document.createElement('div');
                toast.id = 'toast-notification';
                toast.style.cssText = `position: fixed; bottom: 24px; right: 24px; padding: 16px 20px; border-radius: 12px; font-size: 14px; font-weight: 500; z-index: 9999; display: flex; align-items: center; gap: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); animation: toastSlideIn 0.3s ease; ${colors[type] || colors.info}`;
                
                const icons = { success: 'check_circle', error: 'error', info: 'info', warning: 'warning' };
                
                let actionHtml = '';
                if (action && action.url) {
                    actionHtml = `<a href="${action.url}" target="${action.target || '_blank'}" style="background: rgba(255,255,255,0.2); padding: 6px 12px; border-radius: 6px; text-decoration: none; color: white; font-weight: 600; display: flex; align-items: center; gap: 4px; margin-left: 8px;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'"><span class="material-icons-round" style="font-size: 16px;">open_in_new</span>${action.text || 'View'}</a>`;
                } else if (action && action.onClick) {
                    actionHtml = `<button onclick="${action.onClick}" style="background: rgba(255,255,255,0.2); padding: 6px 12px; border-radius: 6px; border: none; color: white; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 4px; margin-left: 8px;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">${action.text || 'Action'}</button>`;
                }
                
                toast.innerHTML = `
                    <span class="material-icons-round" style="font-size: 24px;">${icons[type] || 'info'}</span>
                    <span>${message}</span>
                    ${actionHtml}
                    <button onclick="this.parentElement.remove()" style="background: none; border: none; color: white; opacity: 0.7; cursor: pointer; padding: 4px; margin-left: 8px;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'">
                        <span class="material-icons-round" style="font-size: 18px;">close</span>
                    </button>
                `;
                
                // Add animation keyframes if not already added
                if (!document.getElementById('toast-animation-style')) {
                    const style = document.createElement('style');
                    style.id = 'toast-animation-style';
                    style.textContent = '@keyframes toastSlideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }';
                    document.head.appendChild(style);
                }
                
                document.body.appendChild(toast);
                
                // Auto dismiss (longer for action toasts)
                setTimeout(() => toast.remove(), action ? 8000 : 5000);
            },
        };
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>

<body class="bg-slate-50 dark:bg-slate-900 text-gray-800 dark:text-gray-200 transition-colors duration-200">
    <div id="app" class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 flex-shrink-0 bg-white dark:bg-slate-800 border-r border-gray-200 dark:border-slate-700 flex flex-col transition-colors duration-200 hidden md:flex">
            <!-- Logo -->
            <div class="h-16 flex items-center px-6 border-b border-gray-200 dark:border-slate-700">
                <span class="material-icons-round text-primary text-3xl mr-2">grid_view</span>
                <h1 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">Intent CMS</h1>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1 no-scrollbar">
                <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 mt-2">Main</p>

                <?php
                // $basePath is defined at the top of the layout file
                // Pre-load counts to avoid flicker
                $postsCount = 0;
                $pagesCount = 0;
                try {
                    $postsResult = db()->raw("SELECT COUNT(*) as cnt FROM cms_content WHERE type = 'post'");
                    $postsCount = !empty($postsResult) ? (int)($postsResult[0]['cnt'] ?? 0) : 0;
                    $pagesResult = db()->raw("SELECT COUNT(*) as cnt FROM cms_content WHERE type = 'page'");
                    $pagesCount = !empty($pagesResult) ? (int)($pagesResult[0]['cnt'] ?? 0) : 0;
                } catch (\Throwable $e) {
                    // Silently fail - counts will show 0
                }
                
                $navItems = [
                    'Dashboard' => ['url' => $basePath . '/admin/dashboard.php', 'icon' => 'dashboard'],
                    'Pages'     => ['url' => $basePath . '/admin/pages.php', 'icon' => 'layers', 'count_id' => 'pages-count', 'count' => $pagesCount],
                    'Posts'     => ['url' => $basePath . '/admin/posts.php', 'icon' => 'article', 'count_id' => 'posts-count', 'count' => $postsCount, 'count_class' => 'ml-auto bg-primary text-white py-0.5 px-2 rounded-full text-xs font-medium'],
                    'Media Files' => ['url' => $basePath . '/admin/media.php', 'icon' => 'folder'],
                    'Categories' => ['url' => $basePath . '/admin/categories.php', 'icon' => 'category'],
                    'Tags'      => ['url' => $basePath . '/admin/tags.php', 'icon' => 'label'],
                ];

                // Allow plugins to inject menu items
                // Filter: cms.admin.menu
                // Arguments: array $navItems
                $navItems = apply_filters('cms.admin.menu', $navItems);

                $currentUrl = $_SERVER['PHP_SELF'];

                foreach ($navItems as $name => $item) {
                    $active = strpos($currentUrl, basename($item['url'])) !== false;
                    $bgClass = $active ? 'bg-primary/10 text-primary' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-primary';
                    
                    echo "<a href=\"{$item['url']}\" class=\"flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors $bgClass\">";
                    echo "<span class=\"material-icons-round text-xl mr-3\">{$item['icon']}</span>";
                    echo "$name";
                    if (isset($item['count_id'])) {
                        $countClass = isset($item['count_class']) ? $item['count_class'] : 'ml-auto bg-gray-100 dark:bg-gray-700 py-0.5 px-2 rounded-full text-xs font-medium text-gray-600 dark:text-gray-300';
                        $countValue = $item['count'] ?? 0;
                        echo "<span class=\"$countClass\" id=\"{$item['count_id']}\">$countValue</span>";
                    }
                    echo "</a>";
                }
                ?>

                <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 mt-6">Management</p>
                
                <a href="<?= $basePath ?>/admin/comments.php" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-primary transition-colors">
                    <span class="material-icons-round text-xl mr-3">comment</span> Comments
                </a>
                <a href="<?= $basePath ?>/admin/users.php" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-primary transition-colors">
                    <span class="material-icons-round text-xl mr-3">people</span> Users
                </a>
                <a href="<?= $basePath ?>/admin/plugins.php" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-primary transition-colors">
                    <span class="material-icons-round text-xl mr-3">extension</span> Plugins
                </a>
                <a href="<?= $basePath ?>/admin/settings.php" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-primary transition-colors">
                    <span class="material-icons-round text-xl mr-3">settings</span> Settings
                </a>

                <?php
                // Render plugin menu items from #[AdminMenuItem] attributes
                $pluginManager = \App\Services\PluginManager::getInstance();
                $pluginMenuItems = $pluginManager->getMenuItems();
                if (!empty($pluginMenuItems)) {
                    echo '<p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 mt-6">Plugins</p>';
                    foreach ($pluginMenuItems as $menuItem) {
                        $icon = $menuItem->icon ?: 'extension';
                        $route = $basePath . $menuItem->route;
                        echo "<a href=\"{$route}\" class=\"flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-primary transition-colors\">";
                        echo "<span class=\"material-icons-round text-xl mr-3\">{$icon}</span> {$menuItem->label}";
                        if ($menuItem->badge) {
                            echo "<span class=\"ml-auto bg-primary text-white text-xs px-2 py-0.5 rounded-full\">{$menuItem->badge}</span>";
                        }
                        echo "</a>";
                    }
                }
                ?>

                <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 mt-6">Developer</p>
                
                <a href="<?= $basePath ?>/admin/developer.php" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-primary transition-colors">
                    <span class="material-icons-round text-xl mr-3">code</span> Docs & API
                </a>
            </nav>

            <!-- User Profile -->
            <div class="mt-auto flex-shrink-0 border-t border-gray-200 dark:border-slate-700 p-4">
                <div class="flex items-center w-full">
                    <div class="h-9 w-9 rounded-full bg-primary flex items-center justify-center text-white font-medium" id="sidebar-avatar">A</div>
                    <div class="ml-3 truncate">
                        <p class="text-sm font-medium text-gray-900 dark:text-white" id="sidebar-name">Admin</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate" id="sidebar-email">admin@intent.com</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
            <!-- Header -->
            <header class="h-16 flex items-center justify-between px-6 bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700 z-10 transition-colors duration-200">
                <button class="md:hidden text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <span class="material-icons-round">menu</span>
                </button>

                <!-- Search -->
                <div class="hidden md:flex items-center w-96 relative">
                    <span class="material-icons-round absolute left-3 text-gray-400">search</span>
                    <input type="text" placeholder="Search anything..." class="w-full pl-10 pr-4 py-2 text-sm bg-gray-50 dark:bg-gray-800 border-none rounded-lg text-gray-900 dark:text-gray-200 placeholder-gray-400 focus:ring-2 focus:ring-primary">
                </div>

                <!-- Header Right -->
                <div class="flex items-center space-x-4">
                    <button class="text-gray-400 hover:text-primary transition-colors relative">
                        <span class="material-icons-round">notifications</span>
                        <span class="absolute top-0 right-0 h-2 w-2 bg-red-500 rounded-full border-2 border-white dark:border-slate-800"></span>
                    </button>
                    <button id="theme-toggle" class="text-gray-400 hover:text-primary transition-colors">
                        <span class="material-icons-round">dark_mode</span>
                    </button>
                    <form action="<?= $basePath ?>/logout" method="POST" id="logout-form" class="inline">
                        <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors" title="Logout">
                            <span class="material-icons-round">logout</span>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto bg-slate-50 dark:bg-slate-900 p-6 transition-colors duration-200" id="content">
                <?= $content ?? '' ?>
            </main>
        </div>
    </div>

    <!-- Global Modal -->
    <div id="global-modal" class="fixed inset-0 z-50 hidden" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0;" onclick="App.closeModal()"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0;">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0" style="min-height: 100vh; display: flex; align-items: center; justify-content: center;">
                <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-slate-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-200 dark:border-slate-700">
                    <div class="bg-white dark:bg-slate-800 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white mb-4" id="modal-title"></h3>
                                <div class="mt-2" id="modal-body"></div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2" id="modal-actions"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lightbox Modal (for Media) -->
    <div id="lightbox-modal" class="hidden" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 60; background: rgba(0,0,0,0.9); display: none; align-items: center; justify-content: center;" onclick="App.closeLightbox()">
        <button onclick="App.closeLightbox()" style="position: absolute; top: 20px; right: 20px; color: white; background: none; border: none; cursor: pointer; z-index: 61;">
            <span class="material-icons-round" style="font-size: 32px;">close</span>
        </button>
        <img id="lightbox-image" src="" alt="" style="max-width: 90%; max-height: 90%; object-fit: contain;" onclick="event.stopPropagation()">
    </div>

    <!-- Upload Progress Modal -->
    <div id="upload-progress-modal" class="hidden" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 55; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center;">
        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-2xl" style="min-width: 320px; max-width: 400px;" onclick="event.stopPropagation()">
            <h3 class="font-bold text-gray-900 dark:text-white mb-4">Uploading Files...</h3>
            <div class="space-y-3" id="upload-progress-list"></div>
            <p class="text-sm text-gray-500 mt-4" id="upload-progress-status">Preparing...</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => App.init());
    </script>
    
    <!-- Plugin Scripts Hook -->
    <?php do_action('cms.admin.footer'); ?>
</body>
</html>
