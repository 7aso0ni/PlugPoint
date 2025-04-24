<!-- views/layouts/main.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? "PlugPoint"?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/851d1efc24.js" crossorigin="anonymous"></script>
</head>
<body class="flex flex-col min-h-screen">
<header class="sticky top-0 z-50 w-full border-b bg-white shadow-md">
    <div class="container mx-auto flex h-16 items-center justify-between">
        <a href="index.php" class="flex items-center gap-2">
            <span class="text-2xl font-bold text-green-600">🔋 PlugPoint</span>
        </a>

        <?php if (!empty($_COOKIE['loggedIn'])): ?>
            <!-- Navigation for logged in users -->
            <nav class="hidden md:flex items-center gap-6">
                <a href="index.php?route=shop" class="text-sm font-medium hover:text-green-600">Browse Chargers</a>
                <a href="index.php?route=dashboard" class="text-sm font-medium hover:text-green-600">My Rentals</a>
                <a href="index.php?route=account" class="text-sm font-medium hover:text-green-600">My Account</a>
            </nav>
            <div class="flex items-center gap-4">
                <form method="POST" action="index.php?route=logout">
                    <button type="submit" class="bg-green-600 text-white py-2 px-4 rounded-lg">Log out</button>
                </form>
            </div>
        <?php else: ?>
            <!-- Navigation for guests -->
            <div class="flex items-center gap-4">
                <a href="index.php?route=login" class="border border-green-600 py-2 px-4 rounded-lg hidden md:block">Log in</a>
                <a href="index.php?route=signup" class="bg-green-600 text-white py-2 px-4 rounded-lg">Sign up</a>
            </div>
        <?php endif; ?>
    </div>
</header>

<main class="flex-grow">
    <?= $content ?? '' ?>
</main>

<footer class="bg-gray-900 text-gray-300 py-12">
    <div class="container mx-auto text-center">
        <p class="text-gray-400">&copy; <?php echo date('Y'); ?> PlugPoint. All rights reserved.</p>
    </div>
</footer>

</body>
</html>