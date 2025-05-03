/**
 * Charge Points Search and Filter System
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialize state
    let currentState = {
        search: '',
        maxPrice: 0.50,
        available: '', // Empty string to show all stations (not just available ones)
        page: 1,
        totalPages: window.initialChargeData.pagination.total_pages || 1
    };

    // DOM elements
    const searchInput = document.getElementById('search');
    const priceRangeInput = document.getElementById('price-range');
    const priceValueElement = document.getElementById('price-value');
    const availabilitySelect = document.getElementById('availability');
    const applyFiltersButton = document.getElementById('apply-filters');
    const resetFiltersButton = document.getElementById('reset-filters');
    const clearFiltersButton = document.getElementById('clear-filters');
    const chargepointsContainer = document.getElementById('chargepoints-container');
    const loadingIndicator = document.getElementById('loading-indicator');
    const noResultsElement = document.getElementById('no-results');
    const prevPageButton = document.getElementById('prev-page');
    const nextPageButton = document.getElementById('next-page');
    const pageNumbersContainer = document.getElementById('page-numbers');
    
    // Card template
    const cardTemplate = document.getElementById('chargepoint-template');

    // Initialize UI
    updatePriceLabel();
    renderPagination();

    // Event listeners
    priceRangeInput.addEventListener('input', updatePriceLabel);
    applyFiltersButton.addEventListener('click', applyFilters);
    resetFiltersButton.addEventListener('click', resetFilters);
    clearFiltersButton.addEventListener('click', resetFilters);
    prevPageButton.addEventListener('click', goToPrevPage);
    nextPageButton.addEventListener('click', goToNextPage);
    
    // Add debounced search for better UX
    searchInput.addEventListener('input', debounce(function() {
        currentState.search = searchInput.value;
        currentState.page = 1; // Reset to first page on new search
        loadChargePoints();
    }, 500));

    // Functions
    function updatePriceLabel() {
        const value = parseFloat(priceRangeInput.value);
        priceValueElement.textContent = value.toFixed(2);
        currentState.maxPrice = value;
    }

    function applyFilters() {
        currentState.search = searchInput.value;
        currentState.maxPrice = parseFloat(priceRangeInput.value);
        currentState.available = availabilitySelect.value;
        currentState.page = 1; // Reset to first page on filter change
        loadChargePoints();
    }

    function resetFilters() {
        // Reset UI
        searchInput.value = '';
        priceRangeInput.value = 0.50;
        availabilitySelect.value = '';
        updatePriceLabel();

        // Reset state
        currentState = {
            search: '',
            maxPrice: 0.50,
            available: '',
            page: 1,
            totalPages: window.initialChargeData.pagination.total_pages || 1
        };

        loadChargePoints();
    }

    function goToPrevPage() {
        if (currentState.page > 1) {
            currentState.page--;
            loadChargePoints();
            window.scrollTo(0, document.getElementById('browse').offsetTop - 100);
        }
    }

    function goToNextPage() {
        if (currentState.page < currentState.totalPages) {
            currentState.page++;
            loadChargePoints();
            window.scrollTo(0, document.getElementById('browse').offsetTop - 100);
        }
    }

    function goToPage(page) {
        currentState.page = page;
        loadChargePoints();
        window.scrollTo(0, document.getElementById('browse').offsetTop - 100);
    }

    function renderPagination() {
        pageNumbersContainer.innerHTML = '';
        
        // Don't render pagination if there's only one page
        if (currentState.totalPages <= 1) {
            prevPageButton.disabled = true;
            nextPageButton.disabled = true;
            prevPageButton.classList.add('opacity-50', 'cursor-not-allowed');
            nextPageButton.classList.add('opacity-50', 'cursor-not-allowed');
            return;
        }

        // Enable/disable prev/next buttons
        prevPageButton.disabled = currentState.page === 1;
        nextPageButton.disabled = currentState.page === currentState.totalPages;
        
        if (prevPageButton.disabled) {
            prevPageButton.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            prevPageButton.classList.remove('opacity-50', 'cursor-not-allowed');
        }
        
        if (nextPageButton.disabled) {
            nextPageButton.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            nextPageButton.classList.remove('opacity-50', 'cursor-not-allowed');
        }

        // Determine pages to show (up to 5)
        let startPage = Math.max(1, currentState.page - 2);
        let endPage = Math.min(currentState.totalPages, startPage + 4);
        
        if (endPage - startPage < 4 && startPage > 1) {
            startPage = Math.max(1, endPage - 4);
        }

        // Add page number buttons
        for (let i = startPage; i <= endPage; i++) {
            const pageButton = document.createElement('button');
            pageButton.textContent = i;
            pageButton.className = 'relative inline-flex items-center px-4 py-2 border text-sm font-medium';
            
            if (i === currentState.page) {
                pageButton.classList.add('z-10', 'bg-green-50', 'border-green-500', 'text-green-600');
            } else {
                pageButton.classList.add('bg-white', 'border-gray-300', 'text-gray-500', 'hover:bg-gray-50');
                pageButton.addEventListener('click', () => goToPage(i));
            }
            
            pageNumbersContainer.appendChild(pageButton);
        }
    }

    function loadChargePoints() {
        // Show loading indicator
        loadingIndicator.classList.remove('hidden');
        chargepointsContainer.classList.add('opacity-50');
        
        // Hide no results message if it's visible
        noResultsElement.classList.add('hidden');

        // Create form data
        const formData = new FormData();
        formData.append('search', currentState.search);
        formData.append('max_price', currentState.maxPrice);
        formData.append('available', currentState.available);
        formData.append('page', currentState.page);

        // Fetch data
        fetch('index.php?route=chargepoints/filter', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Update state with new data
            currentState.totalPages = data.pagination.total_pages;
            
            // Render charge points
            renderChargePoints(data.chargePoints);
            
            // Update pagination
            renderPagination();
            
            // Hide loading indicator
            loadingIndicator.classList.add('hidden');
            chargepointsContainer.classList.remove('opacity-50');
            
            // Show no results message if needed
            if (data.chargePoints.length === 0) {
                noResultsElement.classList.remove('hidden');
                chargepointsContainer.classList.add('hidden');
            } else {
                chargepointsContainer.classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error fetching charge points:', error);
            // Hide loading indicator
            loadingIndicator.classList.add('hidden');
            chargepointsContainer.classList.remove('opacity-50');
            // Show an error message
            alert('Error loading charging stations. Please try again later.');
        });
    }
/**
 * Updates the renderChargePoints function to fix the details URL
 */
