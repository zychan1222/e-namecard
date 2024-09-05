<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Profile Page</title>
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
<header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
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
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">{{ $pageTitle }}</h1>
        <div class="flex justify-between items-center mt-4">
            <div>
                <h3 class="text-base font-semibold leading-7 text-gray-900">{{ $editMode ? 'Edit' : 'View' }} Employee Information</h3>
                <p class="mt-1 max-w-2xl text-sm leading-6 text-gray-500">{{ $editMode ? 'Edit' : 'Details about' }} the employee.</p>
            </div>
            <div class="flex space-x-4">
                <button id="editButton" onclick="toggleEditMode()" class="flex items-center justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    <img src="{{ asset('storage/editicon.png') }}" alt="Edit Icon" class="w-4 h-4 mr-2">
                    Edit Profile
                </button>

                @if ($employee->organization->owner_id !== $employee->id)
                    <form id="deleteEmployeeForm" action="{{ route('admin.employee.destroy', ['employee' => $employee->id]) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Are you sure you want to delete this employee?')" class="flex items-center justify-center rounded-md bg-red-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600">
                            <img src="{{ asset('storage/deleteicon.png') }}" alt="Delete Icon" class="w-4 h-4 mr-2">
                            Delete Employee
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</header>
<div class="mt-6 border-t border-gray-100 lg:pl-20 sm:px-0">
    <form id="profileForm" action="{{ route('admin.employee.update', ['employee' => $employee->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <dl class="divide-y divide-gray-100">
            <div class="px-8 py-6 lg:pl-20 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">Profile Picture</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                    <img src="{{ $employee->profile_pic ? asset('storage/profile_pics/' . $employee->profile_pic) : asset('storage/default-user.jpg') }}" alt="Profile Picture" class="w-24 h-24 border-2 border-gray-300">
                    <input type="file" name="profile_pic" class="mt-2 hidden">
                    <div id="newProfilePicPreview" class="mt-2"></div>
                </dd>
            </div>
            <div class="px-8 py-6 lg:pl-20 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">User ID</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{ $employee->id }}</dd>
            </div>
            <div class="px-8 py-6 lg:pl-20 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">Name</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                    <span>{{ $employee->name }}</span>
                    <input type="text" name="name" value="{{ $employee->name }}" class="border border-gray-300 rounded-md w-1/2 px-4 py-3 hidden" required>
                </dd>
            </div>
            <div class="px-8 py-6 lg:pl-20 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">Chinese Name</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                    <span>{{ $employee->name_cn }}</span>
                    <input type="text" name="name_cn" value="{{ $employee->name_cn }}" class="border border-gray-300 rounded-md w-1/2 px-4 py-3 hidden" required>
                </dd>
            </div>
            <div class="px-8 py-6 lg:pl-20 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">Email</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{ $employee->user->email }}</dd>
            </div>
            <div class="px-8 py-6 lg:pl-20 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">Phone</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                    <span>{{ $employee->phone }}</span>
                    <input type="text" name="phone" value="{{ $employee->phone }}" class="border border-gray-300 rounded-md w-1/2 px-4 py-3 hidden" required>
                </dd>
            </div>
            <div class="px-8 py-6 lg:pl-20 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">Company Name</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{ $employee->organization->name }}</dd>
            </div>
            <div class="px-8 py-6 lg:pl-20 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">Department</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                    <span>{{ $employee->department }}</span>
                    <input type="text" name="department" value="{{ $employee->department }}" class="border border-gray-300 rounded-md w-1/2 px-4 py-3 hidden" required>
                </dd>
            </div>
            <div class="px-8 py-6 lg:pl-20 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">Designation</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                    <span>{{ $employee->designation }}</span>
                    <input type="text" name="designation" value="{{ $employee->designation }}" class="border border-gray-300 rounded-md w-1/2 px-4 py-3 hidden" required>
                </dd>
            </div>
            <div class="px-8 py-6 lg:pl-20 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">Is Active</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                    <span>{{ $employee->is_active ? 'Yes' : 'No' }}</span>
                    <select name="is_active" class="border border-gray-300 rounded-md px-4 py-3 hidden">
                        <option value="1" {{ $employee->is_active ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ !$employee->is_active ? 'selected' : '' }}>No</option>
                    </select>
                </dd>
            </div>
        </dl>
        <div id="saveButtons" class="px-8 py-6 lg:pl-20 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0" style="display: {{ $editMode ? 'block' : 'none' }}">
            <div class="flex justify-center space-x-4 w-full">
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:shadow-outline transition ease-in-out duration-150">
                    <img src="{{ asset('storage/saveicon.png') }}" alt="Edit Icon" class="w-4 h-4 mr-2">
                    Save Changes
                </button>
                <button type="button" onclick="toggleEditMode()" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm leading-5 font-medium rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:shadow-outline transition ease-in-out duration-150">Cancel</button>
            </div>
        </div>
    </form>
</div>
<script>
    let editMode = <?php echo json_encode($editMode); ?>;

    function toggleEditMode() {
        const elements = document.querySelectorAll('input[type="text"], input[type="file"], select, #editButton');
        elements.forEach(el => {
            el.classList.toggle('hidden');
        });

        const spans = document.querySelectorAll('dd > span');
        spans.forEach(span => {
            span.classList.toggle('hidden');
        });

        editMode = !editMode;

        const saveButtons = document.getElementById('saveButtons');
        saveButtons.style.display = editMode ? 'block' : 'none';
    }

    const profilePicInput = document.querySelector('input[name="profile_pic"]');
    if (profilePicInput) {
        profilePicInput.addEventListener('change', (event) => {
            const file = event.target.files[0];
            const reader = new FileReader();
            reader.onload = (e) => {
                const previewContainer = document.getElementById('newProfilePicPreview');
                previewContainer.innerHTML = `<img src="${e.target.result}" alt="New Profile Picture" class="w-24 h-24 border-2 border-gray-300 mt-2">`;
            };
            reader.readAsDataURL(file);
        });
    }
</script>
</body>
</html>
