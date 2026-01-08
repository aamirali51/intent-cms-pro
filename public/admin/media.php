<?php
$title = 'Media Library';
ob_start();
?>

<div id="media-container" class="h-[calc(100vh-8rem)] flex flex-col">
    <!-- Media Content will be rendered here -->
    <div class="flex items-center justify-center h-full">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
    </div>
</div>

<script>
    // Media Logic
    Object.assign(App, {
        // Media State
        customFile: null,
        selectedFile: null,
        selectedFileIds: new Set(),
        mediaQuery: { type: 'all', search: '', sort: 'created_at', order: 'desc' },
        viewMode: 'grid',
        currentFolderId: null,
        currentFolderPath: [],
        folders: [],
        _pendingResolve: null,

        async renderMedia() {
            const container = document.getElementById('media-container');
            const page = this.mediaQuery?.page || 1;
            const type = this.mediaQuery?.type || 'all';
            const search = this.mediaQuery?.search || '';
            const sort = this.mediaQuery?.sort || 'created_at';
            const order = this.mediaQuery?.order || 'desc';

            // Load folder path
            await this.loadFolderPath(this.currentFolderId);

            try {
                const [mediaRes, foldersRes] = await Promise.all([
                    this.api(`/media?page=${page}&type=${type}&search=${search}&folder_id=${this.currentFolderId || ''}&sort=${sort}&order=${order}`),
                    this.api(`/media/folders?parent_id=${this.currentFolderId || ''}`)
                ]);

                const items = mediaRes.data || (Array.isArray(mediaRes) ? mediaRes : []);
                const meta = mediaRes.meta || { page: 1, last_page: 1, total: items.length };
                this.mediaMeta = meta;

                const folders = foldersRes || [];
                this.folders = folders;

                // Grid Folders
                const gridFolders = folders.map(folder => `
                    <div class="relative group cursor-pointer border rounded-lg overflow-hidden bg-white dark:bg-surface-dark border-border-light dark:border-border-dark aspect-square hover:shadow-md transition-all flex flex-col items-center justify-center"
                        onclick="App.navigateToFolder(${folder.id})">
                        <span class="material-icons-round text-5xl text-yellow-400 mb-2">folder</span>
                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 px-2 text-center truncate w-full">${folder.name}</span>
                        
                        <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity flex gap-1" onclick="event.stopPropagation()">
                            <button onclick="App.renameFolder(${folder.id}, '${folder.name.replace(/'/g, "\\'")}')" class="p-1 bg-white dark:bg-gray-700 rounded shadow hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300">
                                <span class="material-icons-round text-sm">edit</span>
                            </button>
                            <button onclick="App.deleteFolder(${folder.id})" class="p-1 bg-white dark:bg-gray-700 rounded shadow hover:bg-red-50 text-red-500">
                                <span class="material-icons-round text-sm">delete</span>
                            </button>
                        </div>
                    </div>
                `).join('');

                const upButton = this.currentFolderId ? `
                     <div class="relative group cursor-pointer border rounded-lg overflow-hidden bg-gray-50 dark:bg-surface-dark border-border-light dark:border-border-dark aspect-square hover:shadow-md transition-all flex flex-col items-center justify-center border-dashed"
                        onclick="App.navigateUp()">
                        <span class="material-icons-round text-4xl text-gray-400 mb-2">arrow_upward</span>
                        <span class="text-xs font-semibold text-gray-500">Back</span>
                    </div>
                ` : '';

                const gridItems = items.length ? items.map(item => `
                    <div id="media-item-${item.id}" class="relative group cursor-pointer border rounded-lg overflow-hidden bg-white dark:bg-surface-dark border-border-light dark:border-border-dark aspect-square hover:shadow-md transition-all ${this.selectedFile?.id === item.id ? 'ring-2 ring-primary border-primary' : ''} ${this.selectedFileIds.has(item.id) ? 'ring-2 ring-blue-500 border-blue-500' : ''}" onclick="App.selectFile(${JSON.stringify(item).replace(/"/g, '&quot;')})">
                         ${this.isImage(item)
                        ? `<img src="${this.getThumb(item, 'small')}" class="w-full h-full object-cover" alt="${item.alt_text || 'Media'}" loading="lazy">`
                        : `<div class="w-full h-full flex flex-col items-center justify-center bg-gray-50 dark:bg-gray-800 text-gray-400">
                                <span class="material-icons-round text-4xl mb-2">description</span>
                                <span class="text-xs uppercase font-bold">${item.filename.split('.').pop()}</span>
                               </div>`
                    }
                        <div class="absolute top-2 left-2 z-10 transition-opacity ${this.selectedFileIds.has(item.id) ? 'opacity-100' : 'opacity-0 group-hover:opacity-100'}" onclick="event.stopPropagation()">
                            <input type="checkbox" id="media-check-${item.id}"
                                ${this.selectedFileIds.has(item.id) ? 'checked' : ''} 
                                onchange="App.toggleFileSelection(${item.id}, event)"
                                class="w-5 h-5 rounded border-gray-300 text-primary focus:ring-primary shadow-sm cursor-pointer block">
                        </div>
                        <div class="absolute inset-0 bg-black/50 opacity-0 transition-opacity flex items-center justify-center text-white ${this.selectedFile?.id === item.id ? 'opacity-100' : ''} pointer-events-none">
                            ${this.selectedFile?.id === item.id ? '<span class="material-icons-round">check_circle</span>' : ''}
                        </div>
                    </div>
                `).join('') : '';

                const finalGrid = upButton + gridFolders + gridItems;

                // List View
                const listFolders = folders.map(folder => `
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors cursor-pointer" onclick="App.navigateToFolder(${folder.id})">
                         <td class="px-6 py-4 w-12"></td>
                         <td class="px-6 py-4 w-16">
                             <div class="w-10 h-10 bg-yellow-50 dark:bg-yellow-900/20 rounded overflow-hidden flex items-center justify-center">
                                <span class="material-icons-round text-yellow-400 text-2xl">folder</span>
                             </div>
                         </td>
                         <td class="px-6 py-4 font-medium text-gray-900 dark:text-white" colspan="2">${folder.name}</td>
                         <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2" onclick="event.stopPropagation()">
                                <button onclick="App.renameFolder(${folder.id}, '${folder.name.replace(/'/g, "\\'")}')" class="text-gray-400 hover:text-primary">
                                    <span class="material-icons-round text-lg">edit</span>
                                </button>
                                <button onclick="App.deleteFolder(${folder.id})" class="text-gray-400 hover:text-red-500">
                                    <span class="material-icons-round text-lg">delete</span>
                                </button>
                            </div>
                         </td>
                    </tr>
                `).join('');

                const upRow = this.currentFolderId ? `
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors cursor-pointer" onclick="App.navigateUp()">
                         <td class="px-6 py-4 w-12"></td>
                         <td class="px-6 py-4 w-16">
                             <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded overflow-hidden flex items-center justify-center">
                                <span class="material-icons-round text-gray-400 text-2xl">arrow_upward</span>
                             </div>
                         </td>
                         <td class="px-6 py-4 font-medium text-gray-500" colspan="3">Back</td>
                         <td class="px-6 py-4"></td>
                    </tr>
                ` : '';

                const listItems = items.length ? items.map(item => `
                    <tr id="media-row-${item.id}" class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors cursor-pointer ${this.selectedFile?.id === item.id ? 'bg-primary/5' : ''} ${this.selectedFileIds.has(item.id) ? 'bg-blue-50 dark:bg-blue-900/20' : ''}" onclick="App.selectFile(${JSON.stringify(item).replace(/"/g, '&quot;')})">
                        <td class="px-6 py-4 w-12" onclick="event.stopPropagation()">
                             <input type="checkbox" id="media-row-check-${item.id}"
                                ${this.selectedFileIds.has(item.id) ? 'checked' : ''}
                                onchange="App.toggleFileSelection(${item.id}, event)"
                                class="rounded border-gray-300 text-primary focus:ring-primary bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                        </td>
                        <td class="px-6 py-4 w-16">
                            <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded overflow-hidden flex items-center justify-center">
                                ${this.isImage(item) ? `<img src="${this.getThumb(item, 'small')}" class="w-full h-full object-cover" loading="lazy">` : `<span class="material-icons-round text-gray-400">description</span>`}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-medium text-gray-900 dark:text-white">${item.filename}</span>
                        </td>
                        <td class="px-6 py-4 text-gray-500 text-xs uppercase">${item.filename.split('.').pop()}</td>
                        <td class="px-6 py-4 text-gray-500 text-xs">${this.formatSize(item.size)}</td>
                        <td class="px-6 py-4 text-gray-500 text-xs">${new Date(item.created_at).toLocaleDateString()}</td>
                    </tr>
                `).join('') : '';

                const finalList = upRow + listFolders + listItems;

                container.innerHTML = `
                <div class="flex flex-col md:flex-row gap-6 h-full">
                    <!-- Main Area -->
                    <div class="flex-1 flex flex-col min-w-0 h-full">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Media Library</h2>
                                <!-- Breadcrumbs -->
                                <nav class="flex items-center text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    <button onclick="App.currentFolderId = null; App.renderMedia()" class="hover:text-primary flex items-center">
                                        <span class="material-icons-round text-sm mr-1">home</span>Root
                                    </button>
                                    ${this.currentFolderPath.map(f => `
                                        <span class="mx-1.5">/</span>
                                        <button onclick="App.navigateToFolder(${f.id})" class="hover:text-primary">${f.name}</button>
                                    `).join('')}
                                </nav>
                            </div>
                            
                            <div class="flex gap-2">
                                <div class="bg-surface-light dark:bg-surface-dark border border-border-light dark:border-border-dark rounded-lg flex p-1">
                                    <button onclick="App.toggleViewMode()" class="p-1 rounded ${this.viewMode === 'grid' ? 'bg-gray-100 dark:bg-gray-700 text-primary shadow-sm' : 'text-gray-400 hover:text-gray-600'}">
                                        <span class="material-icons-round text-xl">grid_view</span>
                                    </button>
                                    <button onclick="App.toggleViewMode()" class="p-1 rounded ${this.viewMode === 'list' ? 'bg-gray-100 dark:bg-gray-700 text-primary shadow-sm' : 'text-gray-400 hover:text-gray-600'}">
                                        <span class="material-icons-round text-xl">view_list</span>
                                    </button>
                                </div>
                                
                                <div id="media-actions" class="flex gap-2">
                                    ${this.selectedFileIds.size > 0 ? `
                                        <button onclick="App.deleteSelectedFiles()" class="px-4 py-2 bg-red-100 text-red-700 hover:bg-red-200 rounded-lg text-sm font-medium flex items-center transition-colors">
                                            <span class="material-icons-round text-base mr-2">delete</span>
                                            Delete Selected (${this.selectedFileIds.size})
                                        </button>
                                        <button onclick="App.moveSelectedFiles()" class="px-4 py-2 bg-blue-100 text-blue-700 hover:bg-blue-200 rounded-lg text-sm font-medium flex items-center transition-colors">
                                            <span class="material-icons-round text-base mr-2">drive_file_move</span>
                                            Move Selected
                                        </button>
                                        <button onclick="App.selectedFileIds.clear(); App.renderMedia()" class="px-4 py-2 text-gray-500 hover:text-gray-700 font-medium text-sm">Cancel</button>
                                    ` : `
                                        <label class="px-4 py-2 bg-primary hover:bg-primaryHover text-white rounded-lg shadow-lg shadow-primary/20 text-sm font-medium flex items-center transition-colors cursor-pointer">
                                            <span class="material-icons-round text-base mr-2">cloud_upload</span>
                                            Upload New
                                            <input type="file" class="hidden" multiple onchange="App.uploadFiles(this.files)">
                                        </label>
                                        
                                        <button onclick="App.createNewFolder()" class="px-4 py-2 bg-white dark:bg-surface-dark border border-border-light dark:border-border-dark text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-sm font-medium flex items-center transition-colors">
                                            <span class="material-icons-round text-base mr-2">create_new_folder</span>
                                            New Folder
                                        </button>
                                    `}
                                </div>
                            </div>
                        </div>

                        <!-- Filters -->
                        <div class="bg-surface-light dark:bg-surface-dark p-4 rounded-xl border border-border-light dark:border-border-dark mb-6 flex flex-wrap gap-4 items-center">
                            <div class="flex-1 relative">
                                <span class="material-icons-round absolute left-3 top-2.5 text-gray-400 text-lg">search</span>
                                <input type="text" placeholder="Search media..." value="${search}" 
                                    oninput="App.filterMedia('search', this.value)"
                                    class="w-full pl-9 pr-4 py-2 text-sm bg-gray-50 dark:bg-gray-800 border-none rounded-lg text-gray-900 dark:text-gray-200">
                            </div>
                            <select onchange="App.filterMedia('type', this.value)" class="py-2 pl-3 pr-8 text-sm bg-gray-50 dark:bg-gray-800 border-none rounded-lg text-gray-900 dark:text-gray-200">
                                <option value="all" ${type === 'all' ? 'selected' : ''}>All Types</option>
                                <option value="image" ${type === 'image' ? 'selected' : ''}>Images</option>
                                <option value="application" ${type === 'application' ? 'selected' : ''}>Documents</option>
                            </select>
                            <select onchange="const [s,o] = this.value.split(':'); App.setSort(s, o)" class="py-2 pl-3 pr-8 text-sm bg-gray-50 dark:bg-gray-800 border-none rounded-lg text-gray-900 dark:text-gray-200">
                                <option value="created_at:desc" ${sort === 'created_at' && order === 'desc' ? 'selected' : ''}>Newest First</option>
                                <option value="created_at:asc" ${sort === 'created_at' && order === 'asc' ? 'selected' : ''}>Oldest First</option>
                                <option value="filename:asc" ${sort === 'filename' && order === 'asc' ? 'selected' : ''}>Name A-Z</option>
                                <option value="filename:desc" ${sort === 'filename' && order === 'desc' ? 'selected' : ''}>Name Z-A</option>
                                <option value="size:desc" ${sort === 'size' && order === 'desc' ? 'selected' : ''}>Largest First</option>
                                <option value="size:asc" ${sort === 'size' && order === 'asc' ? 'selected' : ''}>Smallest First</option>
                            </select>
                        </div>

                        <div class="flex-1 overflow-y-auto min-h-0">
                        ${this.viewMode === 'grid' ? `
                            <!-- Upload Zone -->
                            <div class="border-2 border-dashed border-border-light dark:border-border-dark rounded-xl p-8 text-center bg-gray-50/50 dark:bg-gray-800/20 mb-6 transition-colors hover:bg-primary/5 hover:border-primary/50"
                                ondragover="event.preventDefault(); this.classList.add('border-primary', 'bg-primary/5')"
                                ondragleave="this.classList.remove('border-primary', 'bg-primary/5')"
                                ondrop="event.preventDefault(); this.classList.remove('border-primary', 'bg-primary/5'); App.uploadFiles(event.dataTransfer.files)">
                                <div class="text-primary mb-2">
                                    <span class="material-icons-round text-4xl">cloud_upload</span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-300 font-medium">Click to upload or drag and drop</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">SVG, PNG, JPG or GIF (max. 5MB)</p>
                            </div>

                            <!-- Grid -->
                            <div class="pr-2 mb-6">
                                ${(items.length || folders.length || this.currentFolderId) ? `
                                    <div class="grid gap-4" style="grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));">
                                        ${finalGrid}
                                    </div>
                                ` : `
                                    <div class="text-center py-12">
                                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <span class="material-icons-round text-3xl text-gray-400">image_not_supported</span>
                                        </div>
                                        <p class="text-gray-500 dark:text-gray-400">No media found.</p>
                                    </div>
                                `}
                            </div>
                        ` : `
                            <!-- List View -->
                            <div class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm overflow-hidden border border-border-light dark:border-border-dark mb-6">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full text-left text-sm">
                                        <thead class="bg-gray-50 dark:bg-gray-800/50 uppercase text-gray-500 font-medium border-b border-border-light dark:border-border-dark">
                                            <tr>
                                                <th class="px-6 py-4 w-12"></th>
                                                <th class="px-6 py-4 w-16">Preview</th>
                                                <th class="px-6 py-4">Name</th>
                                                <th class="px-6 py-4">Type</th>
                                                <th class="px-6 py-4">Size</th>
                                                <th class="px-6 py-4">Date</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-border-light dark:divide-border-dark">
                                            ${finalList}
                                        </tbody>
                                    </table>
                                </div>
                                ${(items.length || folders.length || this.currentFolderId) ? '' : `<div class="p-8 text-center text-gray-500">No media found.</div>`}
                            </div>
                        `}
                        </div>

                        <!-- Pagination -->
                        ${meta.last_page > 1 ? `
                            <div class="flex items-center justify-between border-t border-border-light dark:border-border-dark pt-4 mt-auto">
                                <p class="text-sm text-gray-500">
                                    Showing <span class="font-medium text-gray-900 dark:text-white">${((meta.page - 1) * meta.limit) + 1}</span> 
                                    to <span class="font-medium text-gray-900 dark:text-white">${Math.min(meta.page * meta.limit, meta.total)}</span> 
                                    of <span class="font-medium text-gray-900 dark:text-white">${meta.total}</span> results
                                </p>
                                <div class="flex gap-2">
                                    <button ${meta.page <= 1 ? 'disabled' : ''} onclick="App.paginate(${meta.page - 1})" 
                                        class="px-3 py-1 border border-border-light dark:border-border-dark rounded-lg text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                        Previous
                                    </button>
                                    <button ${meta.page >= meta.last_page ? 'disabled' : ''} onclick="App.paginate(${meta.page + 1})" 
                                        class="px-3 py-1 border border-border-light dark:border-border-dark rounded-lg text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                        Next
                                    </button>
                                </div>
                            </div>
                        ` : ''}
                    </div>

                    <!-- Sidebar -->
                    <div id="media-sidebar" class="w-80 max-w-xs flex-shrink-0 bg-surface-light dark:bg-surface-dark border border-border-light dark:border-border-dark rounded-xl p-4 flex flex-col h-full overflow-y-auto ${!this.selectedFile ? 'hidden' : ''}" style="width: 20rem;">
                        ${this.renderSidebarContent(this.selectedFile)}
                    </div>
                </div>
            `;
            } catch (e) {
                console.error(e);
                container.innerHTML = `<div class="p-4 text-red-500">Error loading media: ${e.message}</div>`;
            }
        },

        // Helper Methods
        selectFile(file) {
            if (this.selectedFile) {
                const prevId = this.selectedFile.id;
                this.selectedFile = null;
                this.updateItemSelectionUI(prevId);
            }
            this.selectedFile = file;
            this.updateItemSelectionUI(file.id);

            const sidebar = document.getElementById('media-sidebar');
            if (sidebar) {
                sidebar.innerHTML = this.renderSidebarContent(file);
                sidebar.classList.remove('hidden');
            }
        },

        closeSidebar() {
            if (this.selectedFile) {
                const prevId = this.selectedFile.id;
                this.selectedFile = null;
                this.updateItemSelectionUI(prevId);
            }
            const sidebar = document.getElementById('media-sidebar');
            if (sidebar) {
                sidebar.classList.add('hidden');
                sidebar.innerHTML = '';
            }
        },

        async uploadFiles(files) {
            if (!files || !files.length) return;

            const progressModal = document.getElementById('upload-progress-modal');
            const progressList = document.getElementById('upload-progress-list');
            const progressStatus = document.getElementById('upload-progress-status');

            progressModal.classList.remove('hidden');
            progressModal.style.display = 'flex';
            progressList.innerHTML = '';

            const totalFiles = files.length;
            let completed = 0;
            let failed = 0;

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const itemId = `upload-item-${i}`;

                progressList.innerHTML += `
                    <div id="${itemId}" class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded flex items-center justify-center flex-shrink-0">
                            <span class="material-icons-round text-gray-400 text-sm" id="${itemId}-icon">upload_file</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900 dark:text-white truncate">${file.name}</p>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 mt-1">
                                <div id="${itemId}-bar" class="bg-primary h-1.5 rounded-full transition-all" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                `;

                progressStatus.textContent = `Uploading ${i + 1} of ${totalFiles}...`;

                const formData = new FormData();
                formData.append('file', file);
                if (this.currentFolderId) formData.append('folder_id', this.currentFolderId);

                try {
                    const bar = document.getElementById(`${itemId}-bar`);
                    if(bar) bar.style.width = '50%';
                    
                    const basePath = window.location.pathname.split('/admin/')[0];
                    const res = await fetch(basePath + '/api/media/upload', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': this.csrfToken },
                        body: formData
                    });
                    
                    if (res.ok) {
                        completed++;
                        if (bar) { bar.style.width = '100%'; bar.classList.remove('bg-primary'); bar.classList.add('bg-green-500'); }
                    } else {
                        failed++;
                        if (bar) { bar.style.width = '100%'; bar.classList.remove('bg-primary'); bar.classList.add('bg-red-500'); }
                    }
                } catch (e) {
                    failed++;
                }
            }

            progressStatus.textContent = `Upload complete: ${completed} succeeded, ${failed} failed`;
            setTimeout(() => {
                progressModal.classList.add('hidden');
                progressModal.style.display = 'none';
                this.renderMedia();
            }, 1000);
        },

        async deleteFile(id) {
            if (!confirm('Delete file?')) return;
            try { 
                await fetch(`/api/media/${id}`, { method: 'DELETE', headers: {'X-CSRF-TOKEN': this.csrfToken} });
                this.selectedFile = null;
                this.renderMedia();
                this.showToast('File deleted successfully', 'success');
            } catch(e) {
                this.showToast('Failed to delete file', 'error');
            }
        },

        async updateFile(id, data) {
            try {
                const res = await fetch(`/api/media/${id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },
                    body: JSON.stringify(data)
                });
                if (res.ok) {
                    const updated = await res.json();
                    this.selectedFile = updated;
                }
            } catch(e) {}
        },

        filterMedia(key, value) {
            if (!this.mediaQuery) this.mediaQuery = { type: 'all', search: '', page: 1 };
            this.mediaQuery[key] = value;
            this.renderMedia();
        },

        setSort(sort, order) {
            this.mediaQuery.sort = sort;
            this.mediaQuery.order = order;
            this.renderMedia();
        },

        paginate(page) {
            this.mediaQuery.page = page;
            this.renderMedia();
        },

        toggleViewMode() {
            this.viewMode = this.viewMode === 'grid' ? 'list' : 'grid';
            this.renderMedia();
        },

        formatSize(bytes) {
            if (bytes === 0) return '0 B';
            const k = 1024, sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },

        isImage(file) {
            return file.type.startsWith('image/') || /\.(jpg|jpeg|png|gif|webp|svg|bmp)$/i.test(file.filename);
        },

        getThumb(file, size) {
            if (!this.isImage(file)) return null;
            return (file.thumbnails && file.thumbnails[size]) ? file.thumbnails[size] : file.path;
        },

        toggleFileSelection(id, event) {
            if (event) event.stopPropagation();
            if (this.selectedFileIds.has(id)) this.selectedFileIds.delete(id);
            else this.selectedFileIds.add(id);
            this.updateItemSelectionUI(id);
            this.updateToolbarUI();
        },

        updateItemSelectionUI(id) {
            const el = document.getElementById(`media-item-${id}`);
            const isActive = this.selectedFile && this.selectedFile.id === id;
            if (el) {
                el.classList.remove('ring-2', 'ring-primary', 'border-primary', 'border-blue-500');
                if (isActive) el.classList.add('ring-2', 'ring-primary', 'border-primary');
            }
        },

        updateToolbarUI() {
            // Re-render handled by renderMedia for now, but in optimised app would update just toolbar
            this.renderMedia();
        },

        async deleteSelectedFiles() {
            if(!confirm(`Delete ${this.selectedFileIds.size} files?`)) return;
            try {
                await fetch('/api/media/bulk-delete', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken},
                    body: JSON.stringify({ ids: Array.from(this.selectedFileIds) })
                });
                const count = this.selectedFileIds.size;
                this.selectedFileIds.clear();
                this.selectedFile = null;
                this.renderMedia();
                this.showToast(`${count} files deleted successfully`, 'success');
            } catch(e) {
                this.showToast('Failed to delete files', 'error');
            }
        },

        async createNewFolder() {
            const name = await this.prompt("New Folder", "Name:");
            if (name) {
                await this.api('/media/folders', 'POST', { name, parent_id: this.currentFolderId });
                this.renderMedia();
            }
        },

        async renameFolder(id, name) {
            const newName = await this.prompt("Rename", "New Name:", name);
            if (newName && newName !== name) {
                await this.api(`/media/folders/${id}`, 'PUT', { name: newName });
                this.renderMedia();
            }
        },

        async deleteFolder(id) {
            if (confirm("Delete folder and contents?")) {
                await this.api(`/media/folders/${id}`, 'DELETE');
                this.renderMedia();
                this.showToast('Folder deleted successfully', 'success');
            }
        },

        navigateToFolder(id) {
            this.currentFolderId = id;
            this.selectedFileIds.clear();
            this.renderMedia();
        },

        async navigateUp() {
             if (this.currentFolderId) {
                const res = await this.api(`/media/folders?id=${this.currentFolderId}`);
                this.currentFolderId = (res && res[0]) ? res[0].parent_id : null;
                this.renderMedia();
             }
        },

        async loadFolderPath(id) {
            if (!id) { this.currentFolderPath = []; return; }
            let path = [];
            let curr = id;
            while(curr) {
                const res = await this.api(`/media/folders?id=${curr}`);
                if(res && res.length) {
                    path.unshift({id: res[0].id, name: res[0].name});
                    curr = res[0].parent_id;
                } else break;
            }
            this.currentFolderPath = path;
        },
        
        // Prompts
        prompt(title, label, value = '') {
            return new Promise(resolve => {
                this.showModal({
                    title,
                    body: `<label class="block text-sm mb-2">${label}</label><input id="prompt-in" value="${value}" class="w-full px-3 py-2 border rounded">`,
                    actions: [
                        { text: 'OK', class: 'bg-primary text-white px-4 py-2 rounded', onClick: () => resolve(document.getElementById('prompt-in').value) },
                        { text: 'Cancel', onClick: () => resolve(null) }
                    ]
                });
            });
        },
        
        async moveSelectedFiles() {
             // simplified for brevity
             const target = await this.prompt("Move to Folder ID", "Enter Folder ID (or leave empty for root)");
             if (target !== null) {
                 await this.api('/media/move', 'POST', { ids: Array.from(this.selectedFileIds), folder_id: target || null });
                 this.selectedFileIds.clear();
                 this.renderMedia();
             }
        },

        renderSidebarContent(file) {
            if (!file) return '';
            return `
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-gray-900 dark:text-white">File Details</h3>
                    <button onclick="App.closeSidebar()" class="text-gray-400 hover:text-gray-600">
                        <span class="material-icons-round">close</span>
                    </button>
                </div>

                <div class="bg-gray-100 dark:bg-gray-800 rounded-lg overflow-hidden mb-4 border border-border-light dark:border-border-dark w-full relative group cursor-pointer" ${this.isImage(file) ? `onclick="App.showLightbox('${file.path}')"` : ''}>
                     ${this.isImage(file)
                    ? `<img src="${this.getThumb(file, 'medium') || file.path}" class="w-full h-auto max-h-[300px] object-contain mx-auto">
                       <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                           <span class="material-icons-round text-white text-3xl">zoom_in</span>
                       </div>`
                    : `<div class="h-48 flex items-center justify-center"><span class="material-icons-round text-6xl text-gray-300">description</span></div>`
                }
                </div>

                <div class="space-y-4 text-sm">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">File Name</label>
                        <div class="flex">
                            <input type="text" readonly value="${file.filename}" class="flex-1 min-w-0 px-3 py-2 bg-gray-50 dark:bg-gray-800 border-none rounded-l-lg text-gray-600 dark:text-gray-300 text-xs">
                            <div class="px-2 bg-gray-100 dark:bg-gray-700 rounded-r-lg flex items-center text-xs text-gray-500 border-l border-gray-200 dark:border-gray-600">.${file.filename.split('.').pop()}</div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Alt Text</label>
                        <textarea rows="2" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-800 border-none rounded-lg text-gray-900 dark:text-white text-xs resize-none focus:ring-1 focus:ring-primary"
                            onchange="App.updateFile(${file.id}, {alt_text: this.value})">${file.alt_text || ''}</textarea>
                        <p class="text-[10px] text-gray-400 mt-1">Describe the image for SEO and accessibility.</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4 py-4 border-t border-b border-border-light dark:border-border-dark">
                        <div>
                            <p class="text-xs text-gray-500">Dimensions</p>
                            <p class="font-medium text-gray-900 dark:text-white text-xs">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">File Size</p>
                            <p class="font-medium text-gray-900 dark:text-white text-xs">${this.formatSize(file.size)}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Uploaded On</p>
                            <p class="font-medium text-gray-900 dark:text-white text-xs">${new Date(file.created_at).toLocaleDateString()}</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">File URL</label>
                        <div class="flex">
                            <input type="text" readonly value="${window.location.origin}${file.path}" class="flex-1 min-w-0 px-3 py-2 bg-gray-50 dark:bg-gray-800 border-none rounded-l-lg text-gray-500 dark:text-gray-400 text-xs truncate">
                            <button onclick="navigator.clipboard.writeText('${window.location.origin}${file.path}')" class="px-3 bg-gray-100 dark:bg-gray-700 rounded-r-lg hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-500">
                                <span class="material-icons-round text-sm">content_copy</span>
                            </button>
                        </div>
                    </div>

                    <div class="pt-4 grid grid-cols-2 gap-2">
                        <button onclick="App.downloadFile('${file.path}', '${file.filename}')" class="px-3 py-2 border border-border-light dark:border-border-dark text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg text-xs font-medium transition-colors flex items-center justify-center">
                            <span class="material-icons-round text-sm mr-1">download</span> Download
                        </button>
                        <button onclick="App.renameFile(${file.id}, '${file.filename.replace(/'/g, "\\\\'")}')"
                            class="px-3 py-2 border border-border-light dark:border-border-dark text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg text-xs font-medium transition-colors flex items-center justify-center">
                            <span class="material-icons-round text-sm mr-1">edit</span> Rename
                        </button>
                        <button onclick="App.deleteFile(${file.id})" class="px-3 py-2 border border-red-200 text-red-600 hover:bg-red-50 rounded-lg text-xs font-medium transition-colors flex items-center justify-center">
                            <span class="material-icons-round text-sm mr-1">delete</span> Delete
                        </button>
                        <button onclick="App.closeSidebar()" class="px-3 py-2 bg-primary hover:bg-primaryHover text-white rounded-lg text-xs font-medium transition-colors">
                            Done
                        </button>
                    </div>
                </div>
            `;
        },

        downloadFile(path, filename) {
            const a = document.createElement('a');
            a.href = path;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        },

        async renameFile(id, currentName) {
            const newName = await this.prompt("Rename File", "New filename:", currentName);
            if (newName && newName !== currentName) {
                await this.updateFile(id, { filename: newName });
                this.renderMedia();
            }
        }
    });

    document.addEventListener('DOMContentLoaded', () => App.renderMedia());
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
