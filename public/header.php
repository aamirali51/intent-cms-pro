<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? site_title()) ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDescription ?? site_tagline()) ?>">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#8b5cf6',
                        primaryHover: '#7c3aed',
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    
    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex flex-col">
    <!-- Header -->
    <header class="bg-white border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <a href="/" class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2L3 7v11h14V7l-7-5zm0 2.5L15 8v8H5V8l5-3.5z"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-gray-900"><?= htmlspecialchars(site_title()) ?></span>
                </a>
                
                <!-- Navigation -->
                <nav class="flex items-center gap-8">
                    <a href="/" class="text-sm font-medium text-gray-600 hover:text-primary transition-colors">Home</a>
                    <a href="/blog" class="text-sm font-medium text-gray-600 hover:text-primary transition-colors">Blog</a>
                </nav>
            </div>
        </div>
    </header>
