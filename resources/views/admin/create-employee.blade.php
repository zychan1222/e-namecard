<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Employee</title>
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
@include('partials.admin-header')

    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger">
        {{ $errors->first() }}
    </div>
    @endif

    <header class="bg-white shadow">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">Create New Employee</h1>
            <p class="mt-1 max-w-2xl text-sm leading-6 text-gray-500">New Users will receive an email notification once their account is successfully created!</p>
        </div>
    </header>

    <div class="mt-6 border-t border-gray-100 lg:pl-20 sm:px-0">
        <form id="profileForm" action="{{ route('admin.employee.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <dl class="divide-y divide-gray-100">
                <!-- Name -->
                <div class="px-8 py-6 lg:pl-20 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                    <dt class="text-sm font-medium leading-6 text-gray-900">Name</dt>
                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                        <input type="text" name="name" value="{{ old('name') }}" class="border border-gray-300 rounded-md w-full sm:w-1/2 px-4 py-3" required>
                    </dd>
                </div>
                <!-- CN Name -->
                <div class="px-8 py-6 lg:pl-20 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                    <dt class="text-sm font-medium leading-6 text-gray-900">Chinese Name</dt>
                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                        <input type="text" name="name_cn" value="{{ old('name_cn') }}" class="border border-gray-300 rounded-md w-full sm:w-1/2 px-4 py-3" required>
                    </dd>
                </div>
                <!-- Email -->
                <div class="px-8 py-6 lg:pl-20 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                    <dt class="text-sm font-medium leading-6 text-gray-900">Email</dt>
                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                        <input type="email" name="email" value="{{ old('email') }}" class="border border-gray-300 rounded-md w-full sm:w-1/2 px-4 py-3" required>
                    </dd>
                </div>
                <!-- Phone -->
                <div class="px-8 py-6 lg:pl-20 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                    <dt class="text-sm font-medium leading-6 text-gray-900">Phone</dt>
                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                        <input type="text" name="phone" value="{{ old('phone') }}" class="border border-gray-300 rounded-md w-full sm:w-1/2 px-4 py-3" required>
                    </dd>
                </div>
                <!-- Department -->
                <div class="px-8 py-6 lg:pl-20 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                    <dt class="text-sm font-medium leading-6 text-gray-900">Department</dt>
                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                        <input type="text" name="department" value="{{ old('department') }}" class="border border-gray-300 rounded-md w-full sm:w-1/2 px-4 py-3" required>
                    </dd>
                </div>
                <!-- Designation -->
                <div class="px-8 py-6 lg:pl-20 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                    <dt class="text-sm font-medium leading-6 text-gray-900">Designation</dt>
                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                        <input type="text" name="designation" value="{{ old('designation') }}" class="border border-gray-300 rounded-md w-full sm:w-1/2 px-4 py-3" required>
                    </dd>
                </div>
                <!-- Is Active -->
                <div class="px-8 py-6 lg:pl-20 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                    <dt class="text-sm font-medium leading-6 text-gray-900">Is Active</dt>
                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                        <select name="is_active" class="border border-gray-300 rounded-md px-4 py-3 w-full sm:w-1/2">
                            <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>No</option>
                        </select>
                    </dd>
                </div>
            </dl>
            <div id="saveButtons" class="flex justify-center mt-6 space-x-4">
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:shadow-outline transition ease-in-out duration-150">
                    <img src="{{ asset('storage/saveicon.png') }}" alt="Save Icon" class="w-4 h-4 mr-2">
                    Save Changes
                </button>
                <button type="button" onclick="confirmCancel()" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-gray-600 hover:bg-gray-500 focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                    Cancel
                </button>
            </div>
        </form>
    </div>

    <script>
        function confirmCancel() {
            if (confirm('Are you sure you want to cancel? Any unsaved changes will be lost.')) {
                window.location.href = '{{ route('admin.dashboard') }}';
            }
        }
    </script>
</body>
</html>
