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

    @if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">Employee Profile Page</h1>
        <div class="flex justify-between items-center mt-4">
            <div>
                <h3 class="text-base font-semibold leading-7 text-gray-900">{{ $editMode ? 'Edit' : 'View' }} Employee Information</h3>
                <p class="mt-1 max-w-2xl text-sm leading-6 text-gray-500">{{ $editMode ? 'Edit' : 'Details about' }} the employee.</p>
            </div>
            <div class="flex space-x-4">
                <button id="editButton" onclick="toggleEditMode()" class="flex items-center justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600" data-edit-mode="false">
                    <img src="{{ asset('storage/editicon.png') }}" alt="Edit Icon" class="w-4 h-4 mr-2">
                    Edit Profile
                </button>

                @if ($userOrg->organization->owner_id !== $userOrg->user->id)
                    <form id="deleteEmployeeForm" action="{{ route('admin.employee.destroy', ['employee' => $userOrg->user->id]) }}" method="POST" class="inline">
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
    <form id="profileForm" action="{{ route('admin.employee.update', ['employee' => $userOrg->user->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <dl class="divide-y divide-gray-100">
            <div class="px-8 py-6 lg:pl-20 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">Role</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                    <span>{{ $roleName }}</span>
                    <select name="role_id" class="border border-gray-300 rounded-md w-1/2 px-4 py-3 hidden">
                        <option value="2" {{ $userOrg->user->role_id == 2 ? 'selected' : '' }}>Admin</option>
                        <option value="3" {{ $userOrg->user->role_id == 3 ? 'selected' : '' }}>User</option>
                    </select>
                </dd>
            </div>
            <div class="px-8 py-6 lg:pl-20 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">Profile Picture</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                    <img id="profilePicPreview" src="{{ $userOrg->user->profile_pic ? asset('storage/profile_pics/' . $userOrg->user->profile_pic) : asset('storage/default-user.jpg') }}" alt="Profile Picture" class="w-24 h-24 border-2 border-gray-300">
                    <input type="file" name="profile_pic" class="mt-2 hidden">
                    <div id="newProfilePicPreview" class="mt-2"></div>
                </dd>
            </div>
            <div class="px-8 py-6 lg:pl-20 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">User ID</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                    <span>{{ $userOrg->user->id }}</span>
                    <input type="text" name="id" value="{{ $userOrg->user->id }}" class="border border-gray-300 rounded-md w-1/2 px-4 py-3 hidden" disabled>
                </dd>
            </div>
            <div class="px-8 py-6 lg:pl-20 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">Name</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                    <span>{{ $userOrg->user->name }}</span>
                    <input type="text" name="name" value="{{ $userOrg->user->name }}" class="border border-gray-300 rounded-md w-1/2 px-4 py-3 hidden" required>
                </dd>
            </div>
            <div class="px-8 py-6 lg:pl-20 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">Chinese Name</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                    <span>{{ $userOrg->user->name_cn }}</span>
                    <input type="text" name="name_cn" value="{{ $userOrg->user->name_cn }}" class="border border-gray-300 rounded-md w-1/2 px-4 py-3 hidden" required>
                </dd>
            </div>
            <div class="px-8 py-6 lg:pl-20 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">Email</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                    <span>{{ $userOrg->user->email }}</span>
                    <input type="email" name="email" value="{{ $userOrg->user->email }}" class="border border-gray-300 rounded-md w-1/2 px-4 py-3 hidden" required>
                </dd>
            </div>
            <div class="px-8 py-6 lg:pl-20 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">Phone</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                    <span>{{ $userOrg->user->phone }}</span>
                    <input type="text" name="phone" value="{{ $userOrg->user->phone }}" class="border border-gray-300 rounded-md w-1/2 px-4 py-3 hidden">
                </dd>
            </div>
            <div class="px-8 py-6 lg:pl-20 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">Department</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                    <span>{{ $userOrg->user->department }}</span>
                    <input type="text" name="department" value="{{ $userOrg->user->department }}" class="border border-gray-300 rounded-md w-1/2 px-4 py-3 hidden">
                </dd>
            </div>
            <div class="px-8 py-6 lg:pl-20 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">Designation</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                    <span>{{ $userOrg->user->designation }}</span>
                    <input type="text" name="designation" value="{{ $userOrg->user->designation }}" class="border border-gray-300 rounded-md w-1/2 px-4 py-3 hidden">
                </dd>
            </div>
            <div class="px-8 py-6 lg:pl-20 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">Status</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                    <span>{{ $userOrg->user->is_active ? 'Active' : 'Inactive' }}</span>
                    <select name="is_active" class="border border-gray-300 rounded-md w-1/2 px-4 py-3 hidden">
                        <option value="1" {{ $userOrg->user->is_active ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !$userOrg->user->is_active ? 'selected' : '' }}>Inactive</option>
                    </select>
                </dd>
            </div>
            <div class="px-8 py-6 lg:pl-20 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0 flex justify-center">
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0 flex justify-center">
                    <button id="saveButton" type="submit" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:shadow-outline transition ease-in-out duration-150 hidden">
                        <img src="{{ asset('storage/saveicon.png') }}" alt="Save Icon" class="w-4 h-4 mr-2">
                        Save Changes
                    </button>
                </dd>
            </div>
        </dl>
    </form>
</div>
<script>
function toggleEditMode() {
    const form = document.getElementById('profileForm');
    const displayFields = form.querySelectorAll('dd span');
    const inputFields = form.querySelectorAll('dd input, dd select');
    const editButton = document.getElementById('editButton');
    const saveButton = document.getElementById('saveButton');

    const isEditing = editButton.dataset.editMode === 'true';

    displayFields.forEach(field => {
        field.classList.toggle('hidden');
    });

    inputFields.forEach(field => {
        field.classList.toggle('hidden');
    });

    if (isEditing) {
        editButton.innerText = 'Edit Profile';
        editButton.classList.remove('bg-gray-600');
        editButton.classList.add('bg-indigo-600');
        editButton.dataset.editMode = 'false';
        saveButton.classList.add('hidden');
    } else {
        editButton.innerText = 'Cancel Edit';
        editButton.classList.remove('bg-indigo-600');
        editButton.classList.add('bg-gray-600');
        editButton.dataset.editMode = 'true';
        saveButton.classList.remove('hidden');
    }
}
</script>
</body>
</html>