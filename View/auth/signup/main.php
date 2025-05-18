<main class="py-10">
    <div class="container mx-auto px-4">
        <div class="max-w-lg mx-auto bg-white rounded-lg shadow p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold">Create an Account</h1>
                <p class="text-gray-600 mt-2">Join our community of EV owners and charger hosts</p>
            </div>

            <form class="space-y-6" method="post">
                <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($error)): ?>
                    <div class="bg-red-100 text-red-700 p-3 rounded"><?= $error ?></div>
                <?php endif; ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="first_name" class="block text-gray-700 font-medium mb-2">First Name</label>
                        <input type="text" id="first_name" name="first_name"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                               placeholder="John" required>
                    </div>
                    <div>
                        <label for="last_name" class="block text-gray-700 font-medium mb-2">Last Name</label>
                        <input type="text" id="last_name" name="last_name"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                               placeholder="Doe" required>
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-gray-700 font-medium mb-2">Email Address</label>
                    <input type="email" id="email" name="email"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                           placeholder="your@email.com" required>
                </div>

                <div>
                    <label for="phone" class="block text-gray-700 font-medium mb-2">Phone Number</label>
                    <input type="tel" id="phone" name="phone"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                           placeholder="(123) 456-7890">
                </div>

                <div>
                    <label for="password" class="block text-gray-700 font-medium mb-2">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                               placeholder="••••••••" required>
                        <button type="button" class="absolute right-3 top-3 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Password must be at least 8 characters long</p>
                </div>

                <div>
                    <label for="confirm_password" class="block text-gray-700 font-medium mb-2">Confirm Password</label>
                    <div class="relative">
                        <input type="password" id="confirm_password" name="confirm_password"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                               placeholder="••••••••" required>
                        <button type="button" class="absolute right-3 top-3 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">I am registering as:</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="border border-gray-300 rounded-lg p-4 cursor-pointer hover:border-green-500 transition duration-200">
                            <input type="radio" id="ev_owner" name="user_type" value="ev_owner" class="sr-only">
                            <label for="ev_owner" class="flex items-start cursor-pointer">
                                <div class="h-5 w-5 border border-gray-300 rounded-full flex items-center justify-center mr-3 mt-1">
                                    <div class="h-3 w-3 bg-green-500 rounded-full hidden"></div>
                                </div>
                                <div>
                                    <span class="block font-medium">EV Owner</span>
                                    <span class="text-sm text-gray-500">I want to find charging points</span>
                                </div>
                            </label>
                        </div>
                        <div class="border border-gray-300 rounded-lg p-4 cursor-pointer hover:border-green-500 transition duration-200">
                            <input type="radio" id="charger_host" name="user_type" value="charger_host" class="sr-only">
                            <label for="charger_host" class="flex items-start cursor-pointer">
                                <div class="h-5 w-5 border border-gray-300 rounded-full flex items-center justify-center mr-3 mt-1">
                                    <div class="h-3 w-3 bg-green-500 rounded-full hidden"></div>
                                </div>
                                <div>
                                    <span class="block font-medium">Charger Host</span>
                                    <span class="text-sm text-gray-500">I want to share my charger</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex items-center mb-4">
                    <input type="checkbox" id="not-robot" name="not-robot" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded" required>
                    <label for="not-robot" class="ml-2 block text-sm text-gray-700">
                        I'm not a robot
                    </label>
                </div>

                <div>
                    <button type="submit"
                            id="create-acc"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 rounded-lg transition duration-200">
                        Create Account
                    </button>
                </div>

                <div class="relative flex items-center justify-center mt-6">
                    <div class="border-t border-gray-300 absolute w-full"></div>
                    <div class="bg-white px-4 relative text-sm text-gray-500">or sign up with</div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <button type="button"
                            class="flex justify-center items-center py-3 px-4 border border-gray-300 rounded-lg hover:bg-gray-50 transition duration-200">
                        <i class="fab fa-google mr-2"></i> Google
                    </button>
                    <button type="button"
                            class="flex justify-center items-center py-3 px-4 border border-gray-300 rounded-lg hover:bg-gray-50 transition duration-200" id="submit-button">
                        <i class="fab fa-apple mr-2"></i> Apple
                    </button>
                </div>

                <p class="text-center text-gray-600 mt-6">
                    Already have an account? <a href="index.php?route=login"
                                                class="text-green-600 hover:underline font-medium">Log in</a>
                </p>
            </form>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get all radio buttons
        const radioButtons = document.querySelectorAll('input[name="user_type"]');
        
        // Add click event to each radio button's parent div
        document.querySelectorAll('input[name="user_type"]').forEach(radio => {
            const parentDiv = radio.closest('.border');
            
            parentDiv.addEventListener('click', function() {
                // Uncheck all radio buttons and reset styles
                radioButtons.forEach(rb => {
                    rb.checked = false;
                    const div = rb.closest('.border');
                    div.classList.remove('border-green-500');
                    div.classList.add('border-gray-300');
                    div.querySelector('.h-3').classList.add('hidden');
                });
                
                // Check the clicked radio button and update styles
                radio.checked = true;
                parentDiv.classList.remove('border-gray-300');
                parentDiv.classList.add('border-green-500');
                parentDiv.querySelector('.h-3').classList.remove('hidden');
            });
        });
        
        // Set EV Owner as default selection
        if (radioButtons.length > 0) {
            const evOwnerDiv = document.getElementById('ev_owner').closest('.border');
            evOwnerDiv.click();
        }
    });
</script>
