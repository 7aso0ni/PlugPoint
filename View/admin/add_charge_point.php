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
        <div class="px-4 py-6 sm:px-0">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Add New Charging Station</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">Use the map to select the location for the new charging station.</p>
                    </div>
                    <a href="index.php?route=admin/charge_points" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                        Back to Charge Points
                    </a>
                </div>

                <?php if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mx-4 my-4">
                        <strong class="font-bold">Error!</strong>
                        <ul class="list-disc ml-5">
                            <?php foreach ($_SESSION['errors'] as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php unset($_SESSION['errors']); ?>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mx-4 my-4">
                        <strong class="font-bold">Success!</strong>
                        <span class="block sm:inline"><?= htmlspecialchars($_SESSION['success']) ?></span>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <div class="px-4 py-5 sm:p-6">
                    <!-- Map Selection -->
                    <div id="map-container">
                        <div class="mb-4">
                            <p class="text-gray-600 mb-2">Click on the map to select the charger location. You can drag the marker to adjust the position.</p>
                            <div class="flex space-x-4 mb-4">
                                <div class="flex-grow">
                                    <input type="text" id="map-search" class="w-full border border-gray-300 px-4 py-2 rounded-lg" placeholder="Search for a location...">
                                </div>
                                <button id="search-button" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                                    <i class="fas fa-search mr-1"></i> Search
                                </button>
                            </div>
                        </div>
                        
                        <div id="selection-map" class="h-96 w-full rounded-lg border border-gray-200 mb-6"></div>
                        
                        <form method="POST" enctype="multipart/form-data" action="index.php?route=admin/add_charge_point" class="space-y-6">
                            <input type="hidden" name="latitude" id="map-latitude" value="0">
                            <input type="hidden" name="longitude" id="map-longitude" value="0">
                            
                            <div>
                                <label class="block text-gray-700 mb-1">Selected Location</label>
                                <input name="address" id="map-location" type="text" class="w-full border border-gray-300 px-4 py-2 rounded-lg bg-gray-50" readonly>
                            </div>

                            <div>
                                <label class="block text-gray-700 mb-1">Price per kWh ($)</label>
                                <input name="price_per_kWh" type="number" step="0.01" class="w-full border border-gray-300 px-4 py-2 rounded-lg" placeholder="e.g., 0.50" required>
                            </div>

                            <div>
                                <label class="block text-gray-700 mb-1">Availability</label>
                                <select name="availability" class="w-full border border-gray-300 px-4 py-2 rounded-lg">
                                    <option value="1">Available</option>
                                    <option value="0">Unavailable</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-gray-700 mb-1">Owner</label>
                                <select name="owner_id" class="w-full border border-gray-300 px-4 py-2 rounded-lg" required>
                                    <option value="">Select Owner</option>
                                    <?php foreach ($owners as $owner): ?>
                                        <option value="<?= $owner['id'] ?>"><?= htmlspecialchars($owner['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-gray-700 mb-1">Optional Image</label>
                                <input name="image" type="file" class="w-full border border-gray-300 px-4 py-2 rounded-lg">
                                <p class="mt-1 text-sm text-gray-500">JPG, PNG, GIF up to 5MB</p>
                            </div>

                            <div class="text-center">
                                <button type="submit" id="map-submit" class="bg-green-600 text-white py-2 px-6 rounded-lg hover:bg-green-700" disabled>Add Charger</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Map initialization
        let map = null;
        let marker = null;
        
        function initMap() {
            // Initialize map with default center (Dubai)
            map = L.map('selection-map').setView([25.2048, 55.2708], 12);
            
            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            
            // Handle map clicks to place/move marker
            map.on('click', function(e) {
                placeMarker(e.latlng);
                updateLocationFromCoordinates(e.latlng.lat, e.latlng.lng);
            });
            
            // Try to get user's current location
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const userLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    map.setView([userLocation.lat, userLocation.lng], 14);
                });
            }
            
            // Set up search functionality
            const searchButton = document.getElementById('search-button');
            const searchInput = document.getElementById('map-search');
            
            searchButton.addEventListener('click', function() {
                searchLocation(searchInput.value);
            });
            
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    searchLocation(searchInput.value);
                }
            });
        }
        
        // Initialize map when page loads
        initMap();
        
        function placeMarker(latlng) {
            // Remove existing marker if any
            if (marker) {
                map.removeLayer(marker);
            }
            
            // Create a new marker
            marker = L.marker(latlng, {
                draggable: true
            }).addTo(map);
            
            // Update coordinates when marker is dragged
            marker.on('dragend', function() {
                const newPos = marker.getLatLng();
                updateLocationFromCoordinates(newPos.lat, newPos.lng);
            });
            
            // Update form fields
            document.getElementById('map-latitude').value = latlng.lat;
            document.getElementById('map-longitude').value = latlng.lng;
            
            // Enable submit button
            document.getElementById('map-submit').disabled = false;
        }
        
        async function searchLocation(query) {
            if (!query) return;
            
            try {
                // Use OpenStreetMap Nominatim API for geocoding
                const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`);
                const data = await response.json();
                
                if (data && data.length > 0) {
                    const result = data[0];
                    const latlng = {
                        lat: parseFloat(result.lat),
                        lng: parseFloat(result.lon)
                    };
                    
                    // Center map on result
                    map.setView([latlng.lat, latlng.lng], 14);
                    
                    // Place marker
                    placeMarker(latlng);
                    
                    // Update location field
                    document.getElementById('map-location').value = result.display_name;
                } else {
                    alert('Location not found. Please try a different search term.');
                }
            } catch (error) {
                console.error('Error searching for location:', error);
                alert('Error searching for location. Please try again.');
            }
        }
        
        async function updateLocationFromCoordinates(lat, lng) {
            try {
                // Use OpenStreetMap Nominatim API for reverse geocoding
                const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
                const data = await response.json();
                
                if (data && data.display_name) {
                    document.getElementById('map-location').value = data.display_name;
                } else {
                    document.getElementById('map-location').value = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                }
                
                // Update hidden fields
                document.getElementById('map-latitude').value = lat;
                document.getElementById('map-longitude').value = lng;
                
                // Enable submit button
                document.getElementById('map-submit').disabled = false;
            } catch (error) {
                console.error('Error getting location name:', error);
                document.getElementById('map-location').value = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                
                // Update hidden fields
                document.getElementById('map-latitude').value = lat;
                document.getElementById('map-longitude').value = lng;
                
                // Enable submit button
                document.getElementById('map-submit').disabled = false;
            }
        }
    });
</script>
