    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 mt-auto">
        <div class="max-w-6xl mx-auto px-4 py-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-primary rounded flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2L3 7v11h14V7l-7-5zm0 2.5L15 8v8H5V8l5-3.5z"/>
                        </svg>
                    </div>
                    <span class="text-sm text-gray-500"><?= htmlspecialchars(site_title()) ?></span>
                </div>
                <p class="text-sm text-gray-400">
                    &copy; <?= date('Y') ?> <?= htmlspecialchars(site_title()) ?>. All rights reserved.
                </p>
            </div>
        </div>
    </footer>
    
    <!-- Editor.js Renderer -->
    <script src="/assets/js/editorjs-renderer.js"></script>
</body>
</html>
