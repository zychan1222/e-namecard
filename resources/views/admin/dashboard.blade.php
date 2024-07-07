<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    @include('partials.admin-header')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h2 class="text-xl font-semibold text-gray-900">Admin Dashboard</h2>
        <p class="text-sm text-gray-600">Welcome back, {{ $employee->name }}</p>
        <h2 class="text-xl font-semibold text-gray-900 mt-8">Employee List</h2>
        <p class="text-sm text-gray-600">Manage employee details below:</p>

    <div>
        {{ $employees->links() }}
    </div>
    <dl class="divide-y divide-gray-100">
        <ul role="list" class="divide-y divide-gray-100">
            @foreach($employees as $emp)
            <li>
                <a href="{{ route('admin.employee.profile', $emp->id) }}" class="flex justify-between gap-x-6 py-5 hover:bg-gray-50">
                    <div class="flex min-w-0 gap-x-4">
                        <img class="h-12 w-12 flex-none rounded-full bg-gray-50"
                            src="{{ $emp->profile_pic ? asset('storage/' . $emp->profile_pic) : asset('storage/default-user.jpg') }}"
                            alt="{{ $emp->name }}">

                        <div class="min-w-0 flex-auto">
                            <p class="text-sm font-semibold leading-6 text-gray-900">{{ $emp->name }}</p>
                            <p class="mt-1 truncate text-xs leading-5 text-gray-500">{{ $emp->email }}</p>
                        </div>
                    </div>

                    <div class="hidden sm:flex sm:flex-col sm:items-end">
                        <p class="text-sm leading-6 text-gray-900">{{ $emp->company_name }}</p>
                        <p class="mt-1 text-xs leading-5 text-gray-500">{{ $emp->designation }}</p>
                    </div>
                </a>
            </li>
            @endforeach
        </ul>
    </div>
</body>
<script>
console.log("Current Signed-In User:", <?php echo json_encode(auth()->user()); ?>);
</script>
</html>
