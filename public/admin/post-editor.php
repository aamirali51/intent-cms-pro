<?php
$title = 'Edit Post';

// Get post ID from URL
$postId = isset($_GET['id']) ? (int)$_GET['id'] : null;

ob_start();
?>

<!-- Editor.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@2.28.0/dist/editorjs.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/header@2.8.1/dist/header.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/list@1.9.0/dist/list.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/image@2.9.0/dist/image.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/quote@2.6.0/dist/quote.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/delimiter@1.4.0/dist/delimiter.umd.min.js"></script>

<style>
/* WordPress Gutenberg-inspired Editor Styles */
:root {
    --editor-sidebar-width: 280px;
}

/* Editor Header */
#editor-header {
    position: sticky;
    top: 0;
    z-index: 30;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.5rem 1rem;
    background: white;
    border-bottom: 1px solid #e5e7eb;
}
.dark #editor-header {
    background: #1f2937;
    border-color: #374151;
}

/* Editor Layout */
#editor-layout {
    display: flex;
    height: calc(100vh - 8rem);
}

/* Main Content Area */
#editor-main {
    flex: 1;
    overflow-y: auto;
    background: #fafafa;
    padding: 3rem 1.5rem 10rem;
}
.dark #editor-main {
    background: #111827;
}

/* Editor Canvas */
#editor-canvas {
    max-width: 700px;
    margin: 0 auto;
}

/* Title Input */
#post-title {
    width: 100%;
    font-size: 2.5rem;
    font-weight: 700;
    line-height: 1.2;
    border: none;
    outline: none;
    background: transparent;
    color: #111827;
    padding: 0;
    margin-bottom: 1.5rem;
}
#post-title::placeholder {
    color: #9ca3af;
}
.dark #post-title {
    color: #f9fafb;
}

/* Editor.js Styles */
#editorjs {
    font-size: 1.125rem;
    line-height: 1.8;
    color: #374151;
}
.dark #editorjs {
    color: #d1d5db;
}
#editorjs .ce-block__content,
#editorjs .ce-toolbar__content {
    max-width: 100%;
}
#editorjs .ce-paragraph {
    line-height: 1.8;
}

/* Right Sidebar */
#editor-sidebar {
    width: var(--editor-sidebar-width);
    flex-shrink: 0;
    background: white;
    border-left: 1px solid #e5e7eb;
    overflow-y: auto;
}
.dark #editor-sidebar {
    background: #1f2937;
    border-color: #374151;
}
#editor-sidebar.hidden {
    display: none;
}

/* Sidebar Panel */
.sidebar-panel {
    border-bottom: 1px solid #e5e7eb;
}
.dark .sidebar-panel {
    border-color: #374151;
}
.sidebar-panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem;
    cursor: pointer;
    user-select: none;
}
.sidebar-panel-header:hover {
    background: #f9fafb;
}
.dark .sidebar-panel-header:hover {
    background: #374151;
}
.sidebar-panel-content {
    padding: 0 1rem 1rem;
}
.sidebar-panel-content.collapsed {
    display: none;
}

/* Form Controls */
.form-input {
    width: 100%;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    color: #111827;
}
.dark .form-input {
    background: #374151;
    border-color: #4b5563;
    color: #f9fafb;
}
.form-input:focus {
    outline: none;
    border-color: #8b5cf6;
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    border-radius: 0.5rem;
    transition: all 0.15s;
    cursor: pointer;
}
.btn-primary {
    background: #8b5cf6;
    color: white;
}
.btn-primary:hover {
    background: #7c3aed;
}
.btn-ghost {
    background: transparent;
    color: #6b7280;
}
.btn-ghost:hover {
    background: #f3f4f6;
    color: #111827;
}
.dark .btn-ghost:hover {
    background: #374151;
    color: #f9fafb;
}
</style>

