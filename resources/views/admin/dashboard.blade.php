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
        #filter-user {
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
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">Admin Dashboard</h1>
            <h3 class="text-base font-semibold leading-7 text-gray-900">Welcome back, {{ Auth::user()->name }}</h3>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h2 class="text-xl font-semibold text-gray-900 mt-8">User List</h2>
        <p class="text-sm text-gray-600">Manage user details below:</p>

        <form id="filter-form" method="GET" action="{{ route('admin.dashboard') }}">
            <div class="mt-4 flex">
                <input type="text" id="search" name="search" placeholder="Search by name or email"
                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 flex-grow border-gray-300 rounded-md"
                    value="{{ request('search') }}">
                <select id="sort-by" name="sort-by"
                    class="ml-3 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block sm:text-sm border-gray-300 rounded-md">
                    <option value="name_asc" {{ request('sort-by') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                    <option value="name_desc" {{ request('sort-by') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                    <option value="email_asc" {{ request('sort-by') == 'email_asc' ? 'selected' : '' }}>Email (A-Z)</option>
                    <option value="email_desc" {{ request('sort-by') == 'email_desc' ? 'selected' : '' }}>Email (Z-A)</option>
                </select>
                <select id="filter-user" name="filter-user"
                    class="ml-3 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block sm:text-sm border-gray-300 rounded-md">
                    <option value="all" {{ request('filter-user') == 'all' ? 'selected' : '' }}>All Users</option>
                    <option value="admins_only" {{ request('filter-user') == 'admins_only' ? 'selected' : '' }}>Admins Only</option>
                    <option value="non_admins" {{ request('filter-user') == 'non_admins' ? 'selected' : '' }}>Non-Admins</option>
                </select>
                <button type="submit" id="search-btn"
                    class="ml-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Search</button>
                <button type="button" id="clear-filters-btn"
                    class="ml-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">Clear Filters</button>
            </div>
        </form>

        @if (!empty($searchMessage))
        <p id="sort-message" class="sort-message">{{ $searchMessage }}</p>
        @endif

        <div class="mt-4 flex justify-end">
            <a href="{{ route('admin.employee.create') }}"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <img src="{{ asset('storage/createicon.png') }}" alt="Create Icon" class="w-4 h-4 mr-2">
                Create New User
            </a>
        </div>

        <div id="user-list" class="mt-4">
            @include('partials.employee-list')
        </div>
    </div>

    <script>
        $(document).ready(function () {
            // Clear Filters button functionality
            $('#clear-filters-btn').on('click', function () {
                $('#search').val('');
                $('#sort-by').val('name_asc');
                $('#filter-user').val('all');
                $('#filter-form').submit();
            });
        });
    </script>
</body>

</html>
