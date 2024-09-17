<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organization Profile Page</title>
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
                {{ $errors->first() }}
            </div>
        @endif
        <div class="flex justify-between items-center mt-4">         
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">Organization Information</h1>
                <p class="mt-1 max-w-2xl text-sm leading-6 text-gray-500">Currently viewing the details for {{ $organization->name }}</p>    
            </div>
            <div class="flex space-x-4">
                <button id="editButton" onclick="toggleEditMode()" class="flex items-center justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    <img src="{{ asset('storage/editicon.png') }}" alt="Edit Icon" class="w-4 h-4 mr-2">
                    Edit Information
                </button>
            </div>
        </div>
    </div>
</header>
<div class="mt-6 border-t border-gray-100 lg:pl-20 sm:px-0">
    <form id="profileForm" action="{{ route('admin.organization.update', ['organization' => $organization->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <dl class="divide-y divide-gray-100">
            <div class="px-8 py-6 lg:pl-20 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">Logo</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                    <img src="{{ $organization->logo ? asset('storage/logo/' . $organization->logo) : asset('storage/default-logo.jpg') }}" alt="Organization Logo" class="w-24 h-24 border-2 border-gray-300">
                    <input type="file" name="logo" class="mt-2 hidden">
                    <div id="newLogoPreview" class="mt-2"></div>
                </dd>
            </div>
            <div class="px-8 py-6 lg:pl-20 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">Organization ID</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{ $organization->id }}</dd>
            </div>
            <div class="px-8 py-6 lg:pl-20 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">Organization Owner</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{ $ownerEmail }}</dd>
            </div>          
            <div class="px-8 py-6 lg:pl-20 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">Name</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                    <span>{{ $organization->name }}</span>
                    <input type="text" name="name" value="{{ old('name', $organization->name) }}" class="border border-gray-300 rounded-md w-1/2 px-4 py-3 hidden" required>
                </dd>
            </div>
            <div class="px-8 py-6 lg:pl-20 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">Address</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                    <span>{{ $organization->address }}</span>
                    <input type="text" name="address" value="{{ old('address', $organization->address) }}" class="border border-gray-300 rounded-md w-1/2 px-4 py-3 hidden" required>
                </dd>
            </div>
            <div class="px-8 py-6 lg:pl-20 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">Phone</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                    <span>{{ $organization->phoneNo }}</span>
                    <input type="text" name="phoneNo" value="{{ old('phoneNo', $organization->phoneNo) }}" class="border border-gray-300 rounded-md w-1/2 px-4 py-3 hidden" required>
                </dd>
            </div>
            <div class="px-8 py-6 lg:pl-20 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">Email</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                    <span>{{ $organization->email }}</span>
                    <input type="text" name="email" value="{{ old('email', $organization->email) }}" class="border border-gray-300 rounded-md w-1/2 px-4 py-3 hidden" required>
                </dd>
            </div>
        </dl>
        <div id="saveButtons" class="px-8 py-6 lg:pl-20 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0" style="display: {{ $editMode ? 'block' : 'none' }}">
            <div class="flex justify-center space-x-4 w-full">
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:shadow-outline transition ease-in-out duration-150">
                    <img src="{{ asset('storage/saveicon.png') }}" alt="Save Icon" class="w-4 h-4 mr-2">
                    Save Changes
                </button>
                <button type="button" onclick="toggleEditMode()" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm leading-5 font-medium rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:shadow-outline transition ease-in-out duration-150">Cancel</button>
            </div>
        </div>
    </form>
</div>
<script>
    let editMode = @json($editMode);
    console.log("Edit Mode Status:", editMode);

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

    const logoInput = document.querySelector('input[name="logo"]');
    if (logoInput) {
        logoInput.addEventListener('change', (event) => {
            const file = event.target.files[0];
            const reader = new FileReader();
            reader.onload = (e) => {
                const previewContainer = document.getElementById('newLogoPreview');
                previewContainer.innerHTML = `<img src="${e.target.result}" alt="New Logo" class="w-24 h-24 border-2 border-gray-300 mt-2">`;
            };
            reader.readAsDataURL(file);
        });
    }
    console.log("Current Signed-In User:", <?php echo json_encode(auth()->user()); ?>);
</script>
</body>
</html>
