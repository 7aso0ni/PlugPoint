<?php
/**
 * @var array $booking The booking details
 * @var array $cp The charge point details
 */
?>

<div class="bg-gradient-to-b from-green-50 to-blue-50 min-h-screen py-12">
    <div class="container mx-auto px-4 max-w-3xl">
        <!-- Success/Status message -->
        <?php if (isset($_SESSION['message'])): ?>
            <div
                class="mb-6 p-4 rounded-lg <?= $_SESSION['message_type'] === 'error' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' ?>">
                <?= $_SESSION['message'] ?>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php elseif ($booking['status'] === 'Pending'): ?>
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6 rounded shadow">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-8.414l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 111.414-1.414L9 9.586V5a1 1 0 012 0v4.586z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm leading-5 font-medium text-yellow-800">
                            Booking Request Pending
                        </p>
                        <p class="text-sm leading-5 text-yellow-700 mt-1">
                            Your booking is waiting for administrator approval. You'll be notified when it's confirmed.
                        </p>
                    </div>
                </div>
            </div>
        <?php elseif ($booking['status'] === 'Confirmed'): ?>
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded shadow">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm leading-5 font-medium text-green-800">
                            Booking Confirmed
                        </p>
                        <p class="text-sm leading-5 text-green-700 mt-1">
                            Your booking has been approved. You're all set for your charging session.
                        </p>
                    </div>
                </div>
            </div>
        <?php elseif ($booking['status'] === 'Canceled'): ?>
            <div class="bg-gray-50 border-l-4 border-gray-500 p-4 mb-6 rounded shadow">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm leading-5 font-medium text-gray-800">
                            Booking Canceled
                        </p>
                        <p class="text-sm leading-5 text-gray-700 mt-1">
                            This booking has been canceled.
                        </p>
                    </div>
                </div>
            </div>
        <?php elseif ($booking['status'] === 'Declined'): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded shadow">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm leading-5 font-medium text-red-800">
                            Booking Declined
                        </p>
                        <p class="text-sm leading-5 text-red-700 mt-1">
                            This booking request has been declined by the administrator.
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Booking confirmation card -->
        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-blue-600 to-green-600 text-white">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <h1 class="text-2xl font-bold">Booking Details</h1>
                </div>
                <p class="mt-2 opacity-80">Booking ID: #<?= $booking['id'] ?></p>
            </div>

            <!-- Booking details -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left column: Charge point info -->
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Charging Station</h2>

                        <div class="bg-gray-50 rounded-lg overflow-hidden mb-4">
                            <img src="<?= htmlspecialchars($cp['image_url']) ?>"
                                alt="Charging station at <?= htmlspecialchars($cp['address']) ?>"
                                class="w-full h-40 object-cover">

                            <div class="p-4">
                                <p class="font-semibold text-gray-800"><?= htmlspecialchars($cp['address']) ?></p>

                                <div class="mt-2 text-sm text-gray-600">
                                    <div class="flex items-center mb-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-green-600"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span><?= number_format($cp['latitude'] ?? 0, 6) ?>,
                                            <?= number_format($cp['longitude'] ?? 0, 6) ?></span>
                                    </div>

                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-green-600"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>$<?= number_format($cp['price_per_kWh'] ?? 0, 2) ?> per kWh</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="font-medium text-gray-800 mb-2">Owner Information</h3>

                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">
                                        <?= htmlspecialchars($cp['owner_name'] ?? 'Owner') ?>
                                    </p>
                                    <a href="mailto:<?= htmlspecialchars($cp['owner_email'] ?? '') ?>"
                                        class="text-blue-600 hover:underline text-sm">
                                        <?= htmlspecialchars($cp['owner_email'] ?? 'No email provided') ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right column: Booking details -->
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Booking Details</h2>

                        <div class="bg-white border border-green-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <h3 class="font-medium text-gray-800">Start Date & Time</h3>
                            </div>
                            <p class="text-gray-700 ml-7"><?= date('l, F j, Y', strtotime($booking['booking_date'])) ?>
                            </p>
                            <p class="text-gray-700 ml-7"><?= date('g:i A', strtotime($booking['booking_date'])) ?></p>
                        </div>

                        <div class="bg-white border border-green-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="font-medium text-gray-800">End Date & Time</h3>
                            </div>
                            <p class="text-gray-700 ml-7"><?= date('l, F j, Y', strtotime($booking['due_date'])) ?></p>
                            <p class="text-gray-700 ml-7"><?= date('g:i A', strtotime($booking['due_date'])) ?></p>
                        </div>

                        <!-- Calculate duration -->
                        <?php
                        $start = new DateTime($booking['booking_date']);
                        $end = new DateTime($booking['due_date']);
                        $interval = $start->diff($end);
                        $hours = $interval->h + ($interval->days * 24);
                        $minutes = $interval->i;

                        // Format duration
                        $durationText = '';
                        if ($hours > 0) {
                            $durationText .= $hours . ' hour' . ($hours > 1 ? 's' : '');
                        }
                        if ($minutes > 0) {
                            if ($hours > 0) {
                                $durationText .= ' ';
                            }
                            $durationText .= $minutes . ' minute' . ($minutes > 1 ? 's' : '');
                        }
                        ?>

                        <div class="bg-white border border-green-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="font-medium text-gray-800">Duration</h3>
                            </div>
                            <p class="text-gray-700 ml-7"><?= $durationText ?></p>
                        </div>

                        <div class="bg-blue-50 rounded-lg p-4">
                            <h3 class="font-medium text-gray-800 mb-2">Estimated Charging</h3>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <p class="text-sm text-gray-600">Est. Energy</p>
                                    <p class="font-semibold text-gray-800">
                                        <?= number_format($booking['estimated_kwh'] ?? 0, 0) ?> kWh
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Est. Total</p>
                                    <p class="font-semibold text-green-600">
                                        $<?= number_format($booking['estimated_price'] ?? 0, 2) ?></p>
                                </div>
                            </div>

                            <div class="mt-3 text-xs text-gray-500">
                                <p>Actual charges will be based on energy consumed.</p>
                            </div>
                        </div>

                        <!-- Booking status -->
                        <div class="mt-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Status:</span>
                                <?php if ($booking['status'] === 'Pending'): ?>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <svg class="h-3 w-3 mr-1 text-yellow-600" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Pending Approval
                                    </span>
                                <?php elseif ($booking['status'] === 'Confirmed'): ?>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="h-3 w-3 mr-1 text-green-600" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Confirmed
                                    </span>
                                <?php elseif ($booking['status'] === 'Canceled'): ?>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <svg class="h-3 w-3 mr-1 text-red-600" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Canceled
                                    </span>
                                <?php elseif ($booking['status'] === 'Declined'): ?>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <svg class="h-3 w-3 mr-1 text-red-600" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Declined
                                    </span>
                                <?php else: ?>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <?= htmlspecialchars($booking['status']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action buttons -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row justify-between gap-4">
                        <a href="index.php?route=my_bookings"
                            class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Back to Dashboard
                        </a>

                        <div class="flex flex-col sm:flex-row gap-3">
                            <a href="#" onclick="window.print()"
                                class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                Print
                            </a>

                            <?php if ($booking['status'] === 'Pending'): ?>
                                <a href="index.php?route=cancel_booking&id=<?= $booking['id'] ?>"
                                    onclick="return confirm('Are you sure you want to cancel this booking?');"
                                    class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                                    Cancel Booking
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional information -->
        <div class="mt-8 bg-white shadow-lg rounded-xl overflow-hidden">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Additional Information</h2>

                <div class="space-y-4 text-gray-600">
                    <div class="flex">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-600 flex-shrink-0"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <div>
                            <p class="font-medium text-gray-800">Cancellation Policy</p>
                            <p>You can cancel your booking anytime before the scheduled start time at no cost.</p>
                        </div>
                    </div>

                    <div class="flex">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-600 flex-shrink-0"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="font-medium text-gray-800">Charging Instructions</p>
                            <p>Please arrive on time for your booking. The owner of the charging station will provide
                                any specific instructions needed for access.</p>
                        </div>
                    </div>

                    <div class="flex">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-600 flex-shrink-0"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="font-medium text-gray-800">Payment</p>
                            <p>You will only be charged for the actual energy consumed. Payment will be processed
                                automatically after your charging session.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>