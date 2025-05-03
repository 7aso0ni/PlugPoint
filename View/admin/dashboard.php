<?php
// Ensure this page is only accessible by admins
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 2) {
    header("Location: /PlugPoint/index.php?route=login");
    exit();
}
?>

<div class="bg-gray-100 min-h-screen">
    <!-- Admin Header -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">Welcome, <?= htmlspecialchars($_SESSION['user']['name']) ?></span>
                    <form method="POST" action="index.php?route=logout">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M3 3a1 1 0 00-1 1v12a1 1 0 001 1h12a1 1 0 001-1V4a1 1 0 00-1-1H3zm5 4a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1zm0 4a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1zm-3 1a1 1 0 100-2H4a1 1 0 100 2h1z"
                                    clip-rule="evenodd" />
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Dashboard Content -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Summary Stats -->
        <div class="px-4 py-6 sm:px-0">
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Total Users -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-gray-900"><?= $stats['totalUsers'] ?>
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <div class="text-sm">
                            <a href="index.php?route=admin/users"
                                class="font-medium text-indigo-600 hover:text-indigo-900">View all users</a>
                        </div>
                    </div>
                </div>

                <!-- Charge Points -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Charging Stations</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-gray-900">
                                            <?= $stats['totalChargePoints'] ?></div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <div class="text-sm">
                            <a href="index.php?route=admin/charge_points"
                                class="font-medium text-green-600 hover:text-green-900">Manage stations</a>
                        </div>
                    </div>
                </div>

                <!-- Bookings -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Bookings (Pending)</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-gray-900">
                                            <?= $stats['pendingBookings'] ?></div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <div class="text-sm">
                            <a href="index.php?route=admin/bookings"
                                class="font-medium text-yellow-600 hover:text-yellow-900">View all bookings</a>
                        </div>
                    </div>
                </div>

                <!-- Revenue -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Est. Monthly Revenue</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-gray-900">
                                            $<?= number_format($stats['estimatedRevenue'], 2) ?></div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <div class="text-sm">
                            <a href="index.php?route=admin/reports"
                                class="font-medium text-blue-600 hover:text-blue-900">View reports</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="px-4 sm:px-0">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Recent Bookings</h2>
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <ul class="divide-y divide-gray-200">
                    <?php if (empty($recentBookings)): ?>
                        <li class="px-6 py-4">
                            <p class="text-gray-500 text-center">No recent bookings found.</p>
                        </li>
                    <?php else: ?>
                        <?php foreach ($recentBookings as $booking): ?>
                            <li>
                                <a href="index.php?route=admin/booking_details&id=<?= $booking['id'] ?>"
                                    class="block hover:bg-gray-50">
                                    <div class="px-4 py-4 sm:px-6">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <p class="text-sm font-medium text-indigo-600 truncate">
                                                    <?= htmlspecialchars($booking['user_name']) ?>
                                                </p>
                                                <div class="ml-2 flex-shrink-0 flex">
                                                    <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        <?= $booking['status'] === 'Pending' ? 'bg-yellow-100 text-yellow-800' :
                                                            ($booking['status'] === 'Confirmed' ? 'bg-green-100 text-green-800' :
                                                                'bg-red-100 text-red-800') ?>">
                                                        <?= $booking['status'] ?>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="ml-2 flex-shrink-0 flex">
                                                <span class="text-sm text-gray-500">
                                                    <?= date('M j, Y', strtotime($booking['booking_date'])) ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="mt-2 sm:flex sm:justify-between">
                                            <div class="sm:flex">
                                                <p class="flex items-center text-sm text-gray-500">
                                                    <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400"
                                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                        fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    <?= htmlspecialchars($booking['station_address']) ?>
                                                </p>
                                            </div>
                                            <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                                <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-green-500"
                                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                <?= date('g:i A', strtotime($booking['booking_date'])) ?> -
                                                <?= date('g:i A', strtotime($booking['due_date'])) ?>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </main>
</div>