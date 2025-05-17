<div class="container mx-auto px-4 py-10">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">My Charging Stations</h2>
        <a href="index.php?route=homeowner/add_charger" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
            <i class="fas fa-plus mr-2"></i> Add New Charger
        </a>
    </div>

    <?php if (empty($myChargers)): ?>
        <div class="text-center py-12 bg-white rounded-lg shadow-md">
            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            <h3 class="mt-2 text-lg font-medium text-gray-900">No charging stations yet</h3>
            <p class="mt-1 text-gray-500">Get started by adding your first charging station.</p>
        </div>
    <?php else: ?>
        <?php foreach ($myChargers as $cp): ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                <div class="md:flex">
                    <!-- Charger details -->
                    <div class="md:w-1/3">
                        <div class="relative">
                            <img src="<?= htmlspecialchars($cp['image_url'] ?: 'images/default.jpg') ?>"
                                alt="Charging station at <?= htmlspecialchars($cp['address']) ?>"
                                class="w-full h-48 md:h-full object-cover">
                            <span class="absolute top-0 right-0 text-white text-xs font-bold px-2 py-1 m-2 rounded
                                <?= $cp['availability'] ? 'bg-green-600' : 'bg-red-500' ?>">
                                <?= $cp['availability'] ? 'Available' : 'Unavailable' ?>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Charger info and actions -->
                    <div class="md:w-2/3 p-6">
                        <h3 class="text-xl font-semibold mb-2"><?= htmlspecialchars($cp['address']) ?></h3>
                        
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <p class="text-gray-500 text-sm">Price per kWh</p>
                                <p class="font-medium">$<?= number_format($cp['price_per_kWh'], 2) ?></p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">Location</p>
                                <p class="font-medium"><?= number_format($cp['latitude'], 6) ?>, <?= number_format($cp['longitude'], 6) ?></p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">Status</p>
                                <p class="font-medium <?= $cp['availability'] ? 'text-green-600' : 'text-red-600' ?>">
                                    <?= $cp['availability'] ? 'Available' : 'Unavailable' ?>
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">Created</p>
                                <p class="font-medium"><?= date('M d, Y', strtotime($cp['created_at'] ?? 'now')) ?></p>
                            </div>
                        </div>
                        
                        <div class="flex space-x-3">
                            <a href="index.php?route=homeowner/edit_charger&id=<?= $cp['id'] ?>"
                               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 flex items-center">
                                <i class="fas fa-edit mr-2"></i> Edit
                            </a>
                            <form method="POST" action="index.php?route=homeowner/delete_charger">
                                <input type="hidden" name="id" value="<?= $cp['id'] ?>">
                                <button type="submit"
                                        class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 flex items-center"
                                        onclick="return confirm('Are you sure you want to delete this charger?');">
                                    <i class="fas fa-trash mr-2"></i> Delete
                                </button>
                            </form>
                            <a href="index.php?route=chargepoints/details&id=<?= $cp['id'] ?>"
                               class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 flex items-center">
                                <i class="fas fa-eye mr-2"></i> View
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Booking Requests Section -->
                <?php 
                // Get pending bookings for this charger
                $pendingBookings = $chargerBookings[$cp['id']] ?? [];
                include 'View/ChargePoints/booking_requests.php'; 
                ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
