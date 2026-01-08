<?php
$title = 'Plugins';
ob_start();
?>
<div>
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Plugins</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Add new features and functionality to your site.</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <button type="button" id="upload-btn-trigger" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                <span class="material-icons-round text-lg mr-2">add</span>
                Add New Plugin
            </button>
        </div>
    </div>

    <!-- Filters & Bulk Actions -->
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 mb-6 p-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="flex space-x-1">
                    <button type="button" onclick="setFilter('all')" class="tab-btn active px-4 py-2 rounded-md text-sm font-medium" data-filter="all">
                        All <span class="text-gray-400 ml-1" id="count-all">0</span>
                    </button>
                    <button type="button" onclick="setFilter('active')" class="tab-btn px-4 py-2 rounded-md text-sm font-medium" data-filter="active">
                        Active <span class="text-gray-400 ml-1" id="count-active">0</span>
                    </button>
                    <button type="button" onclick="setFilter('inactive')" class="tab-btn px-4 py-2 rounded-md text-sm font-medium" data-filter="inactive">
                        Inactive <span class="text-gray-400 ml-1" id="count-inactive">0</span>
                    </button>
                </div>
                <!-- Bulk Actions (hidden by default) -->
                <div id="bulk-actions" class="hidden items-center gap-3 ml-4 pl-4 border-l-2 border-gray-200 dark:border-slate-600">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-primary/10 text-primary">
                        <span id="selected-count">0</span>&nbsp;selected
                    </span>
                    <button type="button" onclick="bulkActivate()" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-green-500 hover:bg-green-600 rounded-md shadow-sm transition-colors">
                        <span class="material-icons-round text-sm mr-1">check_circle</span>
                        Activate
                    </button>
                    <button type="button" onclick="bulkDeactivate()" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-orange-500 hover:bg-orange-600 rounded-md shadow-sm transition-colors">
                        <span class="material-icons-round text-sm mr-1">pause_circle</span>
                        Deactivate
                    </button>
                    <button type="button" onclick="bulkDelete()" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-red-500 hover:bg-red-600 rounded-md shadow-sm transition-colors">
                        <span class="material-icons-round text-sm mr-1">delete</span>
                        Delete
                    </button>
                </div>
            </div>
            <div class="relative w-full sm:w-64">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <span class="material-icons-round text-gray-400 text-lg">search</span>
                </div>
                <input type="text" id="plugin-search" placeholder="Search plugins..." 
                    class="focus:ring-primary focus:border-primary block w-full pl-10 sm:text-sm border-gray-300 dark:border-slate-600 rounded-md bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100"
                    oninput="filterPlugins()">
            </div>
        </div>
    </div>

    <!-- Plugin List Table -->
    <div class="bg-white dark:bg-slate-800 shadow-sm border border-gray-200 dark:border-slate-700 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                <thead class="bg-gray-50 dark:bg-slate-700/50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left w-12">
                            <input type="checkbox" id="select-all" onchange="toggleSelectAll()" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded dark:bg-slate-700 dark:border-slate-600">
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Plugin</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="plugin-tbody" class="bg-white dark:bg-slate-800 divide-y divide-gray-200 dark:divide-slate-700">
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex items-center justify-center">
                                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-primary mr-3"></div>
                                Loading plugins...
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Safety Info -->
    <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-100 dark:border-blue-800">
        <div class="flex items-start gap-3">
            <span class="material-icons-round text-blue-500 text-xl">info</span>
            <div class="text-sm text-blue-700 dark:text-blue-300">
                <strong>Safety Note:</strong> Active plugins cannot be deleted. Deactivate a plugin first before deleting it. Inactive plugins appear faded to indicate they are not running.
            </div>
        </div>
    </div>
</div>

