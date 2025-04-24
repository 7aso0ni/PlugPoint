<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Rentals Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 py-8">
<div class="container mx-auto px-4">
    <!-- Simple Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold">My Rentals Dashboard</h1>
    </div>

    <!-- Rentals Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    <th class="px-6 py-3">Charger</th>
                    <th class="px-6 py-3">Rental Period</th>
                    <th class="px-6 py-3">Price</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                <!-- Rental Item 1 -->
                <tr>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="h-10 w-10 flex-shrink-0 bg-gray-300 rounded">
                                <img class="h-10 w-10 rounded" src="/api/placeholder/40/40" alt="Charger">
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">Tesla Wall Connector</div>
                                <div class="text-sm text-gray-500">Tesla Connector • 11.5kW</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">Apr 20 - Apr 27, 2025</div>
                        <div class="text-sm text-gray-500">Weekly</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">$126.00</div>
                        <div class="text-sm text-gray-500">($18.00/day)</div>
                    </td>
                    <td class="px-6 py-4">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                  Active
                </span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <a href="#" class="text-indigo-600 hover:text-indigo-900 mr-3">View Details</a>
                        <a href="#" class="text-red-600 hover:text-red-900">Return</a>
                    </td>
                </tr>

                <!-- Rental Item 2 -->
                <tr>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="h-10 w-10 flex-shrink-0 bg-gray-300 rounded">
                                <img class="h-10 w-10 rounded" src="/api/placeholder/40/40" alt="Charger">
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">Level 2 Home Charger</div>
                                <div class="text-sm text-gray-500">J1772 Connector • 7.2kW</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">Apr 15 - May 15, 2025</div>
                        <div class="text-sm text-gray-500">Monthly</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">$240.00</div>
                        <div class="text-sm text-gray-500">($8.00/day)</div>
                    </td>
                    <td class="px-6 py-4">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                  Active
                </span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <a href="#" class="text-indigo-600 hover:text-indigo-900 mr-3">View Details</a>
                        <a href="#" class="text-red-600 hover:text-red-900">Return</a>
                    </td>
                </tr>

                <!-- Rental Item 3 -->
                <tr>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="h-10 w-10 flex-shrink-0 bg-gray-300 rounded">
                                <img class="h-10 w-10 rounded" src="/api/placeholder/40/40" alt="Charger">
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">Portable DC Fast Charger</div>
                                <div class="text-sm text-gray-500">CCS & CHAdeMO • 50kW</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">Apr 26 - Apr 28, 2025</div>
                        <div class="text-sm text-gray-500">Weekend</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">$84.00</div>
                        <div class="text-sm text-gray-500">($42.00/day)</div>
                    </td>
                    <td class="px-6 py-4">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                  Pending
                </span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <a href="#" class="text-indigo-600 hover:text-indigo-900 mr-3">View Details</a>
                        <a href="#" class="text-red-600 hover:text-red-900">Cancel</a>
                    </td>
                </tr>

                <!-- Rental Item 4 -->
                <tr>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="h-10 w-10 flex-shrink-0 bg-gray-300 rounded">
                                <img class="h-10 w-10 rounded" src="/api/placeholder/40/40" alt="Charger">
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">Smart Home Charger</div>
                                <div class="text-sm text-gray-500">J1772 Connector • 9.6kW</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">Mar 10 - Mar 24, 2025</div>
                        <div class="text-sm text-gray-500">Bi-weekly</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">$210.00</div>
                        <div class="text-sm text-gray-500">($15.00/day)</div>
                    </td>
                    <td class="px-6 py-4">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                  Completed
                </span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <a href="#" class="text-indigo-600 hover:text-indigo-900 mr-3">View Details</a>
                        <a href="#" class="text-green-600 hover:text-green-900">Rent Again</a>
                    </td>
                </tr>

                <!-- Rental Item 5 -->
                <tr>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="h-10 w-10 flex-shrink-0 bg-gray-300 rounded">
                                <img class="h-10 w-10 rounded" src="/api/placeholder/40/40" alt="Charger">
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">Commercial Dual Charger</div>
                                <div class="text-sm text-gray-500">Dual J1772 • 2x 7.2kW</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">Mar 1 - Mar 7, 2025</div>
                        <div class="text-sm text-gray-500">Weekly</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">$175.00</div>
                        <div class="text-sm text-gray-500">($25.00/day)</div>
                    </td>
                    <td class="px-6 py-4">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                  Declined
                </span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <a href="#" class="text-indigo-600 hover:text-indigo-900 mr-3">View Details</a>
                        <a href="#" class="text-green-600 hover:text-green-900">Try Again</a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>