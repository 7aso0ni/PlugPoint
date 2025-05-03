<!-- views/layouts/main.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? "My Shop" ?> </title>
    <script src="https://cdn.tailwindcss.com"></script> <!-- ✅ Tailwind CDN -->
    <script src="https://kit.fontawesome.com/851d1efc24.js" crossorigin="anonymous"></script>
</head>

<body class="flex flex-col min-h-screen">
    <?= $content ?? '' ?>
    <footer class="bg-gray-900 text-gray-300 py-12">
        <div class="container mx-auto text-center">
            <p class="text-gray-400">&copy; <?php echo date('Y'); ?> BorrowMyCharger. All rights reserved.</p>
        </div>
    </footer>

</body>

</html>