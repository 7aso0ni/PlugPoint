<?php /** @var array $booking */ ?>
<div class="bg-gradient-to-b from-blue-50 to-indigo-50 min-h-screen py-12">
    <div class="container mx-auto px-4 max-w-4xl">
        <!-- Back button for mobile -->
        <div class="mb-4 md:hidden">
            <a href="index.php?route=admin/bookings"
                class="inline-flex items-center text-gray-700 hover:text-indigo-600 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to bookings
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
                <!-- Status badge removed from here since we moved it to the header -->
                <div class="absolute top-4 right-4 z-10">
                    <!-- Status badge is now displayed in the header to avoid overlap with date -->
                </div>

                <!-- Header with booking ID and date - status is moved to inside the header -->
                <div class="bg-indigo-600 p-6 text-white">
                    <div class="flex flex-col space-y-2">
                        <div class="flex justify-between items-center">
                            <h1 class="text-2xl font-bold">Booking #<?= $booking['id'] ?></h1>
                            <!-- Status badge moved here -->
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                <?= $booking['status'] === 'Pending' ? 'bg-yellow-100 text-yellow-800' :
                                    ($booking['status'] === 'Approved' ? 'bg-green-100 text-green-800' :
                                        ($booking['status'] === 'Completed' ? 'bg-blue-100 text-blue-800' :
                                            ($booking['status'] === 'Canceled' ? 'bg-red-100 text-red-800' :
                                                ($booking['status'] === 'Declined' ? 'bg-gray-100 text-gray-800' :
                                                    'bg-purple-100 text-purple-800')))) ?>">
                                <span class="h-2 w-2 mr-1 rounded-full 
                                <?= $booking['status'] === 'Pending' ? 'bg-yellow-500' :
                                    ($booking['status'] === 'Approved' ? 'bg-green-500' :
                                        ($booking['status'] === 'Completed' ? 'bg-blue-500' :
                                            ($booking['status'] === 'Canceled' ? 'bg-red-500' :
                                                ($booking['status'] === 'Declined' ? 'bg-gray-500' :
                                                    'bg-purple-500')))) ?>"></span>
                                <?= $booking['status'] ?>
                            </span>
                        </div>
                        <div class="text-indigo-100">
                            <span>Created: <?= date('M d, Y H:i', strtotime($booking['created_at'])) ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="flex flex-col md:flex-row md:justify-between gap-6">
                    <!-- Booking Details Section -->
                    <div class="flex-1">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Booking Information</h2>

                        <div class="space-y-4">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Booking Period</p>
                                    <p class="text-gray-800">
                                        <?= date('M d, Y H:i', strtotime($booking['booking_date'])) ?> -
                                        <?= date('M d, Y H:i', strtotime($booking['due_date'])) ?>
                                    </p>
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
                                    <p class="text-sm text-gray-500">Duration</p>
                                    <p class="text-gray-800">
                                        <?php
                                        $start = new DateTime($booking['booking_date']);
                                        $end = new DateTime($booking['due_date']);
                                        $interval = $start->diff($end);
                                        echo $interval->format('%h hours %i minutes');
                                        ?>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Price per kWh</p>
                                    <p class="text-lg font-bold text-gray-800">
                                        $<?= number_format($booking['price_per_kWh'], 2) ?>
                                    </p>
                                </div>
                            </div>

                            <?php
                            // Calculate estimated cost
                            $hours = round((strtotime($booking['due_date']) - strtotime($booking['booking_date'])) / 3600, 2);
                            $estimatedCost = $hours * $booking['price_per_kWh'];
                            ?>
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Estimated Total</p>
                                    <p class="text-lg font-bold text-gray-800">
                                        $<?= number_format($estimatedCost, 2) ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Information Section -->
                    <div class="flex-1">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Customer Information</h2>

                        <div class="bg-gray-50 p-4 rounded-lg mb-4">
                            <div class="flex items-center mb-4">
                                <div class="h-12 w-12 rounded-full bg-indigo-200 flex items-center justify-center mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Customer</p>
                                    <p class="font-semibold text-gray-800">
                                        <?= htmlspecialchars($booking['user_name']) ?>
                                    </p>
                                    <p class="text-sm text-gray-600"><?= htmlspecialchars($booking['user_email']) ?></p>
                                    <p class="text-sm text-gray-600">
                                        <?= htmlspecialchars($booking['user_phone'] ?? 'No phone provided') ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Station Owner</h2>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center mb-4">
                                <div class="h-12 w-12 rounded-full bg-green-200 flex items-center justify-center mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Owner</p>
                                    <p class="font-semibold text-gray-800">
                                        <?= htmlspecialchars($booking['owner_name']) ?>
                                    </p>
                                    <p class="text-sm text-gray-600"><?= htmlspecialchars($booking['owner_email']) ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Update Status Form -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Update Booking Status</h2>

                    <form action="index.php?route=admin/change_booking_status" method="POST">
                        <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select id="status" name="status"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="Pending" <?= $booking['status'] == 'Pending' ? 'selected' : '' ?>>
                                        Pending</option>
                                    <option value="Approved" <?= $booking['status'] == 'Approved' ? 'selected' : '' ?>>
                                        Approved</option>
                                    <option value="Canceled" <?= $booking['status'] == 'Canceled' ? 'selected' : '' ?>>
                                        Canceled</option>
                                    <option value="Declined" <?= $booking['status'] == 'Declined' ? 'selected' : '' ?>>
                                        Declined</option>
                                </select>
                            </div>

                            <div>
                                <label for="note" class="block text-sm font-medium text-gray-700 mb-1">Add Note
                                    (optional)</label>
                                <textarea id="note" name="note" rows="3"
                                    class="mt-1 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md"></textarea>
                            </div>
                        </div>

                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Update Status
                        </button>
                    </form>
                </div>

                <!-- Map Section -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Charge Point Location</h2>
                    <div id="station-map" class="h-96 w-full rounded-lg border border-gray-200 mb-4"></div>

                    <div class="text-sm text-gray-600 mb-4">
                        <p>This map shows the location of the charging station for this booking.</p>
                    </div>
                </div>

                <!-- Action buttons section -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <a href="index.php?route=admin/bookings"
                            class="inline-flex items-center text-gray-700 hover:text-indigo-600 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                            Back to all bookings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pass booking data to JavaScript -->