<!-- Upload Plugin Modal -->
<div id="upload-modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 9999; overflow-y: auto;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div style="display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 1rem; text-align: center;">
        <!-- Background overlay -->
        <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5);" onclick="closeUploadModal()"></div>
        
        <!-- Modal panel -->
        <div style="position: relative; background: white; border-radius: 0.75rem; text-align: left; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); max-width: 28rem; width: 100%; margin: 0 auto;" class="dark:bg-slate-800 border border-gray-200 dark:border-slate-700">
            <div class="bg-white dark:bg-slate-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-purple-100 dark:bg-purple-900/30 sm:mx-0 sm:h-10 sm:w-10">
                        <span class="material-icons-round text-primary">upload</span>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                            Upload Plugin
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Upload a plugin in .zip format. The plugin must contain a Plugin.php file.
                            </p>
                        </div>
                        
                        <!-- Upload Zone -->
                        <div class="mt-4">
                            <div id="upload-dropzone" class="border-2 border-dashed border-gray-300 dark:border-slate-600 rounded-lg p-6 text-center cursor-pointer transition-colors hover:border-primary hover:bg-purple-50 dark:hover:bg-purple-900/10">
                                <input type="file" id="plugin-file-input" accept=".zip" class="hidden">
                                <span class="material-icons-round text-4xl text-gray-300 dark:text-slate-600">cloud_upload</span>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Drop your plugin ZIP here or click to browse</p>
                            </div>
                            <div id="selected-file" class="hidden mt-3 p-3 bg-gray-50 dark:bg-slate-900 rounded-md flex items-center justify-between">
                                <div class="flex items-center">
                                    <span class="material-icons-round text-primary mr-2">folder_zip</span>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300" id="selected-filename"></span>
                                </div>
                                <button type="button" onclick="clearSelectedFile()" class="text-gray-400 hover:text-red-500">
                                    <span class="material-icons-round text-lg">close</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-slate-800/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" id="install-btn" onclick="installPlugin()" disabled class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                    Install Now
                </button>
                <button type="button" onclick="closeUploadModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-slate-600 shadow-sm px-4 py-2 bg-white dark:bg-slate-700 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-600 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .tab-btn {
        color: #6b7280;
        background: transparent;
    }
    .dark .tab-btn {
        color: #9ca3af;
    }
    .tab-btn.active {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        color: white;
    }
    .tab-btn:hover:not(.active) {
        background: #f3f4f6;
    }
    .dark .tab-btn:hover:not(.active) {
        background: #374151;
    }
    
    /* Inactive plugin faded style */
    .plugin-inactive {
        opacity: 0.6;
    }
    .plugin-inactive:hover {
        opacity: 0.85;
    }
    
    /* Active plugin highlight */
    .plugin-active {
        border-left: 4px solid #8b5cf6 !important;
    }
</style>

<script>
let allPlugins = [];
let currentFilter = 'all';
let selectedFile = null;
let selectedPlugins = new Set();

document.addEventListener('DOMContentLoaded', () => {
    // Move modal to body
    const modal = document.getElementById('upload-modal');
    if (modal) document.body.appendChild(modal);
    
    loadPlugins();
    setupUploadModal();
});

function setupUploadModal() {
    const trigger = document.getElementById('upload-btn-trigger');
    const dropzone = document.getElementById('upload-dropzone');
    const fileInput = document.getElementById('plugin-file-input');
    
    trigger.addEventListener('click', () => {
        document.getElementById('upload-modal').style.display = 'block';
    });
    
    dropzone.addEventListener('click', () => fileInput.click());
    
    fileInput.addEventListener('change', (e) => {
        if (e.target.files[0]) handleFileSelect(e.target.files[0]);
    });
    
    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.classList.add('border-primary', 'bg-purple-50', 'dark:bg-purple-900/10');
    });
    dropzone.addEventListener('dragleave', () => {
        dropzone.classList.remove('border-primary', 'bg-purple-50', 'dark:bg-purple-900/10');
    });
    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('border-primary', 'bg-purple-50', 'dark:bg-purple-900/10');
        if (e.dataTransfer.files[0]) handleFileSelect(e.dataTransfer.files[0]);
    });
}

function handleFileSelect(file) {
    if (!file.name.endsWith('.zip')) {
        App.showToast('Please select a ZIP file', 'error');
        return;
    }
    selectedFile = file;
    document.getElementById('selected-filename').textContent = file.name;
    document.getElementById('selected-file').classList.remove('hidden');
    document.getElementById('upload-dropzone').classList.add('hidden');
    document.getElementById('install-btn').disabled = false;
}

function clearSelectedFile() {
    selectedFile = null;
    document.getElementById('plugin-file-input').value = '';
    document.getElementById('selected-file').classList.add('hidden');
    document.getElementById('upload-dropzone').classList.remove('hidden');
    document.getElementById('install-btn').disabled = true;
}

