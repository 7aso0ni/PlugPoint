/**
 * Fixed User Search Functionality
 * This script focuses on fixing the search feature
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Search script initialized');
    
    // Get the search input element
    const searchInput = document.getElementById('search');
    const searchClear = document.getElementById('search-clear');
    
    // Initialize current page
    let currentPage = 1;
    
    // Add event listener for input changes
    if (searchInput) {
        console.log('Search input found, adding listeners');
        
        // Add direct input event listener for debugging
        searchInput.addEventListener('input', function(e) {
            console.log('Input event fired, value:', searchInput.value);
            
            // Show clear button if text exists
            if (searchClear) {
                if (searchInput.value.length > 0) {
                    searchClear.classList.remove('hidden');
                } else {
                    searchClear.classList.add('hidden');
                }
            }
        });
        
        // Add debounced search function
        searchInput.addEventListener('input', debounce(function() {
            console.log('Debounced search triggered with:', searchInput.value);
            // Reset to first page when search changes
            currentPage = 1;
            // Perform the search
            fetchUsers(searchInput.value, currentPage);
        }, 300));
        
        // Also add keyup event for Enter key
        searchInput.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                console.log('Enter key pressed, searching for:', searchInput.value);
                currentPage = 1;
                fetchUsers(searchInput.value, currentPage);
            }
        });
    } else {
        console.error('Search input element not found!');
    }
    
    // Add event listener for the clear button
    if (searchClear) {
        searchClear.addEventListener('click', function() {
            console.log('Clear button clicked');
            // Clear the search input
            if (searchInput) {
                searchInput.value = '';
                searchClear.classList.add('hidden');
                
                // Reset to first page
                currentPage = 1;
                
                // Perform the search with empty query
                fetchUsers('', currentPage);
                
                // Focus on the search input
                searchInput.focus();
            }
        });
    }
    
    // Function to handle pagination clicks
    window.changePage = function(page) {
        console.log('Page changed to:', page);
        currentPage = page;
        fetchUsers(searchInput ? searchInput.value : '', page);
        return false; // Prevent default link behavior
    };
    
    // Check initial input value
    if (searchInput && searchClear && searchInput.value.length > 0) {
        searchClear.classList.remove('hidden');
    }
    
    // Load users on initial page load
    console.log('Initial user load');
    fetchUsers('', 1);
});

// Debounce function to limit how often a function can be called
function debounce(func, wait) {
    let timeout;
    return function() {
        const context = this;
        const args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(function() {
            func.apply(context, args);
        }, wait);
    };
}

/**
 * Fetch users with search
 */
function fetchUsers(search, page) {
    console.log('Fetching users with search:', search, 'page:', page);
    
    // Show loading state
    const usersList = document.getElementById('users-list');
    if (usersList) {
        usersList.innerHTML = '<tr><td colspan="7" class="px-6 py-4 text-center">Loading...</td></tr>';
    }
    
    // Create the URL with query parameters
    // Make sure to properly encode the search parameter
    const url = `index.php?route=api/users&search=${encodeURIComponent(search || '')}&page=${page || 1}`;
    console.log('Fetch URL:', url);
    
    // Use fetch API
    fetch(url)
        .then(response => {
            console.log('Response status:', response.status);
            // Log the raw response for debugging
            return response.text().then(text => {
                console.log('Raw response:', text);
                try {
                    // Try to parse the response as JSON
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Error parsing JSON:', e);
                    throw new Error('Server returned invalid JSON');
                }
            });
        })
        .then(data => {
            console.log('Parsed data:', data);
            
            if (!data || !data.success) {
                throw new Error('API returned unsuccessful response');
            }
            
            if (!data.users || !Array.isArray(data.users)) {
                throw new Error('Invalid user data format');
            }
            
            updateUsersList(data.users);
            if (data.pagination) {
                updatePagination(data.pagination);
            }
        })
        .catch(error => {
            console.error('Error in fetch:', error);
            if (usersList) {
                usersList.innerHTML = `
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-red-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-gray-700 text-lg font-medium">Error loading users</p>
                                <p class="text-gray-500 text-sm mt-1">${error.message}</p>
                                <button onclick="fetchUsers(document.getElementById('search').value, 1)" class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Try Again
                                </button>
                            </div>
                        </td>
                    </tr>`;
            }
        });
}

/**
 * Update the users list in the DOM
 */
