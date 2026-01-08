<?php
$title = 'Users';
ob_start();
?>

<div id="users-container">
    <!-- Loading State -->
    <div class="flex items-center justify-center h-64">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
    </div>
</div>

<script>
    // Users Logic
    Object.assign(App, {
        userRoles: ['admin', 'editor', 'author', 'user'],
        
        getRoleBadgeClass(role) {
            const classes = {
                admin: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                editor: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                author: 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
                user: 'bg-gray-100 text-gray-800 dark:bg-gray-700/50 dark:text-gray-400'
            };
            return classes[role] || classes.user;
        },

        async renderUsers() {
            const container = document.getElementById('users-container');
            container.innerHTML = '<div class="flex items-center justify-center h-64"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div></div>';
            
            try {
                const response = await this.api('/users');
                const users = response.data || response || [];
                
                const rows = users.length ? users.map(u => `
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-medium">
                                ${u.avatar ? `<img src="${u.avatar}" class="w-10 h-10 rounded-full object-cover" alt="${u.name}">` : (u.name || 'U').charAt(0).toUpperCase()}
                            </div>
                            <div class="ml-3">
                                <span class="font-semibold text-gray-900 dark:text-white block">${u.name || 'Unknown'}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">${u.email || ''}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize ${this.getRoleBadgeClass(u.role)}">
                            ${u.role || 'user'}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-500 dark:text-gray-400">${u.created_at ? new Date(u.created_at).toLocaleDateString() : '-'}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button onclick="App.editUser(${u.id})" class="p-1 text-gray-400 hover:text-primary transition-colors" title="Edit">
                                <span class="material-icons-round text-lg">edit</span>
                            </button>
                            <button onclick="App.deleteUser(${u.id}, '${(u.name || '').replace(/'/g, "\\'")}', '${u.email || ''}')" class="p-1 text-gray-400 hover:text-red-500 transition-colors" title="Delete">
                                <span class="material-icons-round text-lg">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('') : '';

                container.innerHTML = `
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">Users</h2>
                        <nav class="flex text-sm text-gray-500"><a href="/admin/dashboard.php" class="hover:text-primary">Dashboard</a><span class="mx-2">/</span><span class="text-gray-800 dark:text-gray-200">Users</span></nav>
                    </div>
                    <div class="mt-4 md:mt-0 flex gap-3">
                        <button onclick="App.editUser()" class="px-4 py-2 bg-primary hover:bg-primaryHover text-white rounded-lg shadow-lg shadow-primary/20 text-sm font-medium flex items-center transition-colors">
                            <span class="material-icons-round text-base mr-2">person_add</span>
                            Add New User
                        </button>
                    </div>
                </div>
                <div class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm overflow-hidden border border-border-light dark:border-border-dark">
                    ${users.length ? `<div class="overflow-x-auto"><table class="min-w-full text-left text-sm"><thead class="bg-gray-50 dark:bg-gray-800/50 uppercase text-gray-500 font-medium border-b border-border-light dark:border-border-dark"><tr><th class="px-6 py-4">User</th><th class="px-6 py-4">Role</th><th class="px-6 py-4">Date</th><th class="px-6 py-4 text-right">Actions</th></tr></thead><tbody class="divide-y divide-border-light dark:divide-border-dark">${rows}</tbody></table></div>` : `<div class="p-12 text-center"><div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4"><span class="material-icons-round text-3xl text-gray-400">people</span></div><h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No users yet</h3><p class="text-gray-500 dark:text-gray-400 mb-4">Add your first user to get started.</p><button onclick="App.editUser()" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium">Add User</button></div>`}
                </div>
            `;
            } catch (e) {
                container.innerHTML = '<div class="p-4 text-red-500">Error loading users</div>';
                console.error('Error loading users:', e);
            }
        },

        async editUser(id = null) {
            let user = { name: '', email: '', role: 'user', avatar: '', password: '' };
            if (id) {
                try {
                    user = await this.api(`/users/${id}`);
                } catch (e) { 
                    this.showToast('Error loading user', 'error'); 
                    return; 
                }
            }

            const roleOptions = this.userRoles.map(r => 
                `<option value="${r}" ${user.role === r ? 'selected' : ''}>${r.charAt(0).toUpperCase() + r.slice(1)}</option>`
            ).join('');

            this.showModal({
                title: id ? 'Edit User' : 'Create New User',
                body: `
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name *</label>
                                <input type="text" id="user-name" value="${user.name || ''}" 
                                    class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:ring-primary focus:border-primary"
                                    placeholder="Full name">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email *</label>
                                <input type="email" id="user-email" value="${user.email || ''}" 
                                    class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:ring-primary focus:border-primary"
                                    placeholder="user@example.com">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Role</label>
                                <select id="user-role" class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:ring-primary focus:border-primary">
                                    ${roleOptions}
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">${id ? 'New Password (leave blank to keep current)' : 'Password *'}</label>
                                <input type="password" id="user-password" 
                                    class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:ring-primary focus:border-primary"
                                    placeholder="${id ? '••••••••' : 'Min 6 characters'}">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Avatar URL (optional)</label>
                            <input type="text" id="user-avatar" value="${user.avatar || ''}" 
                                class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:ring-primary focus:border-primary"
                                placeholder="https://example.com/avatar.jpg">
                        </div>
                    </div>
                `,
                actions: [
                    { text: 'Cancel', onClick: () => this.closeModal() },
                    { text: id ? 'Update User' : 'Create User', class: 'inline-flex w-full justify-center rounded-md bg-primary px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primaryHover sm:w-auto sm:ml-3', onClick: () => this.saveUser(id) }
                ]
            });
        },

        async saveUser(id) {
            const name = document.getElementById('user-name').value.trim();
            const email = document.getElementById('user-email').value.trim();
            const role = document.getElementById('user-role').value;
            const password = document.getElementById('user-password').value;
            const avatar = document.getElementById('user-avatar').value.trim();

            // Validation
            if (!name) {
                this.showToast('Name is required', 'error');
                return;
            }
            if (!email || !email.includes('@')) {
                this.showToast('Valid email is required', 'error');
                return;
            }
            if (!id && (!password || password.length < 6)) {
                this.showToast('Password must be at least 6 characters', 'error');
                return;
            }

            const data = { name, email, role };
            if (avatar) data.avatar = avatar;
            if (password) data.password = password;

            try {
                const url = id ? `/users/${id}` : '/users';
                const method = id ? 'PUT' : 'POST';

                const res = await this.api(url, method, data);

                if (res && (res.id || res.message || res.success)) {
                    this.closeModal();
                    this.renderUsers();
                    this.showToast(id ? 'User updated successfully!' : 'User created successfully!', 'success');
                } else if (res && res.error) {
                    this.showToast(res.error, 'error');
                } else {
                    this.showToast('Failed to save user', 'error');
                }
            } catch (e) {
                this.showToast('Error saving user: ' + e.message, 'error');
            }
        },

        async deleteUser(id, name, email) {
            if (!confirm(`Are you sure you want to delete user "${name}" (${email})?`)) return;
            try {
                const res = await this.api(`/users/${id}`, 'DELETE');
                if (res && res.error) {
                    this.showToast(res.error, 'error');
                } else {
                    this.renderUsers();
                    this.showToast('User deleted successfully', 'success');
                }
            } catch (e) {
                this.showToast('Error deleting user: ' + e.message, 'error');
            }
        }
    });

    document.addEventListener('DOMContentLoaded', () => App.renderUsers());
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