function closeUploadModal() {
    document.getElementById('upload-modal').style.display = 'none';
    clearSelectedFile();
}

async function installPlugin() {
    if (!selectedFile) return;
    
    const btn = document.getElementById('install-btn');
    btn.disabled = true;
    btn.innerHTML = 'Installing...';
    
    const formData = new FormData();
    formData.append('plugin', selectedFile);
    formData.append('csrf_token', App.csrfToken);
    
    try {
        const response = await fetch('/api/plugins/upload', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        
        if (!response.ok) throw new Error(data.error || 'Upload failed');
        
        App.showToast('Plugin installed successfully!', 'success');
        closeUploadModal();
        loadPlugins();
    } catch (e) {
        App.showToast(e.message, 'error');
        btn.disabled = false;
        btn.textContent = 'Install Now';
    }
}

async function loadPlugins() {
    const tbody = document.getElementById('plugin-tbody');
    try {
        allPlugins = await App.api('/plugins');
        updateCounts();
        renderPlugins();
    } catch (e) {
        tbody.innerHTML = `<tr><td colspan="4" class="px-6 py-12 text-center text-red-500">${e.message}</td></tr>`;
    }
}

function updateCounts() {
    const total = allPlugins.length;
    const active = allPlugins.filter(p => p.is_active).length;
    document.getElementById('count-all').textContent = total;
    document.getElementById('count-active').textContent = active;
    document.getElementById('count-inactive').textContent = total - active;
}

function updateBulkActions() {
    const bulkDiv = document.getElementById('bulk-actions');
    const countSpan = document.getElementById('selected-count');
    
    if (selectedPlugins.size > 0) {
        bulkDiv.classList.remove('hidden');
        bulkDiv.classList.add('flex');
        countSpan.textContent = selectedPlugins.size;
    } else {
        bulkDiv.classList.add('hidden');
        bulkDiv.classList.remove('flex');
    }
}

function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.plugin-checkbox');
    
    checkboxes.forEach(cb => {
        cb.checked = selectAllCheckbox.checked;
        if (selectAllCheckbox.checked) {
            selectedPlugins.add(cb.value);
        } else {
            selectedPlugins.delete(cb.value);
        }
    });
    
    updateBulkActions();
}

function togglePluginSelect(id) {
    if (selectedPlugins.has(id)) {
        selectedPlugins.delete(id);
    } else {
        selectedPlugins.add(id);
    }
    updateBulkActions();
    
    // Update select all checkbox
    const checkboxes = document.querySelectorAll('.plugin-checkbox');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    document.getElementById('select-all').checked = allChecked;
}

function renderPlugins() {
    const tbody = document.getElementById('plugin-tbody');
    const search = document.getElementById('plugin-search').value.toLowerCase();
    
    let filtered = allPlugins.filter(p => {
        const matchesSearch = p.name.toLowerCase().includes(search) || p.description.toLowerCase().includes(search);
        const matchesFilter = currentFilter === 'all' || 
                             (currentFilter === 'active' && p.is_active) ||
                             (currentFilter === 'inactive' && !p.is_active);
        return matchesSearch && matchesFilter;
    });

    if (filtered.length === 0) {
        tbody.innerHTML = `<tr><td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No plugins found</td></tr>`;
        return;
    }

    tbody.innerHTML = filtered.map(plugin => {
        const isActive = plugin.is_active;
        const rowClass = isActive ? 'plugin-active' : 'plugin-inactive';
        const isChecked = selectedPlugins.has(plugin.id) ? 'checked' : '';
        
        return `
        <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 ${rowClass} border-l-4 ${isActive ? '' : 'border-l-transparent'}">
            <td class="px-4 py-4">
                <input type="checkbox" class="plugin-checkbox h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded dark:bg-slate-700 dark:border-slate-600" 
                    value="${plugin.id}" onchange="togglePluginSelect('${plugin.id}')" ${isChecked}>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-lg ${isActive ? 'bg-gradient-to-br from-primary to-purple-600' : 'bg-gray-200 dark:bg-slate-700'} flex items-center justify-center">
                        <span class="material-icons-round ${isActive ? 'text-white' : 'text-gray-400'}">${plugin.icon || 'extension'}</span>
                    </div>
                    <div class="ml-4">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">${plugin.name}</span>
                            <span class="text-xs text-gray-400">v${plugin.version}</span>
                            ${isActive 
                                ? '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Active</span>' 
                                : '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-slate-700 dark:text-gray-400">Inactive</span>'
                            }
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">By ${plugin.author}</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4">
                <div class="text-sm text-gray-600 dark:text-gray-400 max-w-md truncate">${plugin.description}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                ${isActive ? `
                    <a href="plugin-settings.php?plugin=${plugin.id}" class="text-primary hover:text-purple-700 mr-3">Settings</a>
                    <button onclick="togglePlugin('${plugin.id}', false)" class="text-orange-500 hover:text-orange-600">Deactivate</button>
                ` : `
                    <button onclick="togglePlugin('${plugin.id}', true)" class="text-primary hover:text-purple-700 mr-3">Activate</button>
                    <button onclick="deletePlugin('${plugin.id}')" class="text-red-500 hover:text-red-600">Delete</button>
                `}
            </td>
        </tr>
    `}).join('');
}

