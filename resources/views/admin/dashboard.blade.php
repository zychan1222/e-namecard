<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Include jQuery -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .sort-message {
            font-size: 0.875rem;
            color: #6B7280;
            margin-top: 0.5rem;
        }

        #search,
        #sort-by,
        #filter-admin {
            border: 1px solid #6B7280;
            padding-left: 5px;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }

        .alert-success {
            color: #3c763d;
            background-color: #dff0d8;
            border-color: #d6e9c6;
        }
        
        .alert-danger {
            color: #a94442; 
            background-color: #f2dede; 
            border-color: #ebccd1;
        }        

        .modal {
            display: none;
            position: fixed;
            z-index: 50;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 8px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>

<body>
    @include('partials.admin-header')
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    <header class="bg-white shadow">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">Admin Dashboard</h2>
        <h3 class="text-base font-semibold leading-7 text-gray-900">Welcome back, {{ $employee->name }}</h3>
        </div>
    </header>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h2 class="text-xl font-semibold text-gray-900 mt-8">Employee List</h2>
    <p class="text-sm text-gray-600">Manage employee details below:</p>

    <div class="mt-4 flex">
    <input type="text" id="search" name="search" placeholder="Search by name, email, or phone"
        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 flex-grow border-gray-300 rounded-md"> <!-- Make the search bar flexible -->
    <select id="sort-by" name="sort-by"
        class="ml-3 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block sm:text-sm border-gray-300 rounded-md">
        <option value="name_asc">Name (A-Z)</option>
        <option value="name_desc">Name (Z-A)</option>
        <option value="email_asc">Email (A-Z)</option>
        <option value="email_desc">Email (Z-A)</option>
    </select>
    <select id="filter-admin" name="filter-admin"
        class="ml-3 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block sm:text-sm border-gray-300 rounded-md">
        <option value="all">All Users</option>
        <option value="admins_only">Admins Only</option>
        <option value="non_admins">Non-Admins</option>
    </select>
    <button id="search-btn"
        class="ml-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Search</button>
    <button id="clear-filters-btn"
        class="ml-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">Clear Filters</button>
</div>


@if (!empty($searchMessage))
    <p id="sort-message" class="sort-message">{{ $searchMessage }}</p>
@endif
        
        <div class="mt-4 flex justify-end">
            <a href="{{ route('admin.employee.create') }}"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <img src="{{ asset('storage/createicon.png') }}" alt="Edit Icon" class="w-4 h-4 mr-2">
                Create New User
            </a>
            <button id="manage-admins-button"
                class="ml-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Manage Admins
            </button>
        </div>

        <div id="employee-list" class="mt-4">
            <!-- Employee list will be dynamically loaded here -->
            @include('partials.employee-list')
        </div>
    </div>

    <!-- Modal Structure -->
    <div id="manage-admins-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 class="text-xl font-semibold text-gray-900">Manage Admins</h2>
            @include('auth.manage-admins')
        </div>
    </div><script>
    // Function to load employees based on search query, sort criteria, and admin filter
    function loadEmployees(pageNumber, searchQuery, sortBy, filterAdmin) {
        $.ajax({
            url: "https://da4b-175-143-246-6.ngrok-free.app/e-namecard/public/admin/dashboard/search",
            method: 'GET',
            data: {
                page: pageNumber,
                search: searchQuery,
                sort: sortBy,
                filter_admin: filterAdmin
            },
            success: function(response) {
                $('#employee-list').html(response);

                // Log number of employees shown
                var numEmployees = $(response).find('.employee-item').length;
                console.log(`Showing ${numEmployees} employees for page ${pageNumber}`);

                // Update sort message
                updateSortMessage(sortBy, filterAdmin, searchQuery);
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                // Handle error
            }
        });
    }

    // Event listener for search button click
    $('#search-btn').click(function(e) {
        e.preventDefault(); // Prevent default form submission
        var searchQuery = $('#search').val(); // Get search query
        var sortBy = $('#sort-by').val(); // Get current sort criteria
        var filterAdmin = $('#filter-admin').val(); // Get selected admin filter option

        // Load employees for the first page with search and sort criteria
        loadEmployees(1, searchQuery, sortBy, filterAdmin); 

        // Log search action
        console.log(`Search button clicked - Search query: ${searchQuery}, Sort: ${sortBy}, Filter: ${filterAdmin}`);
    });

    // Clear Filters Button Click
    $('#clear-filters-btn').click(function(e) {
        e.preventDefault(); // Prevent default form submission
        $('#search').val(''); // Clear search input
        $('#sort-by').val('name_asc'); // Reset sort criteria to alphabetical order
        $('#filter-admin').val('all'); // Reset admin filter

        // Load all employees sorted by name (A-Z)
        loadEmployees(1, '', 'name_asc', 'all'); 

        // Log filters cleared action
        console.log('Filters cleared, showing all users sorted by name A-Z.');
    });

    // Event listener for pagination links click
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault(); // Prevent default link behavior
        var page = $(this).attr('href').split('page=')[1]; // Extract page number
        var searchQuery = $('#search').val(); // Get search query
        var sortBy = $('#sort-by').val(); // Get current sort criteria
        var filterAdmin = $('#filter-admin').val(); // Get selected admin filter option

        loadEmployees(page, searchQuery, sortBy, filterAdmin); // Load employees for the clicked page

        // Log pagination action
        console.log(`Pagination clicked - Page: ${page}, Search: ${searchQuery}, Sort: ${sortBy}, Filter: ${filterAdmin}`);
    });

    // Event listener for sort-by select change
    $('#sort-by').change(function() {
        var sortBy = $(this).val(); // Get selected sorting option
        var searchQuery = $('#search').val(); // Get current search query
        var filterAdmin = $('#filter-admin').val(); // Get selected admin filter option

        loadEmployees(1, searchQuery, sortBy, filterAdmin); // Load employees for the first page with updated sort criteria

        // Log sort action
        console.log(`Sorting employees by: ${sortBy}, Filter: ${filterAdmin}`);
    });

    // Event listener for filter-admin select change
    $('#filter-admin').change(function() {
        var filterAdmin = $(this).val(); // Get selected admin filter option
        var searchQuery = $('#search').val(); // Get current search query
        var sortBy = $('#sort-by').val(); // Get current sort criteria

        loadEmployees(1, searchQuery, sortBy, filterAdmin); // Load employees for the first page with updated admin filter

        // Log filter action
        console.log(`Filtering employees by: ${filterAdmin}, Search: ${searchQuery}, Sort: ${sortBy}`);
    });

    // Function to update sort message
    function updateSortMessage(sortBy, filterAdmin, searchQuery) {
        var sortText = '';
        var filterText = '';

        if (sortBy === 'name_asc') {
            sortText = 'Name (A-Z)';
        } else if (sortBy === 'name_desc') {
            sortText = 'Name (Z-A)';
        } else if (sortBy === 'email_asc') {
            sortText = 'Email (A-Z)';
        } else if (sortBy === 'email_desc') {
            sortText = 'Email (Z-A)';
        }

        if (filterAdmin === 'all') {
            filterText = 'All Users';
        } else if (filterAdmin === 'admins_only') {
            filterText = 'Admins Only';
        } else if (filterAdmin === 'non_admins') {
            filterText = 'Non-Admins';
        }

        var searchMessage = `Searching for '${searchQuery}', sorted by ${sortText}, and showing ${filterText}`;
        $('#sort-message').text(searchMessage); // Update sort message

        // Log sort message update
        console.log(`Sort message updated: ${searchMessage}`);
    }

    // Modal script
    $(document).ready(function() {
        var modal = $('#manage-admins-modal');
        var btn = $('#manage-admins-button');
        var span = $('.close');

        // When the user clicks the button, open the modal
        btn.on('click', function() {
            modal.show();
        });

        // When the user clicks on <span> (x), close the modal
        span.on('click', function() {
            modal.hide();
        });

        // When the user clicks anywhere outside of the modal, close it
        $(window).on('click', function(event) {
            if ($(event.target).is(modal)) {
                modal.hide();
            }
        });
    });

    // Function to filter employees in the modal based on search query
    $('#modal-search').on('input', function() {
        var searchQuery = $(this).val().toLowerCase(); // Get search query and convert to lowercase

        // Filter the employee list in the modal
        $('#employee-roles .flex').each(function() {
            var employeeName = $(this).find('span').text().toLowerCase(); // Get employee name and convert to lowercase
            if (employeeName.includes(searchQuery)) {
                $(this).show(); // Show employee item if name matches search query
            } else {
                $(this).hide(); // Hide employee item if name does not match search query
            }
        });
    });
</script>
</body>
</html>