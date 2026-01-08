<?php
$title = 'Posts';
ob_start();
?>
<div>
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Posts</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage your blog content, filter by status, or create new articles.</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                <span class="material-icons-round text-lg mr-2">file_download</span>
                Export
            </button>
            <button type="button" onclick="createNewPost()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                <span class="material-icons-round text-lg mr-2">add</span>
                Add New Post
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 mb-6 p-4">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            <div class="md:col-span-4">
                <label for="post-search" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Search Posts</label>
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="material-icons-round text-gray-400 text-lg">search</span>
                    </div>
                    <input type="text" id="post-search" placeholder="Title, slug, or summary" onkeyup="filterPosts()" class="focus:ring-primary focus:border-primary block w-full pl-10 sm:text-sm border-gray-300 dark:border-slate-600 rounded-md bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100">
                </div>
            </div>
            <div class="md:col-span-2">
                <label for="status-filter" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select id="status-filter" onchange="filterPosts()" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-slate-600 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100">
                    <option value="">Any Status</option>
                    <option value="published">Published</option>
                    <option value="draft">Draft</option>
                </select>
            </div>
            <div class="md:col-span-2 flex justify-end md:col-start-11 md:col-span-2">
                <button onclick="resetFilters()" class="text-sm text-primary hover:text-purple-700 font-medium flex items-center">
                    <span class="material-icons-round text-sm mr-1">filter_list_off</span>
                    Reset Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Posts Table -->
    <div class="bg-white dark:bg-slate-800 shadow-sm border border-gray-200 dark:border-slate-700 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                <thead class="bg-gray-50 dark:bg-slate-700/50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-12">
                            <input type="checkbox" id="select-all" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded dark:bg-slate-700 dark:border-slate-600">
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Title & Slug
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden lg:table-cell">
                            Author
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden md:table-cell">
                            Date
                        </th>
                        <th scope="col" class="relative px-6 py-3 w-24">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody id="posts-table-body" class="bg-white dark:bg-slate-800 divide-y divide-gray-200 dark:divide-slate-700">
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <span class="material-icons-round text-4xl mb-2">hourglass_empty</span>
                            <p>Loading posts...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div id="pagination" class="bg-white dark:bg-slate-800 px-4 py-3 flex items-center justify-between border-t border-gray-200 dark:border-slate-700 sm:px-6 hidden">
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700 dark:text-gray-400">
                        Showing <span id="showing-start" class="font-medium">1</span> to <span id="showing-end" class="font-medium">10</span> of <span id="total-count" class="font-medium">0</span> results
                    </p>
                </div>
                <div id="pagination-buttons">
                    <!-- Pagination buttons will be inserted here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let allPosts = [];
let currentPage = 1;
const postsPerPage = 10;

document.addEventListener('DOMContentLoaded', async () => {
    await loadPosts();
});

