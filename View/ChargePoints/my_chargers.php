<div class="container mx-auto px-4 py-10">
    <h2 class="text-2xl font-bold mb-6">My Charging Stations</h2>

    <?php if (empty($myChargers)): ?>
        <div class="text-center text-gray-500">
            You haven’t added any charging stations yet.
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php foreach ($myChargers as $cp): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="relative">
                        <img src="<?= htmlspecialchars($cp['image_url'] ?: 'images/default.jpg') ?>"
                             alt="Charging station at <?= htmlspecialchars($cp['address']) ?>"
                             class="w-full h-48 object-cover">
                        <span class="absolute top-0 right-0 text-white text-xs font-bold px-2 py-1 m-2 rounded
                            <?= $cp['availability'] ? 'bg-green-600' : 'bg-red-500' ?>">
                            <?= $cp['availability'] ? 'Available' : 'Unavailable' ?>
                        </span>
                    </div>
                    <div class="p-4">
                        <h3 class="text-lg font-semibold mb-1"><?= htmlspecialchars($cp['address']) ?></h3>
                        <div class="text-gray-600 text-sm mb-2">
                            ⚡ <?= number_format($cp['price_per_kWh'], 2) ?> BD/hour
                        </div>
                        <div class="flex justify-between gap-2">
                            <a href="index.php?route=edit_charger&id=<?= $cp['id'] ?>"
                               class="bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 w-1/2 text-center">
                                Edit
                            </a>
                            <form method="POST" action="index.php?route=delete_charger" class="w-1/2">
                                <input type="hidden" name="id" value="<?= $cp['id'] ?>">
                                <button type="submit"
                                        class="bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700 w-full"
                                        onclick="return confirm('Are you sure you want to delete this charger?');">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
