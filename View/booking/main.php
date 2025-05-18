<?php
/**
 * @var array $cp Charge point details
 * @var array $bookedSlots Array of booked time slots
 * @var string $startDate Current date
 * @var string $endDate End date for calendar (30 days from now)
 */
?>

<div class="bg-gradient-to-b from-green-50 to-blue-50 min-h-screen py-12">
    <div class="container mx-auto px-4 max-w-4xl">
        <!-- Back button -->
        <div class="mb-4">
            <a href="index.php?route=chargepoint&id=<?= $cp['id'] ?>"
                class="inline-flex items-center text-gray-700 hover:text-green-600 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to station details
            </a>
        </div>

        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <!-- Header with station info -->
            <div class="bg-gray-50 p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center">
                    <div class="flex-shrink-0 mb-4 md:mb-0 md:mr-6">
                        <div class="h-16 w-16 rounded-full bg-blue-100 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Book Charging Session</h1>
                        <p class="text-gray-600"><?= htmlspecialchars($cp['address']) ?></p>
                        <div class="mt-1 flex items-center">
                            <span
                                class="text-green-600 font-semibold">$<?= number_format($cp['price_per_kWh'], 2) ?></span>
                            <span class="text-gray-600 ml-1">per kWh</span>

                            <?php if ($cp['availability']): ?>
                                <span
                                    class="ml-4 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <span class="h-1.5 w-1.5 mr-1 rounded-full bg-green-500"></span>
                                    Available
                                </span>
                            <?php else: ?>
                                <span
                                    class="ml-4 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <span class="h-1.5 w-1.5 mr-1 rounded-full bg-red-500"></span>
                                    Unavailable
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking form -->
            <div class="p-6">
                <?php if (isset($_SESSION['message'])): ?>
                    <div
                        class="mb-6 p-4 rounded-lg <?= $_SESSION['message_type'] === 'error' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' ?>">
                        <?= $_SESSION['message'] ?>
                    </div>
                    <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
                <?php endif; ?>

                <form action="index.php?route=create_booking" method="post" id="booking-form">
                    <input type="hidden" name="charge_point_id" value="<?= $cp['id'] ?>">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Calendar section (Flatpickr replaces the old div) -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Select Date</h3>

                            <!-- new -->
                            <input type="text" id="date-picker"
                                class="w-full p-2 border border-gray-300 rounded cursor-pointer bg-white"
                                placeholder="Click to choose a date" readonly required>

                            <!-- Selected date display (unchanged) -->
                            <div class="mt-4 p-3 bg-white rounded border border-gray-200">
                                <p class="text-sm text-gray-600">Selected Date:</p>
                                <p class="font-semibold text-gray-800" id="selected-date-display">Select a date</p>
                            </div>
                        </div>


                        <!-- Time selection section -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Select Time</h3>

                            <!-- Time slots -->
                            <div class="mb-6">
                                <label class="block text-gray-700 mb-2">Start Time</label>
                                <div class="time-selector" id="start-time-selector">
                                    <select name="start_time" id="start-time"
                                        class="w-full p-2 border border-gray-300 rounded" required>
                                        <option value="">Select a date first</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-6">
                                <label class="block text-gray-700 mb-2">Duration</label>
                                <div class="duration-selector">
                                    <select name="duration" id="duration"
                                        class="w-full p-2 border border-gray-300 rounded" required>
                                        <option value="">Select start time first</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Charging estimate -->
                            <div class="mt-6 p-4 bg-blue-50 rounded-lg" id="charging-estimate" style="display: none;">
                                <h4 class="font-semibold text-gray-800 mb-2">Estimated Charging</h4>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-600">Duration</p>
                                        <p class="font-semibold text-gray-800" id="duration-display">-</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Est. Energy</p>
                                        <p class="font-semibold text-gray-800" id="energy-display">-</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Price per kWh</p>
                                        <p class="font-semibold text-gray-800">
                                            $<?= number_format($cp['price_per_kWh'], 2) ?></p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Est. Total</p>
                                        <p class="font-semibold text-green-600" id="price-display">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden fields for form submission -->
                    <input type="hidden" name="booking_date" id="booking_date" required>
                    <input type="hidden" name="due_date" id="due_date" required>

                    <!-- Submit section -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <a href="index.php?route=chargepoint&id=<?= $cp['id'] ?>"
                                class="text-gray-600 hover:text-gray-800">
                                Cancel
                            </a>

                            <button type="submit"
                                class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
                                id="submit-booking" disabled>
                                Confirm Booking
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Help section -->
        <div class="mt-8 bg-white shadow-lg rounded-xl overflow-hidden">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Booking Information</h2>

                <div class="space-y-4 text-gray-600">
                    <div class="flex">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-600 flex-shrink-0"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p>You can book time slots in advance to ensure the charging station is available when you need
                            it.</p>
                    </div>

                    <div class="flex">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-600 flex-shrink-0"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p>Bookings can be made up to 30 days in advance. You can cancel your booking anytime before the
                            scheduled start time.</p>
                    </div>

                    <div class="flex">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-600 flex-shrink-0"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p>You will only be charged for the energy you actually consume during your session. The
                            estimated cost is based on average charging rates.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Booked slots data for JS -->
<script>
    // Pass PHP data to JavaScript
    const chargePointData = {
        id: <?= $cp['id'] ?>,
        pricePerKwh: <?= $cp['price_per_kWh'] ?>,
        bookedSlots: <?= json_encode($bookedSlots) ?>
    };
</script>

<!-- Include booking calendar JS -->
<script src="/View/booking/booking-calender.js"></script>