<!-- Editor Header -->
<div id="editor-header">
    <div class="flex items-center gap-4">
        <a href="posts.php" class="btn btn-ghost" title="Back to posts">
            <span class="material-icons-round mr-1">arrow_back</span>
            Posts
        </a>
        <span id="save-status" class="text-sm text-gray-400"></span>
    </div>
    <div class="flex items-center gap-2">
        <button onclick="PostEditor.preview()" class="btn btn-ghost" title="Preview post">
            <span class="material-icons-round text-base mr-1">visibility</span>
            Preview
        </button>
        <button onclick="PostEditor.save('draft')" class="btn btn-ghost">Save Draft</button>
        <button onclick="PostEditor.save('published')" class="btn btn-primary">
            <span class="material-icons-round text-base mr-1">send</span>
            Publish
        </button>
        <button onclick="PostEditor.toggleSidebar()" class="btn btn-ghost ml-2" title="Toggle sidebar">
            <span class="material-icons-round">settings</span>
        </button>
    </div>
</div>

<!-- Editor Layout -->
<div id="editor-layout">
    <!-- Main Editor Area -->
    <div id="editor-main">
        <div id="editor-canvas">
            <input type="text" id="post-title" placeholder="Add title" oninput="PostEditor.onTitleChange()">
            <div id="editorjs"></div>
        </div>
    </div>

    <!-- Right Sidebar -->
    <div id="editor-sidebar">
        <!-- Status Panel -->
        <div class="sidebar-panel">
            <div class="sidebar-panel-header" onclick="this.nextElementSibling.classList.toggle('collapsed')">
                <span class="text-sm font-medium text-gray-900 dark:text-white">Status</span>
                <span class="material-icons-round text-gray-400 text-lg">expand_more</span>
            </div>
            <div class="sidebar-panel-content">
                <select id="post-status" class="form-input">
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                </select>
            </div>
        </div>

        <!-- Permalink Panel -->
        <div class="sidebar-panel">
            <div class="sidebar-panel-header" onclick="this.nextElementSibling.classList.toggle('collapsed')">
                <span class="text-sm font-medium text-gray-900 dark:text-white">URL Slug</span>
                <span class="material-icons-round text-gray-400 text-lg">expand_more</span>
            </div>
            <div class="sidebar-panel-content">
                <input type="text" id="post-slug" placeholder="post-url-slug" class="form-input">
            </div>
        </div>

        <!-- Featured Image Panel -->
        <div class="sidebar-panel">
            <div class="sidebar-panel-header" onclick="this.nextElementSibling.classList.toggle('collapsed')">
                <span class="text-sm font-medium text-gray-900 dark:text-white">Featured Image</span>
                <span class="material-icons-round text-gray-400 text-lg">expand_more</span>
            </div>
            <div class="sidebar-panel-content" id="featured-image-panel">
                <button onclick="PostEditor.selectImage()" class="w-full py-6 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-lg text-center hover:border-primary transition-colors">
                    <span class="material-icons-round text-2xl text-gray-400">add_photo_alternate</span>
                    <p class="text-sm text-gray-500 mt-1">Set featured image</p>
                </button>
                <input type="hidden" id="post-image" value="">
            </div>
        </div>

        <!-- Excerpt Panel -->
        <div class="sidebar-panel">
            <div class="sidebar-panel-header" onclick="this.nextElementSibling.classList.toggle('collapsed')">
                <span class="text-sm font-medium text-gray-900 dark:text-white">Excerpt</span>
                <span class="material-icons-round text-gray-400 text-lg">expand_more</span>
            </div>
            <div class="sidebar-panel-content">
                <textarea id="post-excerpt" rows="3" placeholder="Write an excerpt..." class="form-input resize-none"></textarea>
            </div>
        </div>

        <!-- Categories Panel -->
        <div class="sidebar-panel">
            <div class="sidebar-panel-header" onclick="this.nextElementSibling.classList.toggle('collapsed')">
                <span class="text-sm font-medium text-gray-900 dark:text-white">Categories</span>
                <span class="material-icons-round text-gray-400 text-lg">expand_more</span>
            </div>
            <div class="sidebar-panel-content">
                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <input type="checkbox" class="rounded border-gray-300 text-primary focus:ring-primary"> General
                    </label>
                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <input type="checkbox" class="rounded border-gray-300 text-primary focus:ring-primary"> News
                    </label>
                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <input type="checkbox" class="rounded border-gray-300 text-primary focus:ring-primary"> Tutorial
                    </label>
                </div>
            </div>
        </div>

        <!-- Tags Panel -->
        <div class="sidebar-panel">
            <div class="sidebar-panel-header" onclick="this.nextElementSibling.classList.toggle('collapsed')">
                <span class="text-sm font-medium text-gray-900 dark:text-white">Tags</span>
                <span class="material-icons-round text-gray-400 text-lg">expand_more</span>
            </div>
            <div class="sidebar-panel-content" id="tags-panel">
                <!-- Selected Tags -->
                <div id="selected-tags" class="flex flex-wrap gap-1.5 mb-2"></div>
                
                <!-- Tag Input with Autocomplete -->
                <div class="relative">
                    <input type="text" id="tag-input" 
                        placeholder="Add tags..." 
                        class="form-input pr-8"
                        autocomplete="off"
                        oninput="PostEditor.searchTags(this.value)"
                        onkeydown="PostEditor.handleTagKeydown(event)">
                    <span class="material-icons-round absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 text-lg">label</span>
                    
                    <!-- Autocomplete Dropdown -->
                    <div id="tag-suggestions" class="hidden absolute left-0 right-0 top-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-10 max-h-48 overflow-y-auto"></div>
                </div>
                
                <p class="text-xs text-gray-400 mt-2">Press Enter to add, or select from suggestions</p>
            </div>
        </div>
    </div>
