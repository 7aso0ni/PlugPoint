<div class="bg-gray-100 min-h-screen">
    <!-- Admin Header -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-900"><?= $title ?></h1>
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

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Monthly Stats Chart -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Monthly Statistics</h3>
            </div>
            <div class="p-6">
                <div class="w-full h-80" id="monthlyStatsChart"></div>
            </div>
        </div>

        <!-- Two Columns Layout -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Top Charge Points -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-5 border-b border-gray-200">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Top Charging Stations</h3>
                </div>
                <div class="overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Location
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Bookings
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Revenue
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($topChargePoints)): ?>
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No data available
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($topChargePoints as $cp): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?= htmlspecialchars($cp['address']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?= $cp['total_bookings'] ?? 0 ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            $<?= number_format(($cp['revenue'] ?? 0), 2) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- User Growth -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-5 border-b border-gray-200">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">User Growth</h3>
                </div>
                <div class="p-6">
                    <div class="w-full h-64" id="userGrowthChart"></div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Process Monthly Stats for the Chart
        const monthlyStats = <?= json_encode($monthlyStats) ?>;

        // Extract month labels and data series
        const months = [];
        const bookings = [];
        const revenue = [];

        // Check if monthlyStats is an object or array
        if (Array.isArray(monthlyStats)) {
            // If array format (new format)
            monthlyStats.forEach(stat => {
                months.push(stat.month);
                bookings.push(stat.bookings);
                revenue.push(parseFloat(stat.revenue) || 0);
            });
        } else {
            // If object format (old format with month keys)
            Object.keys(monthlyStats).forEach(month => {
                months.push(month);
                bookings.push(monthlyStats[month].bookings);
                revenue.push(parseFloat(monthlyStats[month].revenue) || 0);
            });
        }

        // Create Monthly Stats Chart
        const statsCtx = document.getElementById('monthlyStatsChart').getContext('2d');
        const statsChart = new Chart(statsCtx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Bookings',
                        data: bookings,
                        backgroundColor: 'rgba(79, 70, 229, 0.2)',
                        borderColor: 'rgba(79, 70, 229, 1)',
                        borderWidth: 1,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Revenue ($)',
                        data: revenue,
                        backgroundColor: 'rgba(16, 185, 129, 0.2)',
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 1,
                        type: 'line',
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Bookings'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false
                        },
                        title: {
                            display: true,
                            text: 'Revenue ($)'
                        }
                    }
                }
            }
        });

        // Process User Growth for the Chart
        const userGrowth = <?= json_encode($userGrowth) ?>;

        // Extract user growth data
        const growthMonths = [];
        const newUsers = [];

        // Check if userGrowth is an object or array
        if (Array.isArray(userGrowth)) {
            // If array format (new format)
            userGrowth.forEach(stat => {
                growthMonths.push(stat.month);
                newUsers.push(stat.new_users);
            });
        } else {
            // If object format (old format with month keys)
            Object.keys(userGrowth).forEach(month => {
                growthMonths.push(month);
                newUsers.push(userGrowth[month].new_users || 0);
            });
        }

        // Create User Growth Chart
        const growthCtx = document.getElementById('userGrowthChart').getContext('2d');
        const growthChart = new Chart(growthCtx, {
            type: 'line',
            data: {
                labels: growthMonths,
                datasets: [{
                    label: 'New Users',
                    data: newUsers,
                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'New Users'
                        }
                    }
                }
            }
        });
    });
</script>