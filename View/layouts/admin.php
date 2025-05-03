<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin Dashboard' ?> - PlugPoint</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/851d1efc24.js" crossorigin="anonymous"></script>
    <!-- Leaflet CSS and JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

</head>

<body class="bg-gray-100 min-h-screen flex flex-col">
    <!-- Top Navigation -->
    <nav class="bg-indigo-700 shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="index.php?route=admin/dashboard" class="text-white font-bold text-xl">PlugPoint
                            Admin</a>
                    </div>
                </div>
                <div class="flex items-center">
                    <div class="hidden md:ml-4 md:flex md:items-center">
                        <a href="index.php?route=home"
                            class="px-3 py-2 rounded-md text-sm font-medium text-white hover:bg-indigo-500">
                            View Site
                        </a>

                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Side Navigation and Content -->
    <div class="flex flex-1">
        <!-- Sidebar -->
        <div class="bg-indigo-800 text-white w-64 p-4 hidden md:block">
            <nav class="mt-5">
                <div class="space-y-1">
                    <a href="index.php?route=admin/dashboard"
                        class="group flex items-center px-2 py-2 text-base font-medium rounded-md <?= $route === 'admin/dashboard' ? 'bg-indigo-900 text-white' : 'text-indigo-100 hover:bg-indigo-700' ?>">
                        <svg class="mr-3 h-6 w-6 <?= $route === 'admin/dashboard' ? 'text-white' : 'text-indigo-300 group-hover:text-white' ?>"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>

                    <a href="index.php?route=admin/users"
                        class="group flex items-center px-2 py-2 text-base font-medium rounded-md <?= $route === 'admin/users' ? 'bg-indigo-900 text-white' : 'text-indigo-100 hover:bg-indigo-700' ?>">
                        <svg class="mr-3 h-6 w-6 <?= $route === 'admin/users' ? 'text-white' : 'text-indigo-300 group-hover:text-white' ?>"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Users
                    </a>

                    <a href="index.php?route=admin/charge_points"
                        class="group flex items-center px-2 py-2 text-base font-medium rounded-md <?= $route === 'admin/charge_points' ? 'bg-indigo-900 text-white' : 'text-indigo-100 hover:bg-indigo-700' ?>">
                        <svg class="mr-3 h-6 w-6 <?= $route === 'admin/charge_points' ? 'text-white' : 'text-indigo-300 group-hover:text-white' ?>"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Charging Stations
                    </a>

                    <a href="index.php?route=admin/bookings"
                        class="group flex items-center px-2 py-2 text-base font-medium rounded-md <?= $route === 'admin/bookings' ? 'bg-indigo-900 text-white' : 'text-indigo-100 hover:bg-indigo-700' ?>">
                        <svg class="mr-3 h-6 w-6 <?= $route === 'admin/bookings' ? 'text-white' : 'text-indigo-300 group-hover:text-white' ?>"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Bookings
                    </a>

                    <a href="index.php?route=admin/reports"
                        class="group flex items-center px-2 py-2 text-base font-medium rounded-md <?= $route === 'admin/reports' ? 'bg-indigo-900 text-white' : 'text-indigo-100 hover:bg-indigo-700' ?>">
                        <svg class="mr-3 h-6 w-6 <?= $route === 'admin/reports' ? 'text-white' : 'text-indigo-300 group-hover:text-white' ?>"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Reports
                    </a>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <?= $content ?>
        </div>
    </div>
</body>

</html>