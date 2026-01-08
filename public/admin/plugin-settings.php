<?php
/**
 * Plugin Settings Page
 * 
 * Dedicated page for plugin-specific settings (WordPress-style)
 */
$pluginId = $_GET['plugin'] ?? '';

if (empty($pluginId)) {
    header('Location: plugins.php');
    exit;
}

$title = 'Plugin Settings';
ob_start();

// Get plugin info
require_once __DIR__ . '/../../config/bootstrap.php';
$manager = \App\Services\PluginManager::getInstance();
$plugin = $manager->get($pluginId);

if (!$plugin) {
    echo '<div class="max-w-4xl mx-auto"><div class="bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 p-4 rounded-lg">Plugin not found.</div></div>';
    $content = ob_get_clean();
    include __DIR__ . '/layout.php';
    exit;
}

$pluginName = $plugin->metadata->name;
$pluginIcon = $plugin->metadata->icon ?: 'extension';
?>

<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <nav class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-4">
            <a href="plugins.php" class="hover:text-primary transition-colors">Plugins</a>
            <span class="mx-2">→</span>
            <span class="text-gray-900 dark:text-white"><?= htmlspecialchars($pluginName) ?> Settings</span>
        </nav>
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-primary to-purple-600 flex items-center justify-center shadow-lg">
                <span class="material-icons-round text-3xl text-white"><?= htmlspecialchars($pluginIcon) ?></span>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($pluginName) ?> Settings</h1>
                <p class="text-gray-500 dark:text-gray-400">v<?= htmlspecialchars($plugin->metadata->version) ?> by <?= htmlspecialchars($plugin->metadata->author) ?></p>
            </div>
        </div>
    </div>

    <!-- Settings Form -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200 dark:border-slate-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Configuration</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage settings for this plugin.</p>
        </div>
        
        <form id="settings-form" class="p-6 space-y-6">
            <div id="settings-fields" class="space-y-6">
                <div class="flex items-center justify-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                    <span class="ml-3 text-gray-500 dark:text-gray-400">Loading settings...</span>
                </div>
            </div>
        </form>
        
        <div class="px-6 py-4 bg-gray-50 dark:bg-slate-800/50 border-t border-gray-200 dark:border-slate-700 flex items-center justify-between">
            <a href="plugins.php" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                ← Back to Plugins
            </a>
            <button type="button" onclick="saveSettings()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary-dark text-white rounded-lg font-medium transition-all shadow-sm">
                <span class="material-icons-round text-lg">save</span>
                Save Changes
            </button>
        </div>
    </div>
</div>

<script>
const pluginId = '<?= htmlspecialchars($pluginId) ?>';

document.addEventListener('DOMContentLoaded', loadSettings);

async function loadSettings() {
    const container = document.getElementById('settings-fields');
    
    try {
        const data = await App.api(`/plugins/${pluginId}/settings`);
        const settings = data.settings || {};
        
        if (Object.keys(settings).length === 0) {
            container.innerHTML = `
                <div class="text-center py-8">
                    <span class="material-icons-round text-5xl text-gray-300 dark:text-slate-600 mb-4">tune</span>
                    <p class="text-gray-500 dark:text-gray-400">No configurable settings for this plugin.</p>
                </div>
            `;
            return;
        }
        
        // Group settings
        const groups = {};
        Object.entries(settings).forEach(([key, setting]) => {
            const group = setting.group || 'general';
            if (!groups[group]) groups[group] = [];
            groups[group].push({ key, ...setting });
        });
        
        let html = '';
        Object.entries(groups).forEach(([groupName, groupSettings]) => {
            html += `
                <div class="mb-8">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider mb-4 flex items-center gap-2">
                        <span class="w-2 h-2 bg-primary rounded-full"></span>
                        ${groupName.charAt(0).toUpperCase() + groupName.slice(1)}
                    </h3>
                    <div class="space-y-5">
                        ${groupSettings.map(setting => renderField(setting)).join('')}
                    </div>
                </div>
            `;
        });
        
        container.innerHTML = html;
    } catch (e) {
        container.innerHTML = `<p class="text-red-500 py-8 text-center">Error loading settings: ${e.message}</p>`;
    }
}

function renderField(setting) {
    const value = setting.value ?? setting.default ?? '';
    const id = `setting-${setting.key}`;
    
    let inputHtml = '';
    
    switch (setting.type) {
        case 'toggle':
            inputHtml = `
                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-slate-900 rounded-lg">
                    <div>
                        <label for="${id}" class="font-medium text-gray-900 dark:text-white">${setting.label || setting.key}</label>
                        ${setting.description ? `<p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">${setting.description}</p>` : ''}
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="${id}" data-key="${setting.key}" class="sr-only peer" ${value ? 'checked' : ''}>
                        <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/25 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                    </label>
                </div>
            `;
            return inputHtml;
            
        case 'password':
            inputHtml = `<input type="password" id="${id}" data-key="${setting.key}" value="${escapeHtml(value)}" 
                class="w-full px-4 py-3 bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">`;
            break;
            
        case 'textarea':
            inputHtml = `<textarea id="${id}" data-key="${setting.key}" rows="4"
                class="w-full px-4 py-3 bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">${escapeHtml(value)}</textarea>`;
            break;
            
        case 'select':
            inputHtml = `
                <select id="${id}" data-key="${setting.key}" class="w-full px-4 py-3 bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                    ${Object.entries(setting.options || {}).map(([val, label]) => 
                        `<option value="${escapeHtml(val)}" ${val === value ? 'selected' : ''}>${escapeHtml(label)}</option>`
                    ).join('')}
                </select>
            `;
            break;
            
        case 'number':
            inputHtml = `<input type="number" id="${id}" data-key="${setting.key}" value="${escapeHtml(value)}" 
                class="w-full px-4 py-3 bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">`;
            break;
            
        default:
            inputHtml = `<input type="text" id="${id}" data-key="${setting.key}" value="${escapeHtml(value)}" 
                class="w-full px-4 py-3 bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">`;
    }
    
    return `
        <div>
            <label for="${id}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                ${setting.label || setting.key}
                ${setting.required ? '<span class="text-red-500">*</span>' : ''}
            </label>
            ${inputHtml}
            ${setting.description && setting.type !== 'toggle' ? `<p class="mt-2 text-sm text-gray-500 dark:text-gray-400">${setting.description}</p>` : ''}
        </div>
    `;
}

function escapeHtml(str) {
    if (typeof str !== 'string') return str;
    return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

async function saveSettings() {
    const inputs = document.querySelectorAll('[data-key]');
    const settings = {};
    
    inputs.forEach(input => {
        const key = input.dataset.key;
        if (input.type === 'checkbox') {
            settings[key] = input.checked;
        } else {
            settings[key] = input.value;
        }
    });
    
    try {
        await App.api(`/plugins/${pluginId}/settings`, 'PUT', settings);
        App.showToast('Settings saved successfully!', 'success');
    } catch (e) {
        App.showToast('Error saving settings: ' + e.message, 'error');
    }
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
