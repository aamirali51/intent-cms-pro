<?php
$title = 'Pages';
ob_start();
?>
<?php include __DIR__ . '/includes/editorjs-init.php'; ?>

<div id="pages-container">
    <!-- Loading State -->
    <div class="flex items-center justify-center h-64">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
    </div>
</div>

<script>
    // Pages Logic
    Object.assign(App, {
        async renderPages() {
            const container = document.getElementById('pages-container');
            container.innerHTML = '<div class="flex items-center justify-center h-64"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div></div>';
            
            try {
                const pages = await this.api('/pages') || [];
                const rows = pages.length ? pages.map(p => `
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group">
                    <td class="px-6 py-4">
                        <input type="checkbox" class="rounded border-gray-300 text-primary focus:ring-primary bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-semibold text-gray-900 dark:text-white">${p.title || 'Untitled'}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">/${p.slug || ''}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${p.status === 'published' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400'}">
                            ${p.status || 'draft'}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-500 dark:text-gray-400">${p.created_at || '-'}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button onclick="App.editPage(${p.id})" class="p-1 text-gray-400 hover:text-primary transition-colors" title="Edit">
                                <span class="material-icons-round text-lg">edit</span>
                            </button>
                            <button onclick="App.deletePage(${p.id})" class="p-1 text-gray-400 hover:text-red-500 transition-colors" title="Delete">
                                <span class="material-icons-round text-lg">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('') : '';

                container.innerHTML = `
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">Pages</h2>
                        <nav class="flex text-sm text-gray-500"><a href="/admin/dashboard.php" class="hover:text-primary">Dashboard</a><span class="mx-2">/</span><span class="text-gray-800 dark:text-gray-200">Pages</span></nav>
                    </div>
                    <div class="mt-4 md:mt-0 flex gap-3">
                        <button onclick="App.editPage()" class="px-4 py-2 bg-primary hover:bg-primaryHover text-white rounded-lg shadow-lg shadow-primary/20 text-sm font-medium flex items-center transition-colors">
                            <span class="material-icons-round text-base mr-2">add</span>
                            Add New Page
                        </button>
                    </div>
                </div>
                <div class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm overflow-hidden border border-border-light dark:border-border-dark">
                    ${pages.length ? `<div class="overflow-x-auto"><table class="min-w-full text-left text-sm"><thead class="bg-gray-50 dark:bg-gray-800/50 uppercase text-gray-500 font-medium border-b border-border-light dark:border-border-dark"><tr><th class="px-6 py-4 w-12"><input type="checkbox" class="rounded border-gray-300 text-primary focus:ring-primary bg-gray-50 dark:bg-gray-700 dark:border-gray-600"></th><th class="px-6 py-4">Title</th><th class="px-6 py-4">Status</th><th class="px-6 py-4">Date</th><th class="px-6 py-4 text-right">Actions</th></tr></thead><tbody class="divide-y divide-border-light dark:divide-border-dark">${rows}</tbody></table></div>` : `<div class="p-12 text-center"><div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4"><span class="material-icons-round text-3xl text-gray-400">layers</span></div><h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No pages yet</h3><p class="text-gray-500 dark:text-gray-400 mb-4">Create your first page to get started.</p><button onclick="App.editPage()" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium">Create Page</button></div>`}
                </div>
            `;
            } catch (e) {
                container.innerHTML = '<div class="p-4 text-red-500">Error loading pages</div>';
            }
        },

        async editPage(id = null) {
            let page = { title: '', slug: '', status: 'draft', content: '', featured_image: '' };
            if (id) {
                try {
                    page = await this.api(`/pages/${id}`);
                    if (typeof page.content === 'string') {
                        try { page.content = JSON.parse(page.content); } catch (e) { page.content = {}; }
                    }
                } catch (e) { this.showToast('Error loading page', 'error'); return; }
            }

            this.showModal({
                title: id ? 'Edit Page' : 'Create New Page',
                body: `
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
                            <input type="text" id="page-title" value="${page.title || ''}" 
                                class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:ring-primary focus:border-primary"
                                oninput="if(!${id ? 'true' : 'false'}) document.getElementById('page-slug').value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '')">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Slug</label>
                                <input type="text" id="page-slug" value="${page.slug || ''}" placeholder="auto-generated" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-500 dark:text-gray-300 cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                                <select id="page-status" class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:ring-primary focus:border-primary">
                                    <option value="draft" ${page.status === 'draft' ? 'selected' : ''}>Draft</option>
                                    <option value="published" ${page.status === 'published' ? 'selected' : ''}>Published</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Content</label>
                            <div id="editorjs" class="bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg p-4 prose dark:prose-invert max-w-none" style="min-height: 500px;"></div>
                        </div>
                    </div>
                `,
                actions: [
                    { text: 'Cancel', onClick: () => this.closeModal() },
                    { text: id ? 'Update Page' : 'Save Page', class: 'inline-flex w-full justify-center rounded-md bg-primary px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primaryHover sm:w-auto sm:ml-3', onClick: () => this.savePage(id) }
                ]
            });

            setTimeout(() => this.initEditor(page.content), 100);
        },

        async savePage(id) {
            const title = document.getElementById('page-title').value;
            const slug = document.getElementById('page-slug').value;
            const status = document.getElementById('page-status').value;

            let contentRaw = {};
            if (this.editor) { contentRaw = await this.editor.save(); }
            const content = JSON.stringify(contentRaw);

            const data = { title, slug, status, content };

            try {
                const url = id ? `/pages/${id}` : '/pages';
                const method = id ? 'PUT' : 'POST';

                const res = await this.api(url, method, data);

                if (res && (res.id || res.message)) {
                    this.closeModal();
                    this.renderPages();
                    this.showToast(id ? 'Page updated successfully!' : 'Page created successfully!', 'success');
                } else {
                    this.showToast('Failed to save page', 'error');
                }
            } catch (e) {
                this.showToast('Error saving page: ' + e.message, 'error');
            }
        },

        async deletePage(id) {
            if (!confirm('Are you sure you want to delete this page?')) return;
            try {
                await this.api(`/pages/${id}`, 'DELETE');
                this.renderPages();
                this.showToast('Page deleted successfully', 'success');
            } catch (e) {
                this.showToast('Error deleting page: ' + e.message, 'error');
            }
        }
    });

    document.addEventListener('DOMContentLoaded', () => App.renderPages());
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