async function loadPosts() {
    try {
        const res = await App.api('/posts?limit=100');
        if (res && res.data) {
            allPosts = res.data;
            renderPosts();
        }
    } catch (e) {
        console.error('Failed to load posts:', e);
        document.getElementById('posts-table-body').innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-red-500">
                    <span class="material-icons-round text-4xl mb-2">error</span>
                    <p>Failed to load posts</p>
                </td>
            </tr>
        `;
    }
}

function filterPosts() {
    currentPage = 1;
    renderPosts();
}

function resetFilters() {
    document.getElementById('post-search').value = '';
    document.getElementById('status-filter').value = '';
    currentPage = 1;
    renderPosts();
}

function renderPosts() {
    const search = document.getElementById('post-search').value.toLowerCase();
    const status = document.getElementById('status-filter').value;
    
    let filtered = allPosts.filter(post => {
        const matchSearch = !search || 
            (post.title && post.title.toLowerCase().includes(search)) ||
            (post.slug && post.slug.toLowerCase().includes(search));
        const matchStatus = !status || post.status === status;
        return matchSearch && matchStatus;
    });
    
    const total = filtered.length;
    const start = (currentPage - 1) * postsPerPage;
    const end = Math.min(start + postsPerPage, total);
    const paginated = filtered.slice(start, end);
    
    const tbody = document.getElementById('posts-table-body');
    
    if (paginated.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                    <span class="material-icons-round text-4xl mb-2">article</span>
                    <p>No posts found</p>
                    <button onclick="createNewPost()" class="text-primary hover:underline mt-2">Create your first post</button>
                </td>
            </tr>
        `;
        document.getElementById('pagination').classList.add('hidden');
        return;
    }
    
    tbody.innerHTML = paginated.map(post => {
        const statusColors = {
            published: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
            draft: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
            scheduled: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300'
        };
        const statusClass = statusColors[post.status] || statusColors.draft;
        const date = new Date(post.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        const icon = getPostIcon(post);
        
        return `
            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                    <input type="checkbox" data-id="${post.id}" class="post-checkbox h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded dark:bg-slate-700 dark:border-slate-600">
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 ${icon.bg} rounded-lg flex items-center justify-center">
                            <span class="material-icons-round ${icon.color}">${icon.icon}</span>
                        </div>
                        <div class="ml-4 min-w-0">
                            <div class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-xs">${escapeHtml(post.title || 'Untitled')}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">/${escapeHtml(post.slug || '')}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-8 w-8 bg-primary rounded-full flex items-center justify-center text-white font-medium text-sm">
                            ${(post.author_name || 'A')[0].toUpperCase()}
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">${escapeHtml(post.author_name || 'Admin')}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">
                        ${(post.status || 'draft').charAt(0).toUpperCase() + (post.status || 'draft').slice(1)}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 hidden md:table-cell">
                    ${date}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex justify-end space-x-2">
                        <button onclick="editPost(${post.id})" class="text-primary hover:text-purple-700 dark:hover:text-purple-400" title="Edit">
                            <span class="material-icons-round text-lg">edit</span>
                        </button>
                        <button onclick="deletePost(${post.id}, '${escapeHtml(post.title || 'Untitled')}')" class="text-gray-400 hover:text-red-600 dark:hover:text-red-400" title="Delete">
                            <span class="material-icons-round text-lg">delete</span>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
    
    // Update pagination
    document.getElementById('showing-start').textContent = start + 1;
    document.getElementById('showing-end').textContent = end;
    document.getElementById('total-count').textContent = total;
    
    const totalPages = Math.ceil(total / postsPerPage);
    if (totalPages > 1) {
        document.getElementById('pagination').classList.remove('hidden');
        renderPagination(totalPages);
    } else {
        document.getElementById('pagination').classList.add('hidden');
    }
}

function renderPagination(totalPages) {
    const container = document.getElementById('pagination-buttons');
    let html = '<nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">';
    
    // Previous button
    html += `
        <button onclick="goToPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''} 
            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700 disabled:opacity-50 disabled:cursor-not-allowed">
            <span class="material-icons-round text-sm">chevron_left</span>
        </button>
    `;
    
    // Page numbers
    for (let i = 1; i <= Math.min(totalPages, 5); i++) {
        const isActive = i === currentPage;
        html += `
            <button onclick="goToPage(${i})" 
                class="${isActive ? 'z-10 bg-purple-50 dark:bg-purple-900/20 border-primary text-primary' : 'bg-white dark:bg-slate-800 border-gray-300 dark:border-slate-600 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700'} relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                ${i}
            </button>
        `;
    }
    
    if (totalPages > 5) {
        html += `<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm font-medium text-gray-700 dark:text-gray-400">...</span>`;
    }
    
    // Next button
    html += `
        <button onclick="goToPage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''} 
            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700 disabled:opacity-50 disabled:cursor-not-allowed">
            <span class="material-icons-round text-sm">chevron_right</span>
        </button>
    `;
    
    html += '</nav>';
    container.innerHTML = html;
}

function goToPage(page) {
    const totalPages = Math.ceil(allPosts.length / postsPerPage);
    if (page < 1 || page > totalPages) return;
    currentPage = page;
    renderPosts();
}

function getPostIcon(post) {
    const icons = [
        { bg: 'bg-purple-50 dark:bg-purple-900/20', color: 'text-purple-500', icon: 'article' },
        { bg: 'bg-indigo-50 dark:bg-indigo-900/20', color: 'text-indigo-500', icon: 'code' },
        { bg: 'bg-pink-50 dark:bg-pink-900/20', color: 'text-pink-500', icon: 'palette' },
        { bg: 'bg-green-50 dark:bg-green-900/20', color: 'text-green-500', icon: 'trending_up' },
        { bg: 'bg-blue-50 dark:bg-blue-900/20', color: 'text-blue-500', icon: 'lightbulb' },
    ];
    return icons[post.id % icons.length];
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function createNewPost() {
    // Navigate to full post editor for new posts
    window.location.href = 'post-editor.php';
}

async function saveNewPost() {
    const title = document.getElementById('new-post-title').value.trim();
    if (!title) {
        alert('Please enter a title');
        return;
    }
    
    let content = {};
    if (App.editor) {
        content = await App.editor.save();
    }
    
    const slug = title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
    
    try {
        const res = await App.api('/posts', 'POST', {
            title,
            slug,
            content: JSON.stringify(content),
            status: 'draft',
            type: 'post'
        });
        
        if (res && !res.error) {
            App.closeModal();
            await loadPosts();
        } else {
            alert(res?.error || 'Failed to create post');
        }
    } catch (e) {
        alert('Failed to create post');
    }
}

function editPost(id) {
    window.location.href = `post-editor.php?id=${id}`;
}

async function deletePost(id, title) {
    if (!confirm(`Are you sure you want to delete "${title}"?`)) return;
    
    try {
        const res = await App.api(`/posts/${id}`, 'DELETE');
        if (res && res.success) {
            allPosts = allPosts.filter(p => p.id !== id);
            renderPosts();
            App.showToast('Post deleted successfully', 'success');
        } else {
            App.showToast('Failed to delete post', 'error');
        }
    } catch (e) {
        App.showToast('Failed to delete post: ' + e.message, 'error');
    }
}

// Select all checkbox
document.getElementById('select-all')?.addEventListener('change', function() {
    document.querySelectorAll('.post-checkbox').forEach(cb => cb.checked = this.checked);
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