function setFilter(filter) {
    currentFilter = filter;
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.filter === filter);
    });
    renderPlugins();
}

function filterPlugins() {
    renderPlugins();
}

async function togglePlugin(id, activate) {
    const action = activate ? 'activate' : 'deactivate';
    try {
        await App.api(`/plugins/${action}`, 'POST', { id });
        App.showToast(`Plugin ${action}d successfully`, 'success');
        await loadPlugins();
    } catch (e) {
        App.showToast(e.message, 'error');
    }
}

async function deletePlugin(id) {
    // Safety check: only delete inactive plugins
    const plugin = allPlugins.find(p => p.id === id);
    if (plugin && plugin.is_active) {
        App.showToast('Cannot delete active plugin. Deactivate it first.', 'error');
        return;
    }
    
    if (!confirm('Are you sure you want to delete this plugin? This cannot be undone.')) return;
    
    try {
        await App.api(`/plugins/${id}`, 'DELETE');
        App.showToast('Plugin deleted', 'success');
        await loadPlugins();
    } catch (e) {
        App.showToast(e.message, 'error');
    }
}

// Bulk Actions
async function bulkActivate() {
    const ids = Array.from(selectedPlugins);
    let activated = 0;
    
    for (const id of ids) {
        try {
            await App.api('/plugins/activate', 'POST', { id });
            activated++;
        } catch (e) { /* skip errors */ }
    }
    
    App.showToast(`${activated} plugin(s) activated`, 'success');
    selectedPlugins.clear();
    document.getElementById('select-all').checked = false;
    updateBulkActions();
    await loadPlugins();
}

async function bulkDeactivate() {
    const ids = Array.from(selectedPlugins);
    let deactivated = 0;
    
    for (const id of ids) {
        try {
            await App.api('/plugins/deactivate', 'POST', { id });
            deactivated++;
        } catch (e) { /* skip errors */ }
    }
    
    App.showToast(`${deactivated} plugin(s) deactivated`, 'success');
    selectedPlugins.clear();
    document.getElementById('select-all').checked = false;
    updateBulkActions();
    await loadPlugins();
}

async function bulkDelete() {
    // Safety: Only delete inactive plugins
    const ids = Array.from(selectedPlugins);
    const inactiveIds = ids.filter(id => {
        const plugin = allPlugins.find(p => p.id === id);
        return plugin && !plugin.is_active;
    });
    
    if (inactiveIds.length === 0) {
        App.showToast('No inactive plugins selected. Active plugins cannot be deleted.', 'warning');
        return;
    }
    
    const activeCount = ids.length - inactiveIds.length;
    let message = `Delete ${inactiveIds.length} inactive plugin(s)?`;
    if (activeCount > 0) {
        message += ` (${activeCount} active plugin(s) will be skipped)`;
    }
    
    if (!confirm(message)) return;
    
    let deleted = 0;
    for (const id of inactiveIds) {
        try {
            await App.api(`/plugins/${id}`, 'DELETE');
            deleted++;
        } catch (e) { /* skip errors */ }
    }
    
    App.showToast(`${deleted} plugin(s) deleted`, 'success');
    selectedPlugins.clear();
    document.getElementById('select-all').checked = false;
    updateBulkActions();
    await loadPlugins();
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
