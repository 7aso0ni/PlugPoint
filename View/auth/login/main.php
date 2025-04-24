<main class="py-10 flex-1">
    <div class="container mx-auto px-4">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold">Welcome Back</h1>
                <p class="text-gray-600 mt-2">Log in to your account to continue</p>
            </div>

            <form class="space-y-6" method="post">
                <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($error)): ?>
                    <div class="bg-red-100 text-red-700 p-3 rounded"><?= $error ?></div>
                <?php endif; ?>

                <div>
                    <label for="email" class="block text-gray-700 font-medium mb-2">Email Address</label>
                    <input type="email" id="email" name="email"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                           placeholder="your@email.com" required>
                </div>

                <div>
                    <div class="flex justify-between mb-2">
                        <label for="password" class="block text-gray-700 font-medium">Password</label>
                        <a href="#" class="text-sm text-green-600 hover:underline">Forgot Password?</a>
                    </div>
                    <div class="relative">
                        <input type="password" id="password" name="password"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                               placeholder="••••••••" required>
                        <button type="button" class="absolute right-3 top-3 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 rounded-lg transition duration-200">
                        Log In
                    </button>
                </div>

                <div class="relative flex items-center justify-center mt-6">
                    <div class="border-t border-gray-300 absolute w-full"></div>
                    <div class="bg-white px-4 relative text-sm text-gray-500">or continue with</div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <button type="button" class="flex justify-center items-center py-3 px-4 border border-gray-300 rounded-lg hover:bg-gray-50 transition duration-200">
                        <i class="fab fa-google mr-2"></i> Google
                    </button>
                    <button type="button" class="flex justify-center items-center py-3 px-4 border border-gray-300 rounded-lg hover:bg-gray-50 transition duration-200">
                        <i class="fab fa-apple mr-2"></i> Apple
                    </button>
                </div>

                <p class="text-center text-gray-600 mt-6">
                    Don't have an account? <a href="index.php?route=signup" class="text-green-600 hover:underline font-medium">Sign up</a>
                </p>
            </form>
        </div>
    </div>
</main>