<script>
    // Create a data object to use in our map script
    const bookingData = {
        id: <?= $booking['id'] ?>,
        latitude: <?= $booking['latitude'] ?>,
        longitude: <?= $booking['longitude'] ?>,
        address: "<?= htmlspecialchars(addslashes($booking['address'])) ?>",
        price_per_kWh: <?= $booking['price_per_kWh'] ?>,
        status: "<?= $booking['status'] ?>"
    };
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize map
        const map = L.map('station-map').setView([bookingData.latitude, bookingData.longitude], 14);

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Determine marker color based on booking status
        let markerColor = '#3B82F6'; // Default blue
        if (bookingData.status === 'Approved') {
            markerColor = '#10B981'; // Green
        } else if (bookingData.status === 'Canceled') {
            markerColor = '#EF4444'; // Red
        } else if (bookingData.status === 'Declined') {
            markerColor = '#6B7280'; // Gray
        } else if (bookingData.status === 'Pending') {
            markerColor = '#F59E0B'; // Yellow/amber
        }

        // Create custom marker icon
        const stationIcon = L.divIcon({
            className: 'custom-div-icon',
            html: `
                <div style="background-color: ${markerColor}; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.3);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"></path>
                    </svg>
                </div>
            `,
            iconSize: [40, 40],
            iconAnchor: [20, 20],
            popupAnchor: [0, -20]
        });

        // Add marker for charge point
        const marker = L.marker([bookingData.latitude, bookingData.longitude], {
            icon: stationIcon
        }).addTo(map);

        // Add popup with station info
        marker.bindPopup(`
            <div style="width: 200px;">
                <h3 style="font-weight: bold; margin-bottom: 8px;">${bookingData.address}</h3>
                <p style="margin-bottom: 4px;">$${bookingData.price_per_kWh.toFixed(2)}/kWh</p>
                <p style="color: ${markerColor}; font-weight: bold;">
                    Booking Status: ${bookingData.status}
                </p>
            </div>
        `).openPopup();

        // Add directions button below the map
        const directionsLink = document.createElement('a');
        directionsLink.href = `https://www.google.com/maps/dir/?api=1&destination=${bookingData.latitude},${bookingData.longitude}`;
        directionsLink.target = '_blank';
        directionsLink.className = 'bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 inline-flex items-center mt-4';
        directionsLink.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
            </svg>
            Get Directions
        `;
        document.getElementById('station-map').after(directionsLink);

        // Resize map when container becomes visible
        setTimeout(() => {
            map.invalidateSize();
        }, 100);
    });
</script>