function renderChargePoints(chargePoints) {
    // Clear current content
    chargepointsContainer.innerHTML = '';
    
    // Loop through charge points and create cards
    chargePoints.forEach(cp => {
        // Clone template
        const card = cardTemplate.content.cloneNode(true);
        
        // Set image
        const img = card.querySelector('img');
        img.src = cp.image_url;
        img.alt = `Charging station at ${cp.address}`;
        
        // Set availability status
        const statusBadge = card.querySelector('.absolute.top-0.right-0');
        if (cp.availability) {
            statusBadge.classList.add('bg-green-600');
            statusBadge.classList.remove('bg-red-500');
            statusBadge.textContent = 'Available';
        } else {
            statusBadge.classList.add('bg-red-500');
            statusBadge.classList.remove('bg-green-600');
            statusBadge.textContent = 'Unavailable';
        }
        
        // Set address
        card.querySelector('h3').textContent = cp.address;
        
        // Set owner name
        card.querySelector('p').textContent = `Hosted by ${cp.owner_name}`;
        
        // Set price
        card.querySelector('.flex.items-center.mb-3 .text-sm').textContent = 
            `$${parseFloat(cp.price_per_kWh).toFixed(2)}/kWh`;
        
        // Set details link - FIXED URL HERE
        card.querySelector('a.bg-green-600').href = `index.php?route=chargepoints/details&id=${cp.id}`;
        
        // Append to container
        chargepointsContainer.appendChild(card);
    });
}

    // Utility function for debouncing input events
    function debounce(func, wait) {
        let timeout;
        return function() {
            const context = this;
            const args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                func.apply(context, args);
            }, wait);
        };
    }
});