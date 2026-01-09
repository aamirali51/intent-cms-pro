<?php
$title = 'Tags';
ob_start();
?>

<div id="tags-container">
    <!-- Loading State -->
    <div class="flex items-center justify-center h-64">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
    </div>
</div>

<script>
    // Tags Logic
    Object.assign(App, {
        defaultColors: [
            '#8b5cf6', '#ec4899', '#f59e0b', '#10b981', '#3b82f6', 
            '#ef4444', '#6366f1', '#14b8a6', '#f97316', '#84cc16'
        ],
        
        async renderTags() {
            const container = document.getElementById('tags-container');
            container.innerHTML = '<div class="flex items-center justify-center h-64"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div></div>';
            
            try {
                const response = await this.api('/tags');
                const tags = response.data || response || [];
                
                const rows = tags.length ? tags.map(t => `
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium text-white" style="background-color: ${t.color || '#6b7280'}">
                                ${t.name || 'Untitled'}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-500 dark:text-gray-400 font-mono text-sm">${t.slug || ''}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700/50 dark:text-gray-300">
                            ${t.count || 0} posts
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-500 dark:text-gray-400">${t.created_at ? new Date(t.created_at).toLocaleDateString() : '-'}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button onclick="App.editTag(${t.id})" class="p-1 text-gray-400 hover:text-primary transition-colors" title="Edit">
                                <span class="material-icons-round text-lg">edit</span>
                            </button>
                            <button onclick="App.deleteTag(${t.id}, this)" data-name="${(t.name || '').replace(/"/g, '&quot;')}" class="p-1 text-gray-400 hover:text-red-500 transition-colors" title="Delete">
                                <span class="material-icons-round text-lg">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('') : '';

                container.innerHTML = `
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">Tags</h2>
                        <nav class="flex text-sm text-gray-500"><a href="/admin/dashboard.php" class="hover:text-primary">Dashboard</a><span class="mx-2">/</span><span class="text-gray-800 dark:text-gray-200">Tags</span></nav>
                    </div>
                    <div class="mt-4 md:mt-0 flex gap-3">
                        <button onclick="App.editTag()" class="px-4 py-2 bg-primary hover:bg-primaryHover text-white rounded-lg shadow-lg shadow-primary/20 text-sm font-medium flex items-center transition-colors">
                            <span class="material-icons-round text-base mr-2">add</span>
                            Add New Tag
                        </button>
                    </div>
                </div>
                <div class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm overflow-hidden border border-border-light dark:border-border-dark">
                    ${tags.length ? `<div class="overflow-x-auto"><table class="min-w-full text-left text-sm"><thead class="bg-gray-50 dark:bg-gray-800/50 uppercase text-gray-500 font-medium border-b border-border-light dark:border-border-dark"><tr><th class="px-6 py-4">Tag</th><th class="px-6 py-4">Slug</th><th class="px-6 py-4">Posts</th><th class="px-6 py-4">Date</th><th class="px-6 py-4 text-right">Actions</th></tr></thead><tbody class="divide-y divide-border-light dark:divide-border-dark">${rows}</tbody></table></div>` : `<div class="p-12 text-center"><div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4"><span class="material-icons-round text-3xl text-gray-400">label</span></div><h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No tags yet</h3><p class="text-gray-500 dark:text-gray-400 mb-4">Create your first tag to organize content.</p><button onclick="App.editTag()" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium">Add Tag</button></div>`}
                </div>
            `;
            } catch (e) {
                container.innerHTML = '<div class="p-4 text-red-500">Error loading tags</div>';
                console.error('Error loading tags:', e);
            }
        },

        async editTag(id = null) {
            let tag = { name: '', slug: '', color: '#8b5cf6', description: '' };
            if (id) {
                try {
                    tag = await this.api(`/tags/${id}`);
                } catch (e) { 
                    this.showToast('Error loading tag', 'error'); 
                    return; 
                }
            }

            const colorPicker = this.defaultColors.map(c => 
                `<button type="button" onclick="document.getElementById('tag-color').value='${c}'; this.parentElement.querySelectorAll('button').forEach(b=>b.classList.remove('ring-2','ring-offset-2')); this.classList.add('ring-2','ring-offset-2','ring-gray-400');" class="w-8 h-8 rounded-full ${tag.color === c ? 'ring-2 ring-offset-2 ring-gray-400' : ''}" style="background-color: ${c}"></button>`
            ).join('');

            this.showModal({
                title: id ? 'Edit Tag' : 'Create New Tag',
                body: `
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name *</label>
                            <input type="text" id="tag-name" value="${tag.name || ''}" 
                                class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:ring-primary focus:border-primary"
                                placeholder="e.g., Technology">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Slug</label>
                            <input type="text" id="tag-slug" value="${tag.slug || ''}" 
                                class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:ring-primary focus:border-primary"
                                placeholder="e.g., technology (auto-generated if empty)">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Color</label>
                            <div class="flex items-center gap-2 mb-2">
                                <input type="color" id="tag-color" value="${tag.color || '#8b5cf6'}" 
                                    class="w-10 h-10 border-0 rounded cursor-pointer">
                                <span class="text-sm text-gray-500">or choose predefined:</span>
                            </div>
                            <div class="flex flex-wrap gap-2">${colorPicker}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                            <textarea id="tag-description" rows="2" 
                                class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:ring-primary focus:border-primary"
                                placeholder="Optional description for this tag">${tag.description || ''}</textarea>
                        </div>
                    </div>
                `,
                actions: [
                    { text: 'Cancel', onClick: () => this.closeModal() },
                    { text: id ? 'Update Tag' : 'Create Tag', class: 'mt-3 inline-flex w-full justify-center rounded-md bg-primary px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primaryHover sm:mt-0 sm:w-auto sm:ml-3', onClick: () => this.saveTag(id) }
                ]
            });
        },

        async saveTag(id) {
            const name = document.getElementById('tag-name').value.trim();
            const slug = document.getElementById('tag-slug').value.trim();
            const color = document.getElementById('tag-color').value;
            const description = document.getElementById('tag-description').value.trim();

            // Validation
            if (!name) {
                this.showToast('Tag name is required', 'error');
                return;
            }

            const data = { name, color };
            if (slug) data.slug = slug;
            if (description) data.description = description;

            try {
                const url = id ? `/tags/${id}` : '/tags';
                const method = id ? 'PUT' : 'POST';

                const res = await this.api(url, method, data);

                if (res && (res.id || res.message || res.success)) {
                    this.closeModal();
                    this.renderTags();
                    this.showToast(id ? 'Tag updated successfully!' : 'Tag created successfully!', 'success');
                } else if (res && res.error) {
                    this.showToast(res.error, 'error');
                } else {
                    this.showToast('Failed to save tag', 'error');
                }
            } catch (e) {
                this.showToast('Error saving tag: ' + e.message, 'error');
            }
        },

        async deleteTag(id, btn) {
            const name = btn.dataset.name || 'this tag';
            
            this.showModal({
                title: 'Delete Tag',
                body: `
                    <div class="text-center py-4">
                        <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="material-icons-round text-3xl text-red-500">warning</span>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Are you sure?</h3>
                        <p class="text-gray-500 dark:text-gray-400">
                            You are about to delete tag <strong class="text-gray-900 dark:text-white">"${name}"</strong>
                        </p>
                        <p class="text-sm text-gray-400 mt-2">Posts will not be deleted, only the tag association.</p>
                    </div>
                `,
                actions: [
                    { 
                        text: 'Delete Tag', 
                        class: 'mt-3 inline-flex w-full justify-center rounded-md px-3 py-2 text-sm font-semibold text-white shadow-sm sm:mt-0 sm:w-auto',
                        style: 'background-color: #dc2626; color: white;',
                        onClick: async () => {
                            this.closeModal();
                            try {
                                const res = await this.api(`/tags/${id}`, 'DELETE');
                                if (res && res.error) {
                                    this.showToast(res.error, 'error');
                                } else {
                                    this.renderTags();
                                    this.showToast('Tag deleted successfully', 'success');
                                }
                            } catch (e) {
                                this.showToast('Error deleting tag: ' + e.message, 'error');
                            }
                        },
                        close: false
                    },
                    { text: 'Cancel', class: 'mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 sm:mt-0 sm:w-auto sm:ml-3', onClick: () => this.closeModal() }
                ]
            });
        }
    });

    document.addEventListener('DOMContentLoaded', () => App.renderTags());
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