function updateUsersList(users) {
    const tbody = document.getElementById('users-list');
    if (!tbody) {
        console.error('Users list element not found');
        return;
    }
    
    // Clear existing content
    tbody.innerHTML = '';
    
    if (!users || users.length === 0) {
        console.log('No users found');
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="px-6 py-8 text-center">
                    <div class="flex flex-col items-center">
                        <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-gray-500 text-lg font-medium">No users found</p>
                        <p class="text-gray-400 text-sm mt-1">Try adjusting your search criteria</p>
                    </div>
                </td>
            </tr>`;
        return;
    }
    
    console.log('Updating users list with', users.length, 'users');
    
    // Add each user to the table
    users.forEach(function(user) {
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50';
        
        // Format the role
        const roleName = user.role_id == 1 ? 'Admin' : 'User';
        const roleClass = user.role_id == 1 ? 'bg-gray-100 text-gray-800' : 'bg-blue-100 text-blue-800';
        
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">${user.id}</td>
            <td class="px-6 py-4 whitespace-nowrap">${escapeHtml(user.name)}</td>
            <td class="px-6 py-4 whitespace-nowrap">${escapeHtml(user.email)}</td>
            <td class="px-6 py-4 whitespace-nowrap">${escapeHtml(user.phone)}</td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${roleClass}">
                    ${roleName}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">${user.created_at}</td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button type="button" onclick="editUser(${user.id}, '${escapeHtml(user.name)}', '${escapeHtml(user.email)}', '${escapeHtml(user.phone)}', ${user.role_id})" class="text-indigo-600 hover:text-indigo-900 mr-3">
                    Edit
                </button>
                <button type="button" onclick="confirmDelete(${user.id}, '${escapeHtml(user.name)}')" class="text-red-600 hover:text-red-900">
                    Delete
                </button>
            </td>
        `;
        
        tbody.appendChild(row);
    });
}

/**
 * Update pagination controls
 */
function updatePagination(pagination) {
    const container = document.getElementById('pagination-container');
    if (!container) {
        console.error('Pagination container not found');
        return;
    }
    
    console.log('Updating pagination with', pagination);
    
    if (!pagination || pagination.total_pages <= 1) {
        container.innerHTML = '';
        return;
    }
    
    // Build pagination HTML
    let html = '<div class="flex justify-between items-center bg-white px-4 py-3 sm:px-6 rounded-md">';
    
    // Mobile pagination (simplified version)
    html += '<div class="flex-1 flex justify-between sm:hidden">';
    if (pagination.current_page > 1) {
        html += `<a href="#" onclick="return changePage(${pagination.current_page - 1})" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Previous</a>`;
    } else {
        html += `<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-gray-100">Previous</span>`;
    }
    
    if (pagination.current_page < pagination.total_pages) {
        html += `<a href="#" onclick="return changePage(${pagination.current_page + 1})" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Next</a>`;
    } else {
        html += `<span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-gray-100">Next</span>`;
    }
    html += '</div>';
    
    // Desktop pagination with page numbers
    html += '<div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">';
    html += `<div><p class="text-sm text-gray-700">Showing <span class="font-medium">${pagination.start}</span> to <span class="font-medium">${pagination.end}</span> of <span class="font-medium">${pagination.total_items}</span> results</p></div>`;
    html += '<div>';
    html += '<nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">';
    
    // Previous page button
    if (pagination.current_page > 1) {
        html += `<a href="#" onclick="return changePage(${pagination.current_page - 1})" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
            <span class="sr-only">Previous</span>
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
        </a>`;
    } else {
        html += `<span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-300">
            <span class="sr-only">Previous</span>
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
        </span>`;
    }
    
    // Page numbers
    let startPage = Math.max(1, pagination.current_page - 2);
    let endPage = Math.min(pagination.total_pages, startPage + 4);
    
    if (endPage - startPage < 4) {
        startPage = Math.max(1, endPage - 4);
    }
    
    for (let i = startPage; i <= endPage; i++) {
        if (i === pagination.current_page) {
            html += `<span aria-current="page" class="z-10 bg-indigo-50 border-indigo-500 text-indigo-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium">${i}</span>`;
        } else {
            html += `<a href="#" onclick="return changePage(${i})" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">${i}</a>`;
        }
    }
    
    // Next page button
    if (pagination.current_page < pagination.total_pages) {
        html += `<a href="#" onclick="return changePage(${pagination.current_page + 1})" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
            <span class="sr-only">Next</span>
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
        </a>`;
    } else {
        html += `<span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-300">
            <span class="sr-only">Next</span>
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
        </span>`;
    }
    
    html += '</nav>';
    html += '</div>';
    html += '</div>';
    html += '</div>';
    
    container.innerHTML = html;
}

// Helper function to escape HTML special characters
function escapeHtml(unsafe) {
    if (!unsafe) return '';
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}