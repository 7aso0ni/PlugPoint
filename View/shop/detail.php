<?php /** @var array $cp */ ?>
<div class="bg-gradient-to-b from-green-50 to-blue-50 min-h-screen py-12">
    <div class="container mx-auto px-4 max-w-4xl">
        <!-- Back button for mobile -->
        <div class="mb-4 md:hidden">
            <a href="index.php?route=chargepoints"
                class="inline-flex items-center text-gray-700 hover:text-green-600 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to list
            </a>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div
                class="mb-6 p-4 rounded-lg <?= $_SESSION['message_type'] === 'error' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' ?>">
                <?= $_SESSION['message'] ?>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>

        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <div class="relative">
                <!-- Status badge -->
                <div class="absolute top-4 right-4 z-10">
                    <?php if ($cp['availability']): ?>
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <span class="h-2 w-2 mr-1 rounded-full bg-green-500"></span>
                            Available
                        </span>
                    <?php else: ?>
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            <span class="h-2 w-2 mr-1 rounded-full bg-red-500"></span>
                            Unavailable
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Image with overlay -->
                <div class="relative h-80">
                    <img src="<?= htmlspecialchars($cp['image_url']) ?>"
                        alt="Charging station at <?= htmlspecialchars($cp['address']) ?>"
                        class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent flex items-end">
                        <div class="p-6 text-white">
                            <h1 class="text-3xl font-bold mb-2"><?= htmlspecialchars($cp['address']) ?></h1>
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="text-sm">
                                    <?= number_format($cp['latitude'], 6) ?>, <?= number_format($cp['longitude'], 6) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="flex flex-col md:flex-row md:justify-between gap-6">
                    <!-- Details Section -->
                    <div class="flex-1">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Charging Details</h2>

                        <div class="space-y-4">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Price per kWh</p>
                                    <p class="text-lg font-bold text-gray-800">
                                        $<?= number_format($cp['price_per_kWh'], 2) ?></p>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Created At</p>
                                    <p class="text-gray-800">
                                        <?= date('F j, Y', strtotime($cp['created_at'])) ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Owner Section -->
                    <div class="flex-1">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Owner Information</h2>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center mb-4">
                                <div class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Owner</p>
                                    <p class="font-semibold text-gray-800"><?= htmlspecialchars($cp['owner_name']) ?>
                                    </p>
                                </div>
                            </div>

                            <a href="mailto:<?= htmlspecialchars($cp['owner_email']) ?>"
                                class="block w-full py-2 px-4 bg-green-600 hover:bg-green-700 text-white text-center rounded-lg transition">
                                <span class="flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    Contact Owner
                                </span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Interactive Map Section -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Location</h2>
                    <div id="station-map" class="h-96 w-full rounded-lg border border-gray-200 mb-4"></div>

                    <div class="text-sm text-gray-600 mb-4">
                        <p>This map shows the location of this charging station. Use the controls to zoom or get
                            directions.</p>
                    </div>
                </div>

                <!-- Action buttons section -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <a href="index.php?route=chargepoints"
                            class="hidden md:inline-flex items-center text-gray-700 hover:text-green-600 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                            Back to all charging stations
                        </a>

                        <?php if ($cp['availability']): ?>
                            <a href="index.php?route=booking_form&id=<?= $cp['id'] ?>"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Book Now
                            </a>
                        <?php else: ?>
                            <button disabled
                                class="inline-flex items-center px-4 py-2 bg-gray-400 text-white rounded-lg cursor-not-allowed opacity-75">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Currently Unavailable
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Pass station data to JavaScript -->
<script>
    // Create a data object to use in our map script
    const stationData = {
        id: <?= $cp['id'] ?>,
        latitude: <?= $cp['latitude'] ?>,
        longitude: <?= $cp['longitude'] ?>,
        address: "<?= htmlspecialchars(addslashes($cp['address'])) ?>",
        price_per_kWh: <?= $cp['price_per_kWh'] ?>,
        availability: <?= $cp['availability'] ? 'true' : 'false' ?>
    };
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize map
        const map = L.map('station-map').setView([stationData.latitude, stationData.longitude], 14);

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Create custom marker icon for current station
        const currentStationIcon = L.divIcon({
            className: 'custom-div-icon',
            html: `
                <div style="background-color: ${stationData.availability ? '#10B981' : '#EF4444'}; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.3);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"></path>
                    </svg>
                </div>
            `,
            iconSize: [40, 40],
            iconAnchor: [20, 20],
            popupAnchor: [0, -20]
        });

        // Create custom marker icon for nearby stations
        const nearbyStationIcon = L.divIcon({
            className: 'custom-div-icon',
            html: `
                <div style="background-color: #3B82F6; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 16px; box-shadow: 0 2px 5px rgba(0,0,0,0.3);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"></path>
                    </svg>
                </div>
            `,
            iconSize: [30, 30],
            iconAnchor: [15, 15],
            popupAnchor: [0, -15]
        });

        // Add marker for current station
        const marker = L.marker([stationData.latitude, stationData.longitude], {
            icon: currentStationIcon
        }).addTo(map);

        // Add popup with station info
        marker.bindPopup(`
            <div style="width: 200px;">
                <h3 style="font-weight: bold; margin-bottom: 8px;">${stationData.address}</h3>
                <p style="margin-bottom: 4px;">$${stationData.price_per_kWh.toFixed(2)}/kWh</p>
                <p style="color: ${stationData.availability ? '#10B981' : '#EF4444'}; font-weight: bold;">
                    ${stationData.availability ? 'Available' : 'Unavailable'}
                </p>
                ${stationData.availability ?
                `<a href="index.php?route=booking_form&id=${stationData.id}" style="display: inline-block; margin-top: 8px; padding: 4px 8px; background-color: #2563EB; color: white; border-radius: 4px; text-decoration: none;">Book Now</a>` :
                ''
            }
            </div>
        `).openPopup();

        // Fetch and display nearby stations
        fetchNearbyStations();

        // Add directions button below the map
        const directionsLink = document.createElement('a');
        directionsLink.href = `https://www.google.com/maps/dir/?api=1&destination=${stationData.latitude},${stationData.longitude}`;
        directionsLink.target = '_blank';
        directionsLink.className = 'bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 inline-flex items-center mt-4';
        directionsLink.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
            </svg>
            Get Directions
        `;
        document.getElementById('station-map').after(directionsLink);

        // Function to fetch nearby stations
        async function fetchNearbyStations() {
            try {
                const response = await fetch(`index.php?route=api/nearby_stations&lat=${stationData.latitude}&lng=${stationData.longitude}&radius=10&exclude=${stationData.id}`);

                if (!response.ok) {
                    throw new Error('Failed to fetch nearby stations');
                }

                const data = await response.json();
                const nearbyStations = data.stations || [];

                // Add markers for nearby stations
                nearbyStations.forEach(station => {
                    const marker = L.marker([station.latitude, station.longitude], {
                        icon: nearbyStationIcon
                    }).addTo(map);

                    // Add popup with station info
                    marker.bindPopup(`
                        <div style="width: 200px;">
                            <h3 style="font-weight: bold; margin-bottom: 8px;">${station.address}</h3>
                            <p style="margin-bottom: 4px;">$${parseFloat(station.price_per_kWh).toFixed(2)}/kWh</p>
                            <p style="color: ${station.availability ? '#10B981' : '#EF4444'}; font-weight: bold;">
                                ${station.availability ? 'Available' : 'Unavailable'}
                            </p>
                            <p style="margin-bottom: 4px; font-size: 12px;">
                                Distance: ${station.distance ? (station.distance).toFixed(1) : '?'} km
                            </p>
                            <a href="index.php?route=chargepoints/details&id=${station.id}" style="display: inline-block; margin-top: 8px; padding: 4px 8px; background-color: #4B5563; color: white; border-radius: 4px; text-decoration: none;">View Details</a>
                        </div>
                    `);
                });

                // Adjust map bounds to show all markers if there are nearby stations
                if (nearbyStations.length > 0) {
                    const markers = [
                        [stationData.latitude, stationData.longitude],
                        ...nearbyStations.map(station => [station.latitude, station.longitude])
                    ];
                    const bounds = L.latLngBounds(markers);
                    map.fitBounds(bounds, { padding: [50, 50] });
                }

                // Add a legend if there are nearby stations
                if (nearbyStations.length > 0) {
                    const legend = L.control({ position: 'bottomright' });
                    legend.onAdd = function () {
                        const div = L.DomUtil.create('div', 'info legend');
                        div.style.backgroundColor = 'white';
                        div.style.padding = '6px 8px';
                        div.style.border = '1px solid #ccc';
                        div.style.borderRadius = '4px';
                        div.style.lineHeight = '18px';
                        div.style.color = '#555';

                        div.innerHTML = `
                            <div style="margin-bottom: 4px;"><strong>Legend</strong></div>
                            <div style="display: flex; align-items: center; margin-bottom: 4px;">
                                <div style="background-color: ${stationData.availability ? '#10B981' : '#EF4444'}; width: 18px; height: 18px; border-radius: 50%; margin-right: 8px;"></div>
                                <span>Current Station</span>
                            </div>
                            <div style="display: flex; align-items: center;">
                                <div style="background-color: #3B82F6; width: 18px; height: 18px; border-radius: 50%; margin-right: 8px;"></div>
                                <span>Nearby Stations</span>
                            </div>
                        `;

                        return div;
                    };
                    legend.addTo(map);
                }
            } catch (error) {
                console.error('Error fetching nearby stations:', error);
            }
        }

        // Resize map when container becomes visible
        setTimeout(() => {
            map.invalidateSize();
        }, 100);
    });
</script>