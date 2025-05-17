<div class="max-w-4xl mx-auto py-10 px-6 bg-white shadow-md rounded-lg mt-6">
    <h1 class="text-2xl font-bold mb-6 text-center text-green-600">Add New Charger</h1>

    <?php if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
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
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <strong class="font-bold">Success!</strong>
            <span class="block sm:inline"><?= htmlspecialchars($_SESSION['success']) ?></span>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- Map Selection -->
    <div id="map-container">
        <div class="mb-4">
            <p class="text-gray-600 mb-2">Click on the map to select your charger location. You can drag the marker to adjust the position.</p>
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

        <form method="POST" enctype="multipart/form-data" action="index.php?route=homeowner/save_charger" class="space-y-6">
            <input type="hidden" name="latitude" id="map-latitude" value="0">
            <input type="hidden" name="longitude" id="map-longitude" value="0">

            <div>
                <label class="block text-gray-700 mb-1">Selected Location</label>
                <input name="location" id="map-location" type="text" class="w-full border border-gray-300 px-4 py-2 rounded-lg bg-gray-50" readonly>
            </div>

            <div>
                <label class="block text-gray-700 mb-1">Price (BD/hour)</label>
                <input name="price" type="number" step="0.01" class="w-full border border-gray-300 px-4 py-2 rounded-lg" placeholder="e.g., 0.50" required>
            </div>

            <div>
                <label class="block text-gray-700 mb-1">Available From</label>
                <input name="available_from" type="datetime-local" class="w-full border border-gray-300 px-4 py-2 rounded-lg" required>
            </div>

            <div>
                <label class="block text-gray-700 mb-1">Available To</label>
                <input name="available_to" type="datetime-local" class="w-full border border-gray-300 px-4 py-2 rounded-lg" required>
            </div>

            <div>
                <label class="block text-gray-700 mb-1">Optional Image</label>
                <input name="image" type="file" class="w-full border border-gray-300 px-4 py-2 rounded-lg">
            </div>

            <div class="text-center">
                <button type="submit" id="map-submit" class="bg-green-600 text-white py-2 px-6 rounded-lg hover:bg-green-700" disabled>Add Charger</button>
            </div>
        </form>
    </div>
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
                const position = marker.getLatLng();
                updateLocationFromCoordinates(position.lat, position.lng);
            });

            // Enable submit button
            document.getElementById('map-submit').disabled = false;

            // Update hidden form fields
            document.getElementById('map-latitude').value = latlng.lat;
            document.getElementById('map-longitude').value = latlng.lng;
        }

        async function updateLocationFromCoordinates(lat, lng) {
            try {
                // Use OpenStreetMap Nominatim API for reverse geocoding
                const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
                const data = await response.json();

                // Format the address
                const address = data.display_name;

                // Update the location input fields
                document.getElementById('map-location').value = address;

                // Update hidden form fields
                document.getElementById('map-latitude').value = lat;
                document.getElementById('map-longitude').value = lng;
            } catch (error) {
                console.error('Error getting address:', error);
                document.getElementById('map-location').value = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
            }
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

                    // Update location input
                    document.getElementById('map-location').value = result.display_name;
                } else {
                    alert('Location not found. Please try a different search term.');
                }
            } catch (error) {
                console.error('Error searching location:', error);
                alert('Error searching for location. Please try again.');
            }
        }
    });
</script>