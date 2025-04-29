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
                    <th class="px-6 py-3">Rental Date</th>
                    <th class="px-6 py-3">Price</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                <?php foreach ($rentals as $rental): ?>
                    <tr>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0 bg-gray-300 rounded overflow-hidden">
                                    <img class="h-10 w-10 rounded"
                                         src="<?= htmlspecialchars($rental['image_url'] ?? '/api/placeholder/40/40') ?>"
                                         alt="Charger">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= htmlspecialchars($rental['address']) ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        $<?= htmlspecialchars(number_format($rental['price_per_kWh'], 2)) ?> per kWh
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                <?= date('M d', strtotime($rental['booking_date'])) ?>
                                - <?= date('M d, Y', strtotime($rental['due_date'])) ?>
                            </div>
                            <div class="text-sm text-gray-500">
                                <?= (strtotime($rental['due_date']) - strtotime($rental['booking_date'])) / 86400 ?>
                                days
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                <!-- Optional total price if you want to show -->
                                $<?= htmlspecialchars(number_format($rental['price_per_kWh'] * 20, 2)) ?>
                            </div>
                            <div class="text-sm text-gray-500">
                                (~20 kWh assumed)
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <?php if ($rental['status'] === 'Pending'): ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Pending
                                </span>
                            <?php elseif ($rental['status'] === 'Approved'): ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Approved
                            </span>
                            <?php elseif ($rental['status'] === 'Declined'): ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Declined
                                </span>
                            <?php elseif ($rental['status'] === 'Canceled'): ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-300 text-gray-700">
                                Canceled
                            </span>
                            <?php endif; ?>

                        </td>
                        <td class="px-6 py-4 text-sm">
                            <a href="#" class="text-indigo-600 hover:text-indigo-900 mr-3">View Details</a>
                            <?php if ($rental['status'] === 'Pending'): ?>
                                <form method="POST" action="/MVCProject/index.php?route=cancelRental"
                                      style="display:inline;">
                                    <input type="hidden" name="booking_id" value="<?= $rental['booking_id'] ?>">
                                    <button type="submit" class="text-red-600 hover:text-red-900 ml-2">Cancel</button>
                                </form>
                            <?php else: ?>
                                <span class="text-gray-400 ml-2">No Actions</span>
                            <?php endif; ?>

                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>