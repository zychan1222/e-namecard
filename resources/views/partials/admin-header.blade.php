<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <div class="min-h-full">
        <nav class="bg-gray-800">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-28 items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <img class="h-20 w-25" src="{{ asset('storage/logo-no-bg.png') }}" alt="Your Company">
                        </div>
                        <div class="hidden md:block">
                            <div class="ml-10 flex items-baseline space-x-4">
                                <!-- Dashboard -->
                                <a href="{{ route('admin.dashboard') }}"
                                    class="rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-gray-900' : 'bg-gray-800' }} px-3 py-2 text-sm font-medium text-white hover:bg-gray-700">Dashboard</a>
                                <!-- Manage Organizations -->
                                <a href="{{ route('admin.organization') }}"
                                    class="rounded-md {{ request()->routeIs('admin.organization') ? 'bg-gray-900' : 'bg-gray-800' }} px-3 py-2 text-sm font-medium text-white hover:bg-gray-700">Manage Organization</a>
                            </div>
                        </div>
                    </div>
                    <div class="hidden md:flex items-center space-x-4">
                        @php
                            $employeeId = session('employee_id');
                            $employee = $employeeId ? \App\Models\Employee::find($employeeId) : null;
                        @endphp
                        @if ($employee)
                            <div class="text-white text-right mr-4">
                                <span class="block text-sm font-semibold">{{ $employee->name }}</span>
                                <span class="block text-xs">{{ $employee->designation }}</span>
                            </div>
                            <!-- Profile dropdown -->
                            <div class="relative">
                                <button type="button"
                                    class="relative flex max-w-xs items-center rounded-full bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800"
                                    id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                    <span class="absolute -inset-1.5"></span>
                                    <span class="sr-only">Open user menu</span>
                                    <img src="{{ $employee->profile_pic ? asset('storage/profile_pics/' . $employee->profile_pic) : asset('storage/default-user.jpg') }}" alt="Profile Picture" class="w-10 h-10 rounded-full">
                                </button>
                                <!-- Dropdown menu, show/hide based on menu state -->
                                <div id="dropdown-menu"
                                    class="hidden absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                                    role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button"
                                    tabindex="-1">
                                    <div>
                                        <form method="POST" action="{{ route('adminlogout') }}" id="logout-form">
                                            @csrf
                                            <button type="submit"
                                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                                role="menuitem" tabindex="-1" id="user-menu-item-1">
                                                Sign Out
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-white text-right mr-4">
                                <span class="block text-sm font-semibold">Guest</span>
                            </div>
                        @endif
                    </div>
                    <div class="-mr-2 flex md:hidden">
                        <!-- Mobile menu button -->
                        <button type="button"
                            class="relative inline-flex items-center justify-center rounded-md bg-gray-800 p-2 text-gray-400 hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800"
                            aria-controls="mobile-menu" aria-expanded="false">
                            <span class="absolute -inset-0.5"></span>
                            <span class="sr-only">Open main menu</span>
                            <!-- Menu open: "hidden", Menu closed: "block" -->
                            <svg class="block h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                            <!-- Menu open: "block", Menu closed: "hidden" -->
                            <svg class="hidden h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6 6h18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </nav>
    </div>
    <script>
        // Toggle visibility of dropdown menu
        const userMenuButton = document.getElementById('user-menu-button');
        const dropdownMenu = document.getElementById('dropdown-menu');

        userMenuButton.addEventListener('click', function (event) {
            event.stopPropagation(); // Prevent click event from propagating to document
            dropdownMenu.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside of it
        document.addEventListener('click', function (event) {
            if (!dropdownMenu.classList.contains('hidden') && !dropdownMenu.contains(event.target) && event.target !== userMenuButton) {
                dropdownMenu.classList.add('hidden');
            }
        });

        // Prevent dropdown from closing when clicking inside it
        dropdownMenu.addEventListener('click', function (event) {
            event.stopPropagation();
        });
    </script>
</body>

</html>
