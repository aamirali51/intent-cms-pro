/**
 * Editor.js Block Renderer
 * Converts Editor.js JSON blocks to HTML
 */
function renderEditorJS(blocks, container) {
    if (!blocks || !Array.isArray(blocks) || !container) {
        return;
    }

    let html = '';

    blocks.forEach(block => {
        switch (block.type) {
            case 'paragraph':
                html += `<p class="text-gray-700 leading-relaxed mb-4">${block.data.text}</p>`;
                break;

            case 'header':
                const level = block.data.level || 2;
                const headerClasses = {
                    1: 'text-4xl font-bold text-gray-900 mb-6 mt-8',
                    2: 'text-3xl font-bold text-gray-900 mb-5 mt-8',
                    3: 'text-2xl font-semibold text-gray-900 mb-4 mt-6',
                    4: 'text-xl font-semibold text-gray-900 mb-3 mt-5',
                    5: 'text-lg font-medium text-gray-900 mb-2 mt-4',
                    6: 'text-base font-medium text-gray-900 mb-2 mt-4'
                };
                html += `<h${level} class="${headerClasses[level] || headerClasses[2]}">${block.data.text}</h${level}>`;
                break;

            case 'list':
                const listTag = block.data.style === 'ordered' ? 'ol' : 'ul';
                const listClass = block.data.style === 'ordered'
                    ? 'list-decimal list-inside space-y-2 mb-4 text-gray-700'
                    : 'list-disc list-inside space-y-2 mb-4 text-gray-700';
                const items = block.data.items.map(item => `<li>${item}</li>`).join('');
                html += `<${listTag} class="${listClass}">${items}</${listTag}>`;
                break;

            case 'image':
                const caption = block.data.caption ? `<figcaption class="text-center text-sm text-gray-500 mt-3">${block.data.caption}</figcaption>` : '';
                const stretched = block.data.stretched ? 'w-full' : 'max-w-2xl mx-auto';
                html += `
                    <figure class="my-8 ${stretched}">
                        <img src="${block.data.file?.url || block.data.url}" 
                             alt="${block.data.caption || ''}" 
                             class="w-full rounded-xl shadow-lg">
                        ${caption}
                    </figure>
                `;
                break;

            case 'quote':
                const quoteCaption = block.data.caption ? `<cite class="block mt-3 text-sm text-gray-500 not-italic">— ${block.data.caption}</cite>` : '';
                html += `
                    <blockquote class="border-l-4 border-primary pl-6 py-2 my-6 bg-gray-50 rounded-r-lg">
                        <p class="text-lg text-gray-700 italic">${block.data.text}</p>
                        ${quoteCaption}
                    </blockquote>
                `;
                break;

            case 'delimiter':
                html += `<hr class="my-10 border-0 h-px bg-gradient-to-r from-transparent via-gray-300 to-transparent">`;
                break;

            case 'code':
                html += `
                    <pre class="bg-gray-900 text-gray-100 p-4 rounded-xl overflow-x-auto my-6">
                        <code>${block.data.code}</code>
                    </pre>
                `;
                break;

            default:
                // Fallback for unknown block types
                if (block.data && block.data.text) {
                    html += `<p class="text-gray-700 leading-relaxed mb-4">${block.data.text}</p>`;
                }
        }
    });

    container.innerHTML = html;
}

// Auto-render on page load
document.addEventListener('DOMContentLoaded', function () {
    const contentContainers = document.querySelectorAll('[data-editorjs-content]');
    contentContainers.forEach(container => {
        try {
            const jsonData = container.getAttribute('data-editorjs-content');
            if (jsonData) {
                const parsed = JSON.parse(jsonData);
                if (parsed && parsed.blocks) {
                    renderEditorJS(parsed.blocks, container);
                }
            }
        } catch (e) {
            console.error('Error parsing Editor.js content:', e);
        }
    });
});
