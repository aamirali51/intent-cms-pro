<?php
$title = 'Dashboard';
ob_start();
?>
<div>
    <!-- Welcome Header -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard</h2>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Welcome back! Here's what's happening with your site.</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Posts Card -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <span class="text-gray-500 dark:text-gray-400 text-sm font-medium">Total Posts</span>
                <span class="material-icons-round text-primary bg-primary/10 p-2 rounded-lg text-xl">article</span>
            </div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white" id="dash-posts">-</div>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">Published content</p>
        </div>

        <!-- Pages Card -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <span class="text-gray-500 dark:text-gray-400 text-sm font-medium">Total Pages</span>
                <span class="material-icons-round text-blue-500 bg-blue-500/10 p-2 rounded-lg text-xl">layers</span>
            </div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white" id="dash-pages">-</div>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">Static pages</p>
        </div>

        <!-- Media Card -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <span class="text-gray-500 dark:text-gray-400 text-sm font-medium">Media Files</span>
                <span class="material-icons-round text-amber-500 bg-amber-500/10 p-2 rounded-lg text-xl">folder</span>
            </div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white" id="dash-media">-</div>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">Images & files</p>
        </div>

        <!-- Users Card -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <span class="text-gray-500 dark:text-gray-400 text-sm font-medium">Users</span>
                <span class="material-icons-round text-green-500 bg-green-500/10 p-2 rounded-lg text-xl">people</span>
            </div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white" id="dash-users">-</div>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">Registered accounts</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Posts -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between">
                <h3 class="font-semibold text-gray-900 dark:text-white">Recent Posts</h3>
                <a href="posts.php" class="text-sm text-primary hover:text-primary/80 font-medium">View all</a>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-slate-700" id="recent-posts">
                <div class="p-6 text-center text-gray-400">
                    <span class="material-icons-round text-4xl mb-2">hourglass_empty</span>
                    <p class="text-sm">Loading...</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
                <h3 class="font-semibold text-gray-900 dark:text-white">Quick Actions</h3>
            </div>
            <div class="p-4 space-y-2">
                <a href="posts.php?action=new" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors group">
                    <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                        <span class="material-icons-round text-primary">add</span>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white group-hover:text-primary">New Post</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Create a new blog post</p>
                    </div>
                </a>
                <a href="pages.php?action=new" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors group">
                    <div class="w-10 h-10 rounded-lg bg-blue-500/10 flex items-center justify-center">
                        <span class="material-icons-round text-blue-500">note_add</span>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white group-hover:text-primary">New Page</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Create a static page</p>
                    </div>
                </a>
                <a href="media.php" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors group">
                    <div class="w-10 h-10 rounded-lg bg-amber-500/10 flex items-center justify-center">
                        <span class="material-icons-round text-amber-500">upload</span>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white group-hover:text-primary">Upload Media</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Add images or files</p>
                    </div>
                </a>
                <a href="settings.php" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors group">
                    <div class="w-10 h-10 rounded-lg bg-gray-500/10 flex items-center justify-center">
                        <span class="material-icons-round text-gray-500">settings</span>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white group-hover:text-primary">Settings</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Configure your site</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- System Info -->
    <div class="mt-6 bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
            <h3 class="font-semibold text-gray-900 dark:text-white">System Information</h3>
        </div>
        <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div>
                <p class="text-gray-500 dark:text-gray-400">PHP Version</p>
                <p class="font-medium text-gray-900 dark:text-white"><?= phpversion() ?></p>
            </div>
            <div>
                <p class="text-gray-500 dark:text-gray-400">CMS Version</p>
                <p class="font-medium text-gray-900 dark:text-white">1.0.0</p>
            </div>
            <div>
                <p class="text-gray-500 dark:text-gray-400">Framework</p>
                <p class="font-medium text-gray-900 dark:text-white">Intent v0.8.1</p>
            </div>
            <div>
                <p class="text-gray-500 dark:text-gray-400">Database</p>
                <p class="font-medium text-gray-900 dark:text-white">MySQL</p>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', async () => {
        try {
            const res = await App.api('/dashboard/stats');
            if (res) {
                document.getElementById('dash-posts').textContent = res.posts || 0;
                document.getElementById('dash-pages').textContent = res.pages || 0;
                document.getElementById('dash-media').textContent = res.media || 0;
                document.getElementById('dash-users').textContent = res.users || 0;
            }

            // Load recent posts
            const posts = await App.api('/posts?limit=5');
            const container = document.getElementById('recent-posts');
            
            if (posts && posts.data && posts.data.length > 0) {
                container.innerHTML = posts.data.map(post => `
                    <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/30 transition-colors">
                        <div class="min-w-0 flex-1">
                            <p class="font-medium text-gray-900 dark:text-white truncate">${post.title || 'Untitled'}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">${post.status || 'draft'} · ${new Date(post.created_at).toLocaleDateString()}</p>
                        </div>
                        <a href="posts.php?edit=${post.id}" class="ml-4 text-primary hover:text-primary/80">
                            <span class="material-icons-round text-xl">edit</span>
                        </a>
                    </div>
                `).join('');
            } else {
                container.innerHTML = `
                    <div class="p-6 text-center text-gray-400">
                        <span class="material-icons-round text-4xl mb-2">article</span>
                        <p class="text-sm">No posts yet</p>
                        <a href="posts.php?action=new" class="text-primary text-sm hover:underline mt-2 inline-block">Create your first post</a>
                    </div>
                `;
            }
        } catch (e) {
            console.error(e);
        }
    });
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
