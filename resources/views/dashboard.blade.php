<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
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
    </style>
</head>

<body>
    @include('partials.header')
                <!-- Flash messages -->
                @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
    <header class="bg-white shadow">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">{{ $pageTitle }}</h1>
        </div>
    </header>
    
    <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
        <!-- Dashboard content goes here -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-800">About Us</h2>
                <p class="mt-4 text-gray-600">Our website offers a platform for creating and managing electronic name cards, making networking easy and efficient.</p>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-800">Mission</h2>
                <p class="mt-4 text-gray-600">To provide a seamless and eco-friendly solution for professionals to share their contact information digitally.</p>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-800">Vision</h2>
                <p class="mt-4 text-gray-600">To be the leading platform for digital name cards, promoting sustainable and innovative networking solutions.</p>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-800">Contact Us</h2>
                <p class="mt-4 text-gray-600">Email: xxx@xxx.com</p>
                <p class="text-gray-600">Phone: +60-xx-xxxx-xxx</p>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-800">Address</h2>
                <p class="mt-4 text-gray-600">xxx, xxxx, xxxxx</p>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-800">Services</h2>
                <ul class="mt-4 text-gray-600">
                    <li>Digital Name Card Creation</li>
                    <li>Contact Management</li>
                    <li>Networking Solutions</li>
                </ul>
            </div>
        </div>
    </div>
</body>

</html>
