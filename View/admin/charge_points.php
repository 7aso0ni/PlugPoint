<div class="bg-gray-100 min-h-screen">
    <!-- Admin Header -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-900"><?= $title ?></h1>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">Welcome, <?= htmlspecialchars($_SESSION['user']['name']) ?></span>
                    <form method="POST" action="index.php?route=logout">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M3 3a1 1 0 00-1 1v12a1 1 0 001 1h12a1 1 0 001-1V4a1 1 0 00-1-1H3zm5 4a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1zm0 4a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1zm-3 1a1 1 0 100-2H4a1 1 0 100 2h1z"
                                    clip-rule="evenodd" />
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Search and Add New Station -->
        <div class="px-4 sm:px-0 mb-6 flex flex-col sm:flex-row justify-between items-center">
            <div class="w-full sm:w-auto mb-4 sm:mb-0">
                <form action="index.php?route=admin/charge_points" method="GET" class="flex">
                    <input type="hidden" name="route" value="admin/charge_points">
                    <div class="relative rounded-md shadow-sm">
                        <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>" 
                            class="focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:w-64 pl-3 pr-10 py-2 border border-gray-300 rounded-md" 
                            placeholder="Search stations...">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" 
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                    <button type="submit" 
                        class="ml-2 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Search
                    </button>
                </form>
            </div>
            <a href="index.php?route=admin/add_charge_point_form" 
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" 
                    fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" 
                        d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" 
                        clip-rule="evenodd" />
                </svg>
                Add Charging Station (Map)
            </a>
        </div>

        <!-- Charge Points List -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <?php if (isset($_SESSION['message'])): ?>
                <div class="p-4 <?= $_SESSION['message_type'] === 'success' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' ?>">
                    <?= $_SESSION['message'] ?>
                </div>
                <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
            <?php endif; ?>

            <?php if (empty($chargePoints)): ?>
                <div class="py-8 text-center text-gray-500">
                    <p>No charging stations found.</p>
                    <p class="mt-2 text-sm">Click "Add Charging Station" to create one.</p>
                </div>
            <?php else: ?>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Address
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Price per kWh
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Owner
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($chargePoints as $cp): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= $cp['id'] ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= htmlspecialchars($cp['address']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    $<?= number_format($cp['price_per_kWh'], 2) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= htmlspecialchars($cp['owner_name']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?= $cp['availability'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                        <?= $cp['availability'] ? 'Available' : 'Unavailable' ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button onclick="showEditModal(<?= $cp['id'] ?>)" class="text-indigo-600 hover:text-indigo-900 mr-4">
                                        Edit
                                    </button>
                                    <button onclick="confirmDelete(<?= $cp['id'] ?>)" class="text-red-600 hover:text-red-900">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Showing <span class="font-medium"><?= ($currentPage - 1) * $perPage + 1 ?></span> to 
                                    <span class="font-medium"><?= min($currentPage * $perPage, $totalChargePoints) ?></span> of 
                                    <span class="font-medium"><?= $totalChargePoints ?></span> results
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                    <?php if ($currentPage > 1): ?>
                                        <a href="index.php?route=admin/charge_points&page=<?= $currentPage - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
                                            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                            <span class="sr-only">Previous</span>
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <a href="index.php?route=admin/charge_points&page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
                                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium 
                                            <?= $i === $currentPage ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50' ?>">
                                            <?= $i ?>
                                        </a>
                                    <?php endfor; ?>
                                    
                                    <?php if ($currentPage < $totalPages): ?>
                                        <a href="index.php?route=admin/charge_points&page=<?= $currentPage + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
                                            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                            <span class="sr-only">Next</span>
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                    <?php endif; ?>
                                </nav>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>
</div>

<!-- Add Charging Station Modal -->
<div id="addModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all max-w-lg w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                    <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Add New Charging Station
                    </h3>
                    <div class="mt-4">
                        <form action="index.php?route=admin/add_charge_point" method="POST" enctype="multipart/form-data">
                            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                                    <div class="mt-1">
                                        <input type="text" name="address" id="address" required
                                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </div>

                                <div>
                                    <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude</label>
                                    <div class="mt-1">
                                        <input type="number" name="latitude" id="latitude" step="0.000001" required
                                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </div>

                                <div>
                                    <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude</label>
                                    <div class="mt-1">
                                        <input type="number" name="longitude" id="longitude" step="0.000001" required
                                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </div>

                                <div>
                                    <label for="price_per_kWh" class="block text-sm font-medium text-gray-700">Price per kWh ($)</label>
                                    <div class="mt-1">
                                        <input type="number" name="price_per_kWh" id="price_per_kWh" step="0.01" min="0" required
                                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </div>

                                <div>
                                    <label for="availability" class="block text-sm font-medium text-gray-700">Availability</label>
                                    <div class="mt-1">
                                        <select id="availability" name="availability"
                                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                            <option value="1">Available</option>
                                            <option value="0">Unavailable</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="owner_id" class="block text-sm font-medium text-gray-700">Owner</label>
                                    <div class="mt-1">
                                        <select id="owner_id" name="owner_id" required
                                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                            <option value="">Select Owner</option>
                                            <?php foreach ($owners as $owner): ?>
                                                <option value="<?= $owner['id'] ?>"><?= htmlspecialchars($owner['name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="image" class="block text-sm font-medium text-gray-700">Image (optional)</label>
                                    <div class="mt-1">
                                        <input type="file" name="image" id="image" accept="image/*"
                                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300">
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">JPG, PNG, GIF up to 2MB</p>
                                </div>
                            </div>

                            <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                                <button type="submit"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:col-start-2 sm:text-sm">
                                    Add Station
                                </button>
                                <button type="button" onclick="hideAddModal()"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Charging Station Modal -->
<div id="editModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all max-w-lg w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                    <svg class="h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Edit Charging Station
                    </h3>
                    <div class="mt-4">
                        <form id="editForm" action="index.php?route=admin/update_charge_point" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="cp_id" id="edit_cp_id">
                            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <label for="edit_address" class="block text-sm font-medium text-gray-700">Address</label>
                                    <div class="mt-1">
                                        <input type="text" name="address" id="edit_address" required
                                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </div>

                                <div>
                                    <label for="edit_latitude" class="block text-sm font-medium text-gray-700">Latitude</label>
                                    <div class="mt-1">
                                        <input type="number" name="latitude" id="edit_latitude" step="0.000001" required
                                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </div>

                                <div>
                                    <label for="edit_longitude" class="block text-sm font-medium text-gray-700">Longitude</label>
                                    <div class="mt-1">
                                        <input type="number" name="longitude" id="edit_longitude" step="0.000001" required
                                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </div>

                                <div>
                                    <label for="edit_price_per_kWh" class="block text-sm font-medium text-gray-700">Price per kWh ($)</label>
                                    <div class="mt-1">
                                        <input type="number" name="price_per_kWh" id="edit_price_per_kWh" step="0.01" min="0" required
                                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </div>

                                <div>
                                    <label for="edit_availability" class="block text-sm font-medium text-gray-700">Availability</label>
                                    <div class="mt-1">
                                        <select id="edit_availability" name="availability"
                                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                            <option value="1">Available</option>
                                            <option value="0">Unavailable</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="edit_owner_id" class="block text-sm font-medium text-gray-700">Owner</label>
                                    <div class="mt-1">
                                        <select id="edit_owner_id" name="owner_id" required
                                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                            <option value="">Select Owner</option>
                                            <?php foreach ($owners as $owner): ?>
                                                <option value="<?= $owner['id'] ?>"><?= htmlspecialchars($owner['name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="edit_image" class="block text-sm font-medium text-gray-700">Image (optional)</label>
                                    <div class="mt-1">
                                        <input type="file" name="image" id="edit_image" accept="image/*"
                                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300">
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">Leave empty to keep current image</p>
                                </div>
                            </div>

                            <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                                <button type="submit"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:col-start-2 sm:text-sm">
                                    Update Station
                                </button>
                                <button type="button" onclick="hideEditModal()"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all max-w-lg w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                    <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Delete Charging Station
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            Are you sure you want to delete this charging station? This action cannot be undone.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <form id="deleteForm" action="index.php?route=admin/delete_charge_point" method="POST">
                <input type="hidden" name="cp_id" id="delete_cp_id">
                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Delete
                </button>
            </form>
            <button type="button" onclick="hideDeleteModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                Cancel
            </button>
        </div>
    </div>
</div>

<!-- JavaScript for modals -->
<script>
    // Data for editing
    let chargePoints = <?= json_encode($chargePoints) ?>;
    
    // Add Modal
    function showAddModal() {
        document.getElementById('addModal').classList.remove('hidden');
    }
    
    function hideAddModal() {
        document.getElementById('addModal').classList.add('hidden');
    }
    
    // Edit Modal
    function showEditModal(id) {
        // Find charge point data
        const cp = chargePoints.find(cp => parseInt(cp.id) === id);
        
        if (cp) {
            // Fill form with data
            document.getElementById('edit_cp_id').value = cp.id;
            document.getElementById('edit_address').value = cp.address;
            document.getElementById('edit_latitude').value = cp.latitude;
            document.getElementById('edit_longitude').value = cp.longitude;
            document.getElementById('edit_price_per_kWh').value = cp.price_per_kWh;
            document.getElementById('edit_availability').value = cp.availability;
            document.getElementById('edit_owner_id').value = cp.owner_id;
            
            // Show modal
            document.getElementById('editModal').classList.remove('hidden');
        }
    }
    
    function hideEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }
    
    // Delete Modal
    function confirmDelete(id) {
        document.getElementById('delete_cp_id').value = id;
        document.getElementById('deleteModal').classList.remove('hidden');
    }
    
    function hideDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }
</script>