</div>

<script>
const PostEditor = {
    editor: null,
    postId: <?= $postId ? $postId : 'null' ?>,
    isNew: <?= $postId ? 'false' : 'true' ?>,
    selectedTags: [],
    allTags: [],
    searchTimeout: null,

    async init() {
        await App.loadCsrfToken();
        
        // Load all tags for autocomplete
        await this.loadAllTags();
        
        if (this.postId) {
            await this.loadPost();
        }
        
        this.initEditor();
        this.renderSelectedTags();
    },

    async loadPost() {
        try {
            const post = await App.api(`/posts/${this.postId}`);
            
            document.getElementById('post-title').value = post.title || '';
            document.getElementById('post-slug').value = post.slug || '';
            document.getElementById('post-status').value = post.status || 'draft';
            document.getElementById('post-excerpt').value = post.excerpt || '';
            document.getElementById('post-image').value = post.featured_image || '';
            
            if (post.featured_image) {
                this.showImagePreview(post.featured_image);
            }
            
            // Parse content for editor
            if (post.content) {
                try {
                    this.editorData = typeof post.content === 'string' ? JSON.parse(post.content) : post.content;
                } catch(e) {
                    this.editorData = null;
                }
            }
            
            // Load post tags
            if (post.tags && Array.isArray(post.tags)) {
                this.selectedTags = post.tags;
            }
        } catch(e) {
            alert('Error loading post');
            window.location.href = 'posts.php';
        }
    },

    initEditor() {
        if (typeof EditorJS === 'undefined') {
            document.getElementById('editorjs').innerHTML = '<p class="text-red-500">Editor.js failed to load</p>';
            return;
        }

        const tools = {};
        if (typeof Header !== 'undefined') tools.header = { class: Header, config: { levels: [1,2,3,4], defaultLevel: 2 } };
        if (typeof List !== 'undefined') tools.list = { class: List, inlineToolbar: true };
        if (typeof Quote !== 'undefined') tools.quote = { class: Quote };
        if (typeof Delimiter !== 'undefined') tools.delimiter = Delimiter;
        if (typeof ImageTool !== 'undefined') {
            tools.image = {
                class: ImageTool,
                config: {
                    uploader: {
                        async uploadByFile(file) {
                            const formData = new FormData();
                            formData.append('file', file);
                            const basePath = window.location.pathname.split('/admin/')[0];
                            const res = await fetch(basePath + '/api/media/upload', {
                                method: 'POST',
                                headers: { 'X-CSRF-TOKEN': App.csrfToken },
                                body: formData
                            });
                            const json = await res.json();
                            return res.ok ? { success: 1, file: { url: json.path } } : { success: 0 };
                        }
                    }
                }
            };
        }

        this.editor = new EditorJS({
            holder: 'editorjs',
            data: this.editorData || { blocks: [] },
            placeholder: 'Start writing or type / for blocks...',
            tools: tools,
            minHeight: 300,
            onChange: () => {
                document.getElementById('save-status').textContent = 'Unsaved changes';
            }
        });
    },

    onTitleChange() {
        if (this.isNew) {
            const title = document.getElementById('post-title').value;
            document.getElementById('post-slug').value = title.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .replace(/^-|-$/g, '');
        }
        document.getElementById('save-status').textContent = 'Unsaved changes';
    },

    toggleSidebar() {
        document.getElementById('editor-sidebar').classList.toggle('hidden');
    },

    // Toast notification helper with optional action button
    showToast(message, type = 'success', action = null) {
        const existing = document.getElementById('toast-notification');
        if (existing) existing.remove();
        
        const colors = {
            success: 'background: linear-gradient(135deg, #10b981, #059669); color: white;',
            error: 'background: linear-gradient(135deg, #ef4444, #dc2626); color: white;',
            info: 'background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white;'
        };
        
        const toast = document.createElement('div');
        toast.id = 'toast-notification';
        toast.style.cssText = `position: fixed; bottom: 24px; right: 24px; padding: 16px 20px; border-radius: 12px; font-size: 14px; font-weight: 500; z-index: 9999; display: flex; align-items: center; gap: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); animation: slideIn 0.3s ease; ${colors[type]}`;
        
        const icons = { success: 'check_circle', error: 'error', info: 'save' };
        
        let actionHtml = '';
        if (action) {
            actionHtml = `<a href="${action.url}" target="_blank" style="background: rgba(255,255,255,0.2); padding: 6px 12px; border-radius: 6px; text-decoration: none; color: white; font-weight: 600; display: flex; align-items: center; gap: 4px; margin-left: 8px; transition: background 0.15s;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'"><span class="material-icons-round" style="font-size: 16px;">open_in_new</span>${action.text}</a>`;
        }
        
        toast.innerHTML = `
            <span class="material-icons-round" style="font-size: 24px;">${icons[type]}</span>
            <span>${message}</span>
            ${actionHtml}
            <button onclick="this.parentElement.remove()" style="background: none; border: none; color: white; opacity: 0.7; cursor: pointer; padding: 4px; margin-left: 8px;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'">
                <span class="material-icons-round" style="font-size: 18px;">close</span>
            </button>
        `;
        
        // Add animation keyframes
        if (!document.getElementById('toast-animation-style')) {
            const style = document.createElement('style');
            style.id = 'toast-animation-style';
            style.textContent = '@keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }';
            document.head.appendChild(style);
        }
        
        document.body.appendChild(toast);
        
        // Auto dismiss after 6 seconds (longer for action toasts)
        setTimeout(() => toast.remove(), action ? 8000 : 5000);
    },

    // ─────────────────────────────────────────────────────────
    // Tag Management Methods
    // ─────────────────────────────────────────────────────────
    
    async loadAllTags() {
        try {
            const res = await App.api('/tags');
            this.allTags = res.data || res || [];
        } catch(e) {
            this.allTags = [];
        }
    },

    searchTags(query) {
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => {
            if (!query.trim()) {
                this.hideSuggestions();
                return;
            }
            
            const lowerQuery = query.toLowerCase();
            const selectedIds = this.selectedTags.map(t => t.id);
            
            // Filter matching tags that aren't already selected
            const matches = this.allTags.filter(t => 
                t.name.toLowerCase().includes(lowerQuery) && !selectedIds.includes(t.id)
            ).slice(0, 8);
            
            this.showSuggestions(matches, query);
        }, 150);
    },

    showSuggestions(tags, query) {
        const container = document.getElementById('tag-suggestions');
        
        let html = '';
        
        // Show matching tags
        tags.forEach(t => {
            html += `<div onclick="PostEditor.addTag(${t.id}, '${t.name.replace(/'/g, "\\'")}', '${t.color || '#6b7280'}')" 
                class="px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer flex items-center justify-between">
                <span class="flex items-center">
                    <span class="w-3 h-3 rounded-full mr-2" style="background: ${t.color || '#6b7280'}"></span>
                    <span class="text-sm text-gray-900 dark:text-white">${t.name}</span>
                </span>
                <span class="text-xs text-gray-400">${t.count || 0} posts</span>
            </div>`;
        });
        
        // Add "Create new" option if no exact match
        const exactMatch = tags.find(t => t.name.toLowerCase() === query.toLowerCase());
        if (!exactMatch && query.trim()) {
            html += `<div onclick="PostEditor.createTag('${query.trim().replace(/'/g, "\\'")}')" 
                class="px-3 py-2 hover:bg-primary/10 cursor-pointer flex items-center border-t border-gray-200 dark:border-gray-700">
                <span class="material-icons-round text-primary mr-2 text-lg">add_circle</span>
                <span class="text-sm text-primary font-medium">Create "${query.trim()}"</span>
            </div>`;
        }
        
        if (html) {
            container.innerHTML = html;
            container.classList.remove('hidden');
        } else {
            this.hideSuggestions();
        }
    },

    hideSuggestions() {
        document.getElementById('tag-suggestions').classList.add('hidden');
    },

    handleTagKeydown(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            const input = document.getElementById('tag-input');
            const query = input.value.trim();
            
            if (!query) return;
            
            // Check if there's an exact match in allTags
            const exactMatch = this.allTags.find(t => t.name.toLowerCase() === query.toLowerCase());
            
            if (exactMatch && !this.selectedTags.find(t => t.id === exactMatch.id)) {
                this.addTag(exactMatch.id, exactMatch.name, exactMatch.color);
            } else if (!exactMatch) {
                // Create new tag
                this.createTag(query);
            }
        } else if (event.key === 'Escape') {
            this.hideSuggestions();
        }
    },

    addTag(id, name, color = '#6b7280') {
        // Check if already added
        if (this.selectedTags.find(t => t.id === id)) return;
        
        this.selectedTags.push({ id, name, color });
        this.renderSelectedTags();
        
        // Clear input
        document.getElementById('tag-input').value = '';
        this.hideSuggestions();
        document.getElementById('save-status').textContent = 'Unsaved changes';
    },

    removeTag(id) {
        this.selectedTags = this.selectedTags.filter(t => t.id !== id);
        this.renderSelectedTags();
        document.getElementById('save-status').textContent = 'Unsaved changes';
    },

    renderSelectedTags() {
        const container = document.getElementById('selected-tags');
        if (!container) return;
        
        container.innerHTML = this.selectedTags.map(t => `
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium text-white" style="background: ${t.color || '#6b7280'}">
                ${t.name}
                <button onclick="PostEditor.removeTag(${t.id})" class="ml-1 hover:opacity-80">
                    <span class="material-icons-round text-sm">close</span>
                </button>
            </span>
        `).join('');
    },

    async createTag(name) {
        try {
            const res = await App.api('/tags', 'POST', { name });
            
            if (res && res.id) {
                // Add to allTags and select it
                const newTag = { id: res.id, name: res.name || name, color: res.color || '#8b5cf6', count: 0 };
                this.allTags.push(newTag);
                this.addTag(newTag.id, newTag.name, newTag.color);
                this.showToast(`Tag "${name}" created!`, 'success');
            } else {
                this.showToast(res.error || 'Failed to create tag', 'error');
            }
        } catch(e) {
            this.showToast('Error creating tag: ' + e.message, 'error');
        }
    },

    // Preview the post
    preview() {
        const slug = document.getElementById('post-slug').value;
        if (!slug) {
            this.showToast('Please add a URL slug first', 'error');
            return;
        }
        // Open preview in new tab
        const basePath = window.location.pathname.split('/admin/')[0];
        window.open(basePath + '/' + slug, '_blank');
    },

    async save(status) {
        const title = document.getElementById('post-title').value;
        const slug = document.getElementById('post-slug').value;
        const excerpt = document.getElementById('post-excerpt').value;
        const featured_image = document.getElementById('post-image').value;
        const postStatus = status || document.getElementById('post-status').value;

        if (!title.trim()) {
            this.showToast('Please enter a title', 'error');
            return;
        }

        let content = null;
        if (this.editor) {
            try { content = await this.editor.save(); } catch(e) {}
        }

        document.getElementById('save-status').textContent = 'Saving...';

        try {
            const url = this.postId ? `/posts/${this.postId}` : '/posts';
            const method = this.postId ? 'PUT' : 'POST';
            
            const res = await App.api(url, method, {
                title, slug, status: postStatus, excerpt, featured_image,
                content: JSON.stringify(content),
                tag_ids: this.selectedTags.map(t => t.id)
            });

            if (res && (res.id || res.message)) {
                document.getElementById('save-status').textContent = '';
                
                if (!this.postId && res.id) {
                    this.postId = res.id;
                    this.isNew = false;
                    history.replaceState(null, '', 'post-editor.php?id=' + res.id);
                }
                
                // Show enhanced toast notification
                const basePath = window.location.pathname.split('/admin/')[0];
                const postUrl = basePath + '/' + slug;
                
                if (postStatus === 'published') {
                    this.showToast('Post published successfully!', 'success', {
                        text: 'View Post',
                        url: postUrl
                    });
                } else {
                    this.showToast('Draft saved successfully!', 'info');
                }
                
                // Update the status dropdown
                document.getElementById('post-status').value = postStatus;
            } else {
                document.getElementById('save-status').textContent = '';
                this.showToast('Failed to save post', 'error');
            }
        } catch(e) {
            document.getElementById('save-status').textContent = '';
            this.showToast('Error: ' + e.message, 'error');
        }
    },

    async selectImage() {
        let images = [];
        try {
            const res = await App.api('/media?type=image&limit=30');
            images = res.data || (Array.isArray(res) ? res : []);
        } catch(e) {}

        const modal = document.createElement('div');
        modal.id = 'image-modal';
        modal.style.cssText = 'position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 9999; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.5);';
        modal.onclick = (e) => { if (e.target === modal) modal.remove(); };
        modal.innerHTML = `
            <div style="background: white; border-radius: 12px; width: 90%; max-width: 900px; max-height: 80vh; overflow: hidden; box-shadow: 0 25px 50px rgba(0,0,0,0.25);" class="dark:bg-gray-800">
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 24px; border-bottom: 1px solid #e5e7eb;">
                    <h3 style="font-size: 18px; font-weight: 600; margin: 0;" class="dark:text-white">Select Featured Image</h3>
                    <button onclick="document.getElementById('image-modal').remove()" style="background: none; border: none; cursor: pointer; padding: 4px;">
                        <span class="material-icons-round" style="color: #9ca3af;">close</span>
                    </button>
                </div>
                <div style="padding: 16px 24px; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                    <label style="display: flex; align-items: center; justify-content: center; padding: 16px; border: 2px dashed #d1d5db; border-radius: 8px; cursor: pointer; transition: border-color 0.15s;" onmouseover="this.style.borderColor='#8b5cf6'" onmouseout="this.style.borderColor='#d1d5db'">
                        <input type="file" accept="image/*" style="display: none;" onchange="PostEditor.uploadImage(this.files[0])">
                        <span class="material-icons-round" style="color: #8b5cf6; margin-right: 8px;">cloud_upload</span>
                        <span style="color: #6b7280; font-size: 14px;">Upload new image from your computer</span>
                    </label>
                </div>
                <div style="padding: 24px; overflow-y: auto; max-height: 50vh;">
                    ${images.length ? '<p style="font-size: 12px; color: #9ca3af; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.05em;">Or select from library</p>' : ''}
                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px;" id="image-grid">
                        ${images.map(img => `
                            <div onclick="PostEditor.setImage('${img.path}')" style="aspect-ratio: 1; cursor: pointer; border-radius: 8px; overflow: hidden; border: 2px solid transparent; transition: border-color 0.15s;" onmouseover="this.style.borderColor='#8b5cf6'" onmouseout="this.style.borderColor='transparent'">
                                <img src="${img.path}" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        `).join('')}
                        ${!images.length ? '<p style="grid-column: span 4; text-align: center; padding: 32px; color: #6b7280;">No images in library yet.</p>' : ''}
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    },

    async uploadImage(file) {
        if (!file) return;
        
        // Show uploading state
        const grid = document.getElementById('image-grid');
        const uploadingDiv = document.createElement('div');
        uploadingDiv.style.cssText = 'aspect-ratio: 1; display: flex; align-items: center; justify-content: center; background: #f3f4f6; border-radius: 8px; border: 2px solid #8b5cf6;';
        uploadingDiv.innerHTML = '<div style="text-align: center;"><div class="material-icons-round" style="color: #8b5cf6; animation: spin 1s linear infinite;">refresh</div><p style="font-size: 12px; color: #6b7280; margin-top: 4px;">Uploading...</p></div>';
        grid.insertBefore(uploadingDiv, grid.firstChild);
        
        try {
            const formData = new FormData();
            formData.append('file', file);
            
            const basePath = window.location.pathname.split('/admin/')[0];
            const res = await fetch(basePath + '/api/media/upload', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': App.csrfToken },
                body: formData
            });
            const json = await res.json();
            
            if (res.ok && json.path) {
                this.setImage(json.path);
                this.showToast('Image uploaded and selected!', 'success');
            } else {
                uploadingDiv.remove();
                this.showToast(json.error || 'Upload failed', 'error');
            }
        } catch(e) {
            uploadingDiv.remove();
            this.showToast('Upload error: ' + e.message, 'error');
        }
    },

    setImage(path) {
        document.getElementById('post-image').value = path;
        this.showImagePreview(path);
        document.getElementById('image-modal')?.remove();
        document.getElementById('save-status').textContent = 'Unsaved changes';
    },

    showImagePreview(path) {
        document.getElementById('featured-image-panel').innerHTML = `
            <div class="relative group cursor-pointer" onclick="PostEditor.selectImage()">
                <img src="${path}" class="w-full h-32 object-cover rounded-lg">
                <div class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg">
                    <span class="material-icons-round text-white text-2xl">edit</span>
                </div>
                <button onclick="event.stopPropagation(); PostEditor.removeImage()" class="absolute top-2 right-2 p-1 bg-black/50 text-white rounded opacity-0 group-hover:opacity-100">
                    <span class="material-icons-round text-sm">close</span>
                </button>
            </div>
            <input type="hidden" id="post-image" value="${path}">
        `;
    },

    removeImage() {
        document.getElementById('featured-image-panel').innerHTML = `
            <button onclick="PostEditor.selectImage()" class="w-full py-6 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-lg text-center hover:border-primary transition-colors">
                <span class="material-icons-round text-2xl text-gray-400">add_photo_alternate</span>
                <p class="text-sm text-gray-500 mt-1">Set featured image</p>
            </button>
            <input type="hidden" id="post-image" value="">
        `;
        document.getElementById('save-status').textContent = 'Unsaved changes';
    }
};

document.addEventListener('DOMContentLoaded', () => PostEditor.init());
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
