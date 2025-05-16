<?php
// This view displays pending booking requests for a specific charger
// It's included in the my_chargers.php page
?>

<div class="mt-6 bg-white rounded-lg shadow-md overflow-hidden">
    <div class="bg-blue-50 px-4 py-3 border-b border-blue-100">
        <h3 class="text-lg font-semibold text-blue-800">Pending Booking Requests</h3>
    </div>

    <?php if (empty($pendingBookings)): ?>
        <div class="p-4 text-center text-gray-500">
            No pending booking requests for this charger.
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Time</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Time</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Est. Price</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($pendingBookings as $booking): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($booking['user_name']) ?></div>
                                        <div class="text-sm text-gray-500"><?= htmlspecialchars($booking['user_email']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <?= date('M d, Y', strtotime($booking['booking_date'])) ?>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <?= date('h:i A', strtotime($booking['booking_date'])) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <?= date('M d, Y', strtotime($booking['due_date'])) ?>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <?= date('h:i A', strtotime($booking['due_date'])) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                // Calculate estimated price if not set
                                $estimatedPrice = $booking['estimated_price'];
                                if ($estimatedPrice === null) {
                                    $bookingDate = new DateTime($booking['booking_date']);
                                    $dueDate = new DateTime($booking['due_date']);
                                    $duration = $bookingDate->diff($dueDate);
                                    $durationHours = $duration->h + ($duration->i / 60); // Include minutes in hours
                                    
                                    // Estimate kWh based on 3kW charging rate (3kWh per hour)
                                    $estimatedKwh = $durationHours * 3;
                                    $estimatedPrice = $estimatedKwh * $booking['price_per_kWh'];
                                }
                                ?>
                                <div class="text-sm font-medium text-gray-900">
                                    $<?= number_format($estimatedPrice, 2) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <form method="POST" action="index.php?route=booking/approve">
                                        <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                                        <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">
                                            Approve
                                        </button>
                                    </form>
                                    <form method="POST" action="index.php?route=booking/decline">
                                        <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                                        <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">
                                            Decline
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
