<div class="bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold mb-2">My Account</h1>
            <p class="text-gray-600">Update your profile details below</p>
        </div>

        <?php if (isset($_COOKIE['message'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
                <span class="block sm:inline"><?= $_COOKIE['message'] ?></span>
                <?php unset($_COOKIE['message']); ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-6">Profile Information</h2>

            <form action="index.php?route=update_profile" method="POST" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                        <input
                            type="text"
                            id="first_name"
                            name="first_name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-green-500"
                            value="<?= $_COOKIE['first_name'] ?? '' ?>"
                            required
                        >
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                        <input
                            type="text"
                            id="last_name"
                            name="last_name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-green-500"
                            value="<?=  $_COOKIE['last_name'] ?? '' ?>"
                            required
                        >
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            readonly
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-green-500"
                            value="<?= $_COOKIE['email'] ?? '' ?>"
                            required
                        >
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-green-500"
                            value="<?= $_COOKIE['phone'] ?? '' ?>"
                            required
                        >
                    </div>
                </div>

                <div class="text-right">
                    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">
                        Save Changes
                    </button>
                </div>
            </form>

            <div class="mt-8 text-right">
                <a href="index.php?route=dashboard" class="text-green-600 hover:underline font-medium">
                    → Go to My Rentals
                </a>
            </div>
        </div>
    </div>
</div>
