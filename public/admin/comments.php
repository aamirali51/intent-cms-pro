<?php
$title = 'Comments';
ob_start();
?>

<div id="comments-container">
    <!-- Loading State -->
    <div class="flex items-center justify-center h-64">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
    </div>
</div>

<script>
    // Comments Logic
    Object.assign(App, {
        currentStatus: 'all',
        commentCounts: { all: 0, pending: 0, approved: 0, spam: 0, trash: 0 },
        selectedComments: [],
        
        getStatusBadgeClass(status) {
            const classes = {
                pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                approved: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                spam: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                trash: 'bg-gray-100 text-gray-800 dark:bg-gray-700/50 dark:text-gray-400'
            };
            return classes[status] || classes.pending;
        },

        async renderComments(status = null) {
            if (status !== null) this.currentStatus = status;
            
            const container = document.getElementById('comments-container');
            container.innerHTML = '<div class="flex items-center justify-center h-64"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div></div>';
            
            try {
                const url = this.currentStatus === 'all' ? '/comments' : `/comments?status=${this.currentStatus}`;
                const response = await this.api(url);
                const comments = response.data || [];
                this.commentCounts = response.counts || this.commentCounts;
                this.selectedComments = [];
                
                const rows = comments.length ? comments.map(c => `
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group ${c.status === 'pending' ? 'bg-yellow-50 dark:bg-yellow-900/10' : ''}">
                    <td class="px-4 py-4">
                        <input type="checkbox" data-id="${c.id}" class="comment-checkbox w-4 h-4 text-primary rounded border-gray-300 dark:border-gray-600 focus:ring-primary" onchange="App.toggleCommentSelection(${c.id})">
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-start space-x-3">
                            <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-500 dark:text-gray-400 font-medium">
                                ${(c.author_name || 'A').charAt(0).toUpperCase()}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-2 mb-1">
                                    <span class="font-semibold text-gray-900 dark:text-white">${c.author_name || 'Anonymous'}</span>
                                    ${c.user_name ? '<span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-primary/10 text-primary">Staff</span>' : ''}
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-2">${this.escapeHtml(c.content || '')}</p>
                                <div class="mt-2 flex items-center space-x-3 text-xs text-gray-500">
                                    <a href="/admin/post-editor.php?id=${c.content_id}" class="hover:text-primary flex items-center">
                                        <span class="material-icons-round text-sm mr-1">article</span>
                                        ${c.post_title || 'Unknown Post'}
                                    </a>
                                    <span>${c.created_at ? new Date(c.created_at).toLocaleString() : '-'}</span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize ${this.getStatusBadgeClass(c.status)}">
                            ${c.status || 'pending'}
                        </span>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            ${c.status !== 'approved' ? `<button onclick="App.moderateComment(${c.id}, 'approved')" class="p-1.5 text-green-500 hover:bg-green-100 dark:hover:bg-green-900/30 rounded transition-colors" title="Approve"><span class="material-icons-round text-lg">check_circle</span></button>` : ''}
                            ${c.status !== 'spam' ? `<button onclick="App.moderateComment(${c.id}, 'spam')" class="p-1.5 text-orange-500 hover:bg-orange-100 dark:hover:bg-orange-900/30 rounded transition-colors" title="Spam"><span class="material-icons-round text-lg">report</span></button>` : ''}
                            ${c.status !== 'trash' ? `<button onclick="App.moderateComment(${c.id}, 'trash')" class="p-1.5 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded transition-colors" title="Trash"><span class="material-icons-round text-lg">delete</span></button>` : ''}
                            ${c.status === 'trash' ? `<button onclick="App.moderateComment(${c.id}, 'pending')" class="p-1.5 text-blue-500 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded transition-colors" title="Restore"><span class="material-icons-round text-lg">restore</span></button>` : ''}
                            <button onclick="App.replyToComment(${c.id}, '${(c.author_name || '').replace(/'/g, "\\'")}')" class="p-1.5 text-primary hover:bg-primary/10 rounded transition-colors" title="Reply">
                                <span class="material-icons-round text-lg">reply</span>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('') : '';

                container.innerHTML = `
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">Comments</h2>
                        <nav class="flex text-sm text-gray-500"><a href="/admin/dashboard.php" class="hover:text-primary">Dashboard</a><span class="mx-2">/</span><span class="text-gray-800 dark:text-gray-200">Comments</span></nav>
                    </div>
                </div>
                
                <!-- Status Tabs -->
                <div class="flex items-center space-x-1 mb-4 border-b border-gray-200 dark:border-gray-700">
                    ${['all', 'pending', 'approved', 'spam', 'trash'].map(s => `
                        <button onclick="App.renderComments('${s}')" class="px-4 py-2 text-sm font-medium ${this.currentStatus === s ? 'text-primary border-b-2 border-primary' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400'} capitalize">
                            ${s} <span class="ml-1 text-xs ${s === 'pending' && this.commentCounts.pending > 0 ? 'bg-yellow-500 text-white px-1.5 py-0.5 rounded-full' : 'text-gray-400'}">${this.commentCounts[s] || 0}</span>
                        </button>
                    `).join('')}
                </div>

                <!-- Bulk Actions -->
                <div id="bulk-actions" class="hidden items-center space-x-3 mb-4 p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                    <span class="text-sm text-gray-600 dark:text-gray-300"><span id="selected-count">0</span> selected</span>
                    <button onclick="App.bulkModerate('approve')" class="px-3 py-1.5 text-sm bg-green-500 text-white rounded hover:bg-green-600">Approve</button>
                    <button onclick="App.bulkModerate('spam')" class="px-3 py-1.5 text-sm bg-orange-500 text-white rounded hover:bg-orange-600">Mark Spam</button>
                    <button onclick="App.bulkModerate('trash')" class="px-3 py-1.5 text-sm bg-gray-500 text-white rounded hover:bg-gray-600">Trash</button>
                    ${this.currentStatus === 'trash' ? '<button onclick="App.bulkModerate(\'delete\')" class="px-3 py-1.5 text-sm bg-red-500 text-white rounded hover:bg-red-600">Delete Forever</button>' : ''}
                </div>

                <div class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm overflow-hidden border border-border-light dark:border-border-dark">
                    ${comments.length ? `<div class="overflow-x-auto"><table class="min-w-full text-left text-sm"><thead class="bg-gray-50 dark:bg-gray-800/50 uppercase text-gray-500 font-medium border-b border-border-light dark:border-border-dark"><tr><th class="px-4 py-3 w-10"><input type="checkbox" id="select-all" class="w-4 h-4 text-primary rounded" onchange="App.toggleAllComments()"></th><th class="px-4 py-3">Comment</th><th class="px-4 py-3">Status</th><th class="px-4 py-3 w-32">Actions</th></tr></thead><tbody class="divide-y divide-border-light dark:divide-border-dark">${rows}</tbody></table></div>` : `<div class="p-12 text-center"><div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4"><span class="material-icons-round text-3xl text-gray-400">comment</span></div><h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No ${this.currentStatus !== 'all' ? this.currentStatus + ' ' : ''}comments</h3><p class="text-gray-500 dark:text-gray-400">Comments will appear here when users interact with your posts.</p></div>`}
                </div>
            `;
            } catch (e) {
                container.innerHTML = '<div class="p-4 text-red-500">Error loading comments</div>';
                console.error('Error loading comments:', e);
            }
        },

        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        },

        toggleCommentSelection(id) {
            const idx = this.selectedComments.indexOf(id);
            if (idx === -1) {
                this.selectedComments.push(id);
            } else {
                this.selectedComments.splice(idx, 1);
            }
            this.updateBulkActionsVisibility();
        },

        toggleAllComments() {
            const selectAll = document.getElementById('select-all');
            const checkboxes = document.querySelectorAll('.comment-checkbox');
            
            this.selectedComments = [];
            checkboxes.forEach(cb => {
                cb.checked = selectAll.checked;
                if (selectAll.checked) {
                    this.selectedComments.push(parseInt(cb.dataset.id));
                }
            });
            this.updateBulkActionsVisibility();
        },

        updateBulkActionsVisibility() {
            const bulkActions = document.getElementById('bulk-actions');
            const selectedCount = document.getElementById('selected-count');
            if (this.selectedComments.length > 0) {
                bulkActions.classList.remove('hidden');
                bulkActions.classList.add('flex');
                selectedCount.textContent = this.selectedComments.length;
            } else {
                bulkActions.classList.add('hidden');
                bulkActions.classList.remove('flex');
            }
        },

        async moderateComment(id, status) {
            try {
                const res = await this.api(`/comments/${id}/status`, 'PUT', { status });
                if (res && res.success) {
                    this.showToast(res.message || `Comment ${status}`, 'success');
                    this.renderComments();
                } else {
                    this.showToast(res.error || 'Failed to moderate comment', 'error');
                }
            } catch (e) {
                this.showToast('Error: ' + e.message, 'error');
            }
        },

        async bulkModerate(action) {
            if (this.selectedComments.length === 0) return;
            
            try {
                const res = await this.api('/comments/bulk', 'POST', {
                    ids: this.selectedComments,
                    action: action
                });
                
                if (res && res.success) {
                    this.showToast(res.message, 'success');
                    this.renderComments();
                } else {
                    this.showToast(res.error || 'Failed to perform bulk action', 'error');
                }
            } catch (e) {
                this.showToast('Error: ' + e.message, 'error');
            }
        },

        replyToComment(id, authorName) {
            this.showModal({
                title: `Reply to ${authorName}`,
                body: `
                    <div class="space-y-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Your reply will be posted immediately as an approved comment.</p>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Your Reply</label>
                            <textarea id="reply-content" rows="4" 
                                class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:ring-primary focus:border-primary"
                                placeholder="Write your reply..."></textarea>
                        </div>
                    </div>
                `,
                actions: [
                    { text: 'Cancel', onClick: () => this.closeModal() },
                    { 
                        text: 'Post Reply', 
                        class: 'mt-3 inline-flex w-full justify-center rounded-md bg-primary px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primaryHover sm:mt-0 sm:w-auto sm:ml-3', 
                        onClick: () => this.submitReply(id) 
                    }
                ]
            });
        },

        async submitReply(parentId) {
            const content = document.getElementById('reply-content').value.trim();
            if (!content) {
                this.showToast('Reply content is required', 'error');
                return;
            }

            try {
                const res = await this.api(`/comments/${parentId}/reply`, 'POST', { content });
                
                if (res && res.id) {
                    this.closeModal();
                    this.showToast('Reply posted successfully!', 'success');
                    this.renderComments();
                } else {
                    this.showToast(res.error || 'Failed to post reply', 'error');
                }
            } catch (e) {
                this.showToast('Error: ' + e.message, 'error');
            }
        }
    });

    document.addEventListener('DOMContentLoaded', () => App.renderComments());
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
