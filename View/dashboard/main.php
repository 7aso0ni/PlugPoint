<div class="container mx-auto px-4">
    <!-- Simple Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold">My Bookings Dashboard</h1>
    </div>

    <?php if (isset($_SESSION['message'])): ?>
        <div
            class="mb-6 p-4 rounded-lg <?= $_SESSION['message_type'] === 'error' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' ?>">
            <?= $_SESSION['message'] ?>
        </div>
        <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
    <?php endif; ?>

    <!-- Rentals Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <th class="px-6 py-3">Charger</th>
                        <th class="px-6 py-3">Booking Time</th>
                        <th class="px-6 py-3">Price</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if (empty($rentals)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                No bookings found. <a href="index.php?route=chargepoints"
                                    class="text-blue-600 hover:underline">Find a charging station</a> to book.
                            </td>
                        </tr>
                    <?php else: ?>
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
                                    <?php
                                    $startDate = date('Y-m-d', strtotime($rental['booking_date']));
                                    $endDate = date('Y-m-d', strtotime($rental['due_date']));
                                    $sameDay = ($startDate === $endDate);
                                    ?>
                                    <div class="text-sm text-gray-900">
                                        <?php if ($sameDay): ?>
                                            <?= date('M j, Y', strtotime($rental['booking_date'])) ?>
                                        <?php else: ?>
                                            <?= date('M j', strtotime($rental['booking_date'])) ?> -
                                            <?= date('M j, Y', strtotime($rental['due_date'])) ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <?= date('g:i A', strtotime($rental['booking_date'])) ?> -
                                        <?= date('g:i A', strtotime($rental['due_date'])) ?>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        <?php
                                        // Calculate duration in seconds
                                        $durationSeconds = strtotime($rental['due_date']) - strtotime($rental['booking_date']);

                                        // Calculate hours and minutes
                                        $hours = floor($durationSeconds / 3600);
                                        $minutes = floor(($durationSeconds % 3600) / 60);

                                        // Format the duration string
                                        if ($hours > 0 && $minutes > 0) {
                                            echo 'Duration: ' . $hours . ' hr ' . $minutes . ' min';
                                        } elseif ($hours > 0) {
                                            echo 'Duration: ' . $hours . ' hr';
                                        } elseif ($minutes > 0) {
                                            echo 'Duration: ' . $minutes . ' min';
                                        } else {
                                            echo 'Duration: Less than 1 min';
                                        }
                                        ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        <!-- Use estimated price from booking if available -->
                                        $<?= htmlspecialchars(number_format($rental['estimated_price'] ?? ($rental['price_per_kWh'] * 20), 2)) ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        (<?= $rental['estimated_kwh'] ?? '~20' ?> kWh)
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ($rental['status'] === 'Pending'): ?>
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                    <?php elseif ($rental['status'] === 'Confirmed' || $rental['status'] === 'Approved'): ?>
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Confirmed
                                        </span>
                                    <?php elseif ($rental['status'] === 'Declined'): ?>
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Declined
                                        </span>
                                    <?php elseif ($rental['status'] === 'Canceled'): ?>
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-300 text-gray-700">
                                            Canceled
                                        </span>
                                    <?php endif; ?>

                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <a href="index.php?route=booking_confirmation&id=<?= $rental['booking_id'] ?? $rental['id'] ?>"
                                        class="text-indigo-600 hover:text-indigo-900 mr-3">View Details</a>
                                    <?php if ($rental['status'] === 'Pending'): ?>
                                        <form method="POST" action="/PlugPoint/index.php?route=cancel_booking"
                                            style="display:inline;"
                                            onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                            <input type="hidden" name="booking_id"
                                                value="<?= $rental['booking_id'] ?? $rental['id'] ?>">
                                            <button type="submit" class="text-red-600 hover:text-red-900 ml-2">Cancel</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-gray-400 ml-2">No Actions</span>
                                    <?php endif; ?>

                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>