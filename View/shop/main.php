<!-- views/shop.php -->
<div class="bg-gray-50 py-10">
    <!-- Hero Section -->
    <div class="container mx-auto px-4 mb-12">
        <div class="bg-green-600 rounded-xl p-8 text-white shadow-lg">
            <h1 class="text-3xl font-bold mb-4">Rent EV Chargers Anywhere</h1>
            <p class="text-lg mb-6">Access affordable charging options. Rent the right charger for your electric vehicle
                needs.</p>
            <div class="flex gap-4">
                <a href="#browse"
                   class="bg-white text-green-600 font-medium px-6 py-3 rounded-lg shadow-md hover:bg-gray-100 transition">Browse
                    Chargers</a>
                <a href="index.php?route=how-it-works"
                   class="border border-white px-6 py-3 rounded-lg hover:bg-green-700 transition">Learn More</a>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="container mx-auto px-4 mb-8">
        <div class="bg-white p-4 rounded-lg shadow-md">
            <form class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Charger Type</label>
                    <select class="w-full border rounded-md px-3 py-2">
                        <option>All Types</option>
                        <option>Level 1 (120V)</option>
                        <option>Level 2 (240V)</option>
                        <option>DC Fast Charger</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Connector</label>
                    <select class="w-full border rounded-md px-3 py-2">
                        <option>All Connectors</option>
                        <option>J1772</option>
                        <option>CCS</option>
                        <option>CHAdeMO</option>
                        <option>Tesla</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rental Period</label>
                    <select class="w-full border rounded-md px-3 py-2">
                        <option>Any Duration</option>
                        <option>Daily</option>
                        <option>Weekly</option>
                        <option>Monthly</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit"
                            class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700">Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Grid -->
    <div id="browse" class="container mx-auto px-4">
        <h2 class="text-2xl font-bold mb-6">Available EV Chargers</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <!-- Product Card 1 -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="relative">
                    <img src="https://via.placeholder.com/300x200" alt="Level 2 Home Charger"
                         class="w-full h-48 object-cover">
                    <span class="absolute top-0 right-0 bg-green-600 text-white text-xs font-bold px-2 py-1 m-2 rounded">Available</span>
                </div>
                <div class="p-4">
                    <h3 class="text-lg font-semibold mb-1">Level 2 Home Charger</h3>
                    <p class="text-gray-600 text-sm mb-2">J1772 Connector • 7.2kW</p>
                    <div class="flex items-center mb-3">
                        <div class="flex text-yellow-400">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <span class="text-xs text-gray-500 ml-1">(42 reviews)</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="text-gray-500 text-sm">From</span>
                            <p class="font-bold text-green-600">$12/day</p>
                        </div>
                        <a href="index.php?route=charger&id=1"
                           class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">Rent Now</a>
                    </div>
                </div>
            </div>

            <!-- Product Card 2 -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="relative">
                    <img src="https://via.placeholder.com/300x200" alt="Portable Level 1 Charger"
                         class="w-full h-48 object-cover">
                    <span class="absolute top-0 right-0 bg-green-600 text-white text-xs font-bold px-2 py-1 m-2 rounded">Available</span>
                </div>
                <div class="p-4">
                    <h3 class="text-lg font-semibold mb-1">Portable Level 1 Charger</h3>
                    <p class="text-gray-600 text-sm mb-2">Standard Outlet • 1.4kW</p>
                    <div class="flex items-center mb-3">
                        <div class="flex text-yellow-400">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                        </div>
                        <span class="text-xs text-gray-500 ml-1">(28 reviews)</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="text-gray-500 text-sm">From</span>
                            <p class="font-bold text-green-600">$8/day</p>
                        </div>
                        <a href="index.php?route=charger&id=2"
                           class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">Rent Now</a>
                    </div>
                </div>
            </div>

            <!-- Product Card 3 -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="relative">
                    <img src="https://via.placeholder.com/300x200" alt="Tesla Wall Connector"
                         class="w-full h-48 object-cover">
                    <span class="absolute top-0 right-0 bg-yellow-500 text-white text-xs font-bold px-2 py-1 m-2 rounded">Limited</span>
                </div>
                <div class="p-4">
                    <h3 class="text-lg font-semibold mb-1">Tesla Wall Connector</h3>
                    <p class="text-gray-600 text-sm mb-2">Tesla Connector • 11.5kW</p>
                    <div class="flex items-center mb-3">
                        <div class="flex text-yellow-400">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <span class="text-xs text-gray-500 ml-1">(64 reviews)</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="text-gray-500 text-sm">From</span>
                            <p class="font-bold text-green-600">$18/day</p>
                        </div>
                        <a href="index.php?route=charger&id=3"
                           class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">Rent Now</a>
                    </div>
                </div>
            </div>

            <!-- Product Card 4 -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="relative">
                    <img src="https://via.placeholder.com/300x200" alt="DC Fast Charger"
                         class="w-full h-48 object-cover">
                    <span class="absolute top-0 right-0 bg-red-500 text-white text-xs font-bold px-2 py-1 m-2 rounded">Premium</span>
                </div>
                <div class="p-4">
                    <h3 class="text-lg font-semibold mb-1">Portable DC Fast Charger</h3>
                    <p class="text-gray-600 text-sm mb-2">CCS & CHAdeMO • 50kW</p>
                    <div class="flex items-center mb-3">
                        <div class="flex text-yellow-400">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <span class="text-xs text-gray-500 ml-1">(15 reviews)</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="text-gray-500 text-sm">From</span>
                            <p class="font-bold text-green-600">$42/day</p>
                        </div>
                        <a href="index.php?route=charger&id=4"
                           class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">Rent Now</a>
                    </div>
                </div>
            </div>

            <!-- Product Card 5 -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="relative">
                    <img src="https://via.placeholder.com/300x200" alt="Smart Home Charger"
                         class="w-full h-48 object-cover">
                    <span class="absolute top-0 right-0 bg-green-600 text-white text-xs font-bold px-2 py-1 m-2 rounded">Available</span>
                </div>
                <div class="p-4">
                    <h3 class="text-lg font-semibold mb-1">Smart Home Charger</h3>
                    <p class="text-gray-600 text-sm mb-2">J1772 Connector • 9.6kW</p>
                    <div class="flex items-center mb-3">
                        <div class="flex text-yellow-400">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                        </div>
                        <span class="text-xs text-gray-500 ml-1">(31 reviews)</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="text-gray-500 text-sm">From</span>
                            <p class="font-bold text-green-600">$15/day</p>
                        </div>
                        <a href="index.php?route=charger&id=5"
                           class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">Rent Now</a>
                    </div>
                </div>
            </div>

            <!-- Product Card 6 -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="relative">
                    <img src="https://via.placeholder.com/300x200" alt="Commercial Charger"
                         class="w-full h-48 object-cover">
                    <span class="absolute top-0 right-0 bg-yellow-500 text-white text-xs font-bold px-2 py-1 m-2 rounded">Limited</span>
                </div>
                <div class="p-4">
                    <h3 class="text-lg font-semibold mb-1">Commercial Dual Charger</h3>
                    <p class="text-gray-600 text-sm mb-2">Dual J1772 • 2x 7.2kW</p>
                    <div class="flex items-center mb-3">
                        <div class="flex text-yellow-400">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                            <i class="far fa-star"></i>
                        </div>
                        <span class="text-xs text-gray-500 ml-1">(12 reviews)</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="text-gray-500 text-sm">From</span>
                            <p class="font-bold text-green-600">$25/day</p>
                        </div>
                        <a href="index.php?route=charger&id=6"
                           class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">Rent Now</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="flex justify-center mt-10">
            <nav class="inline-flex rounded-md shadow">
                <a href="#" class="py-2 px-4 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50">Previous</a>
                <a href="#" class="py-2 px-4 bg-white border-t border-b border-gray-300 hover:bg-gray-50">1</a>
                <a href="#" class="py-2 px-4 bg-green-600 text-white border border-green-600">2</a>
                <a href="#" class="py-2 px-4 bg-white border-t border-b border-gray-300 hover:bg-gray-50">3</a>
                <a href="#" class="py-2 px-4 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50">Next</a>
            </nav>
        </div>
    </div>
</div>