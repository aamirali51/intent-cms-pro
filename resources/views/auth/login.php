<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Intent CMS - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#7f13ec",
                        "background-light": "#f7f6f8",
                        "background-dark": "#191022",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                    borderRadius: { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" },
                },
            },
        }
    </script>
</head>
<body class="bg-background-light text-[#111418] font-display">
    <!-- Main Container -->
    <div class="relative flex h-screen w-full flex-col overflow-hidden items-center justify-center p-4">
        <!-- Login Card -->
        <div class="flex flex-col w-full max-w-[480px] bg-white rounded-xl shadow-sm border border-[#e5e7eb] overflow-hidden">
            <!-- Header Section -->
            <div class="flex flex-col items-center pt-12 pb-6 px-10 text-center">
                <div class="flex items-center gap-3 mb-6 text-[#111418]">
                    <div class="size-8 text-primary">
                        <svg class="w-full h-full" fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                            <path d="M42.4379 44C42.4379 44 36.0744 33.9038 41.1692 24C46.8624 12.9336 42.2078 4 42.2078 4L7.01134 4C7.01134 4 11.6577 12.932 5.96912 23.9969C0.876273 33.9029 7.27094 44 7.27094 44L42.4379 44Z" fill="currentColor"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold leading-tight tracking-[-0.015em]">Intent CMS</h2>
                </div>
                <h1 class="text-xl font-bold leading-tight tracking-tight mb-2">Welcome back</h1>
                <p class="text-[#637588] text-sm">Please enter your details to sign in.</p>
            </div>
            
            <!-- Flash Messages -->
            <?php if ($error = flash('error')): ?>
            <div class="mx-8 mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <?php if ($success = flash('success')): ?>
            <div class="mx-8 mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
                <?= htmlspecialchars($success) ?>
            </div>
            <?php endif; ?>

            <!-- Form Section -->
            <form method="POST" action="/login" class="flex flex-col gap-5 px-8 pb-10">
                <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                
                <!-- Email/Username Field -->
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium leading-normal text-[#111418]" for="email">
                        Username or Email
                    </label>
                    <div class="relative flex items-center">
                        <span class="absolute left-4 text-[#637588] material-symbols-outlined text-[20px]">person</span>
                        <input class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-[#111418] focus:outline-0 focus:ring-2 focus:ring-primary/50 border border-[#d1d5db] bg-white focus:border-primary h-12 placeholder:text-[#9ca3af] pl-11 pr-4 text-sm font-normal leading-normal transition-all duration-200" id="email" name="email" placeholder="Enter your email" type="email" required autofocus>
                    </div>
                </div>
                
                <!-- Password Field -->
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium leading-normal text-[#111418]" for="password">
                        Password
                    </label>
                    <div class="relative flex items-center">
                        <span class="absolute left-4 text-[#637588] material-symbols-outlined text-[20px]">lock</span>
                        <input class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-[#111418] focus:outline-0 focus:ring-2 focus:ring-primary/50 border border-[#d1d5db] bg-white focus:border-primary h-12 placeholder:text-[#9ca3af] pl-11 pr-4 text-sm font-normal leading-normal transition-all duration-200" id="password" name="password" placeholder="Enter your password" type="password" required>
                    </div>
                </div>
                
                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <div class="relative flex items-center">
                            <input class="peer h-4 w-4 cursor-pointer appearance-none rounded border border-[#d1d5db] bg-white checked:bg-primary checked:border-primary focus:ring-2 focus:ring-primary/20 focus:ring-offset-0 transition-all duration-200" type="checkbox" name="remember">
                            <span class="material-symbols-outlined absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 text-[12px] text-white opacity-0 peer-checked:opacity-100 pointer-events-none">check</span>
                        </div>
                        <span class="text-sm text-[#637588] group-hover:text-[#111418] transition-colors">Remember me</span>
                    </label>
                    <a class="text-sm font-medium text-primary hover:text-[#6a10c4] transition-colors" href="#">Forgot password?</a>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" class="flex w-full cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-4 bg-primary hover:bg-[#6a10c4] text-white text-base font-bold leading-normal tracking-[0.015em] transition-colors duration-200 shadow-md hover:shadow-lg mt-2">
                    <span class="truncate">Sign In</span>
                </button>
            </form>
            
            <!-- Footer Area inside card -->
            <div class="bg-[#f9fafb] border-t border-[#e5e7eb] p-4 text-center">
                <p class="text-sm text-[#637588]">
                    Powered by 
                    <a class="text-primary font-medium hover:text-[#6a10c4] transition-colors" href="https://packagist.org/packages/intent/framework">Intent Framework</a>
                </p>
            </div>
        </div>
        
        <!-- Background decorative elements -->
        <div class="fixed top-0 left-0 w-full h-full pointer-events-none -z-10 overflow-hidden">
            <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-primary/5 rounded-full blur-[120px]"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-primary/10 rounded-full blur-[120px]"></div>
        </div>
        
        <!-- Footer outside card -->
        <div class="mt-8 text-center">
            <div class="flex gap-6 justify-center text-xs text-[#9ca3af] font-medium">
                <a class="hover:text-primary transition-colors" href="#">Privacy Policy</a>
                <a class="hover:text-primary transition-colors" href="#">Terms of Service</a>
                <a class="hover:text-primary transition-colors" href="#">Help Center</a>
            </div>
            <p class="text-xs text-[#9ca3af] mt-4">© 2024 Intent CMS. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
