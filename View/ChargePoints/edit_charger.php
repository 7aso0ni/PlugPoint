<div class="max-w-xl mx-auto mt-10 bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-6">Edit Charger</h2>

    <?php if (!empty($successMessage)): ?>
        <div class="bg-green-100 text-green-800 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($successMessage) ?>
        </div>
    <?php endif; ?>

<form method="POST" action="index.php?route=homeowner/edit_charger&id=<?= $charger['id'] ?>" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $charger['id'] ?>">

        <div class="mb-4">
            <label class="block font-medium">Address</label>
            <input name="address" value="<?= htmlspecialchars($charger['address']) ?>"
                   class="w-full border px-4 py-2 rounded" required>
        </div>

        <div class="mb-4">
            <label class="block font-medium">Price (BD/hour)</label>
            <input name="price" type="number" step="0.01" value="<?= $charger['price_per_kWh'] ?>"
                   class="w-full border px-4 py-2 rounded" required>
        </div>

        <div class="mb-4">
            <label class="block font-medium">Availability</label>
            <select name="availability" class="w-full border px-4 py-2 rounded">
                <option value="1" <?= $charger['availability'] ? 'selected' : '' ?>>Available</option>
                <option value="0" <?= !$charger['availability'] ? 'selected' : '' ?>>Unavailable</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block font-medium">Change Image (optional)</label>
            <input type="file" name="image" class="w-full border px-4 py-2 rounded">
            <p class="text-sm mt-2">Current: <?= htmlspecialchars($charger['image_url']) ?></p>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                Update Charger
            </button>
        </div>
    </form>
</div>
