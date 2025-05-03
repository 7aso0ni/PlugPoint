<?php
/* $initialData is defined in the controller */
?>
<div class="bg-gray-50 py-10">
    <!-- hero omitted for brevity (keep yours) -->

    <!-- Charge Points Grid -->
    <div id="browse" class="container mx-auto px-4">
        <h2 class="text-2xl font-bold mb-6">Available Charging Stations</h2>

        <!-- Search and Filters -->
        <div class="bg-white rounded-lg shadow-md p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" id="search"
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-green-500 focus:border-green-500"
                            placeholder="Search by address or city...">
                    </div>
                </div>

                <!-- Price Filter -->
                <div>
                    <label for="price-range" class="block text-sm font-medium text-gray-700 mb-1">
                        Max Price: $<span id="price-value">0.50</span>/kWh
                    </label>
                    <input type="range" id="price-range" min="0.10" max="1.00" step="0.05" value="0.50"
                        class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                </div>

                <!-- Availability Filter -->
                <div>
                    <label for="availability" class="block text-sm font-medium text-gray-700 mb-1">Availability</label>
                    <select id="availability"
                        class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                        <option value="">All Stations</option>
                        <option value="1">Available Only</option>
                        <option value="0">Show All</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 text-right">
                <button id="reset-filters" class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900">
                    Reset Filters
                </button>
                <button id="apply-filters" class="ml-3 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Apply Filters
                </button>
            </div>
        </div>

        <div id="loading-indicator" class="flex justify-center items-center my-8 hidden">
            <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-green-500"></div>
        </div>

        <div id="no-results" class="text-center py-10 hidden">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="mt-2 text-lg font-medium text-gray-900">No charging stations found</h3>
            <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter criteria.</p>
            <div class="mt-6">
                <button id="clear-filters" type="button"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Clear all filters
                </button>
            </div>
        </div>

        <div id="chargepoints-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <!-- server-rendered cards -->
            <?php foreach ($initialData['chargePoints'] as $cp): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="relative">
                        <img src="<?= htmlspecialchars($cp['image_url']) ?>"
                            alt="Charging station at <?= htmlspecialchars($cp['address']) ?>"
                            class="w-full h-48 object-cover">
                        <span class="absolute top-0 right-0 text-white text-xs font-bold px-2 py-1 m-2 rounded
                            <?= $cp['availability'] ? 'bg-green-600' : 'bg-red-500' ?>">
                            <?= $cp['availability'] ? 'Available' : 'Unavailable' ?>
                        </span>
                    </div>
                    <div class="p-4">
                        <h3 class="text-lg font-semibold mb-1">
                            <?= htmlspecialchars($cp['address']) ?>
                        </h3>
                        <p class="text-gray-600 text-sm mb-2">
                            Hosted by <?= htmlspecialchars($cp['owner_name']) ?>
                        </p>
                        <div class="flex items-center mb-3">
                            <i class="fas fa-bolt text-yellow-400 mr-1"></i>
                            <span class="text-sm">
                                $<?= number_format($cp['price_per_kWh'], 2) ?>/kWh
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                <i class="fas fa-map-marker-alt text-red-500 mr-1"></i>
                                <span class="text-gray-500 text-sm">View on map</span>
                            </div>
                            <a href="index.php?route=chargepoints/details&id=<?= $cp['id'] ?>"
                                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- pagination -->
        <div id="pagination" class="flex justify-center mt-10">
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                <button id="prev-page"
                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                    <span class="sr-only">Previous</span>
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div id="page-numbers" class="bg-white border-gray-300">
                    <!-- Page numbers will be inserted here by JavaScript -->
                </div>
                <button id="next-page"
                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                    <span class="sr-only">Next</span>
                    <i class="fas fa-chevron-right"></i>
                </button>
            </nav>
        </div>
    </div>
</div>

<!-- Template for charge point card -->
<template id="chargepoint-template">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="relative">
            <img src="" alt="" class="w-full h-48 object-cover">
            <span class="absolute top-0 right-0 text-white text-xs font-bold px-2 py-1 m-2 rounded">
                <!-- Availability status text will be set by JavaScript -->
            </span>
        </div>
        <div class="p-4">
            <h3 class="text-lg font-semibold mb-1">
                <!-- Address will be set by JavaScript -->
            </h3>
            <p class="text-gray-600 text-sm mb-2">
                <!-- Owner name will be set by JavaScript -->
            </p>
            <div class="flex items-center mb-3">
                <i class="fas fa-bolt text-yellow-400 mr-1"></i>
                <span class="text-sm">
                    <!-- Price will be set by JavaScript -->
                </span>
            </div>
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <i class="fas fa-map-marker-alt text-red-500 mr-1"></i>
                    <span class="text-gray-500 text-sm">View on map</span>
                </div>
                <a href="#" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                    View Details
                </a>
            </div>
        </div>
    </div>
</template>

<!-- pass PHP data to JS -->
<script>
    window.initialChargeData = <?= json_encode($initialData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP) ?>;
</script>
<script src="/View/shop/chargepoint-filter.js"></script>