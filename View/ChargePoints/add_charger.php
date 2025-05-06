<div class="max-w-2xl mx-auto py-10 px-6 bg-white shadow-md rounded-lg mt-6">
    <h1 class="text-2xl font-bold mb-6 text-center text-green-600">Add New Charger</h1>

    <form method="POST" enctype="multipart/form-data" action="index.php?route=homeowner/save_charger" class="space-y-6">
        <div>
            <label class="block text-gray-700 mb-1">Location (Address)</label>
            <input name="location" type="text" class="w-full border border-gray-300 px-4 py-2 rounded-lg" placeholder="Enter location" required>
        </div>

        <div>
            <label class="block text-gray-700 mb-1">Price (BD/hour)</label>
            <input name="price" type="number" step="0.01" class="w-full border border-gray-300 px-4 py-2 rounded-lg" placeholder="e.g., 0.50" required>
        </div>

        <div>
            <label class="block text-gray-700 mb-1">Available From</label>
            <input name="available_from" type="datetime-local" class="w-full border border-gray-300 px-4 py-2 rounded-lg" required>
        </div>

        <div>
            <label class="block text-gray-700 mb-1">Available To</label>
            <input name="available_to" type="datetime-local" class="w-full border border-gray-300 px-4 py-2 rounded-lg" required>
        </div>

        <div>
            <label class="block text-gray-700 mb-1">Optional Image</label>
            <input name="image" type="file" class="w-full border border-gray-300 px-4 py-2 rounded-lg">
        </div>

        <div class="text-center">
            <button type="submit" class="bg-green-600 text-white py-2 px-6 rounded-lg hover:bg-green-700">Add Charger</button>
        </div>
    </form>
</div>
