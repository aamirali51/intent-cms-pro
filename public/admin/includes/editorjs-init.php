<?php
// public/admin/includes/editorjs-init.php
?>
<script>
    // Shared Editor.js Logic
    App.initEditor = function(data) {
        if (this.editor) {
            try { this.editor.destroy(); } catch (e) { }
            this.editor = null;
        }

        // Safe access to tools
        const tools = {};
        if (window.Header) tools.header = window.Header;
        if (window.List) tools.list = window.List;
        if (window.ImageTool) {
            tools.image = {
                class: window.ImageTool,
                config: {
                    uploader: {
                        uploadByFile: async (file) => {
                            const formData = new FormData();
                            formData.append('file', file);
                            try {
                                const basePath = window.location.pathname.split('/admin/')[0];
                                const res = await fetch(basePath + '/api/media/upload', {
                                    method: 'POST',
                                    headers: { 'X-CSRF-TOKEN': this.csrfToken },
                                    body: formData
                                });
                                const json = await res.json();
                                if (res.ok) {
                                    return { success: 1, file: { url: json.path, ...json } };
                                }
                            } catch (e) { console.error('Image upload failed', e); }
                            return { success: 0 };
                        }
                    }
                }
            };
        }

        this.editor = new EditorJS({
            holder: 'editorjs',
            data: data || {},
            placeholder: 'Type your story...',
            tools: tools,
            minHeight: 0, // We handle height with CSS
        });
    };
</script>
