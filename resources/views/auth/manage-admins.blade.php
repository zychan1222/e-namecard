<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('modal-search');
        const employeeRoles = document.getElementById('employee-roles');
        const employeeProfiles = employeeRoles.querySelectorAll('.user-profile');

        searchInput.addEventListener('input', function () {
            const searchTerm = searchInput.value.toLowerCase();
            employeeProfiles.forEach(profile => {
                const userInfo = profile.querySelector('.user-info');
                const name = userInfo.querySelector('span').textContent.toLowerCase();
                const email = userInfo.querySelector('.email').textContent.toLowerCase();

                if (name.includes(searchTerm) || email.includes(searchTerm)) {
                    profile.style.display = '';
                } else {
                    profile.style.display = 'none';
                }
            });
        });
    });
</script>

<style>
    #modal-search {
        height: 2rem;
        padding-left: 3px;
        margin-bottom: 1rem;
    }
    .role-dropdown {
        margin-left: auto;
        width: 125px;
    }
    .user-profile {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
        padding: 0.5rem;
    }
    .user-info {
        margin-left: 10px;
    }
    .user-profile img {
        width: 50px;
        height: 50px;
        border: 2px solid #ccc;
        border-radius: 50%;
    }
    .user-info span {
        font-weight: bold;
    }
    .user-info .email {
        font-size: 0.9rem;
        color: #666;
    }
</style>

@if ($employee->is_owner)
<form action="{{ route('admin.update-roles') }}" method="POST">
    <div class="mt-4">
        <div class="mb-4">
            <br>
            <h2 class="text-lg font-medium mb-2">Existing Admins</h2>
            <ul>
                @foreach ($modalEmployees as $employee)
                    @if ($employee->admin)
                        <div class="flex items-center justify-between mb-2 user-profile" data-name="{{ $employee->name }}" data-email="{{ $employee->user->email }}">
                            <img class="rounded-full"
                            src="{{ $employee->profile_pic ? asset('storage/profile_pics/' . $employee->profile_pic) : asset('storage/default-user.jpg') }}"
                            alt="{{ $employee->name }}">
                            <div class="user-info">
                                <span>{{ $employee->name }}</span>
                                <div class="email">{{ $employee->user->email }}</div>
                            </div>
                                @if ($employee->is_owner)
                                <select disabled name="roles[{{ $employee->id }}]" class="role-dropdown border rounded p-2">
                                    <option value="owner" disabled selected>Owner</option>
                                </select>
                                @else
                                <select name="roles[{{ $employee->id }}]" class="role-dropdown border rounded p-2">
                                    <option value="employee" {{ $employee->admin ? '' : 'selected' }}>Employee</option>
                                    <option value="admin" {{ $employee->admin ? 'selected' : '' }}>Admin</option>
                                </select>
                                @endif
                        </div>
                    @endif
                @endforeach
            </ul>
        </div>

        <br>
        <h2 class="text-lg font-medium mb-2">Add New Admins</h2>
        <input type="text" id="modal-search" name="modal-search" placeholder="Search employees"
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md mb-4">

        <div id="employee-roles">
            @foreach ($modalEmployees as $employee)
                @if (!$employee->admin)
                    <div class="flex items-center justify-between mb-2 user-profile" data-name="{{ $employee->name }}" data-email="{{ $employee->user->email }}">
                        <img class="rounded-full"
                        src="{{ $employee->profile_pic ? asset('storage/profile_pics/' . $employee->profile_pic) : asset('storage/default-user.jpg') }}"
                            alt="{{ $employee->name }}">
                        <div class="user-info">
                            <span>{{ $employee->name }}</span>
                            <div class="email">{{ $employee->user->email }}</div>
                        </div>
                        <select name="roles[{{ $employee->id }}]" class="role-dropdown border rounded p-2">
                            <option value="employee" {{ $employee->admin ? '' : 'selected' }}>Employee</option>
                            <option value="admin" {{ $employee->admin ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                @endif
            @endforeach
        </div>

        <div class="flex justify-center mt-4">
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                Save Changes
            </button>
        </div>
    </div>
    @csrf
</form>
@else
    <div class="mt-4">
        <div class="mb-4">
            <h2 class="text-lg font-medium mb-2">Existing Admins</h2>
            <ul>
                @foreach ($modalEmployees as $employee)
                    @if ($employee->admin)
                        <div class="flex items-center justify-between mb-2 user-profile" data-name="{{ $employee->name }}" data-email="{{ $employee->user->email }}">
                            <img class="rounded-full"
                                src="{{ $employee->profile_pic ? asset('storage/' . $employee->profile_pic) : asset('storage/default-user.jpg') }}"
                                alt="{{ $employee->name }}">
                            <div class="user-info">
                                <span>{{ $employee->name }}</span>
                                <div class="email">{{ $employee->user->email }}</div>
                            </div>
                            @if ($employee->is_owner)
                                <select disabled name="roles[{{ $employee->id }}]" class="role-dropdown border rounded p-2">
                                    <option value="owner" disabled selected>Owner</option>
                                </select>
                            @else
                                <select disabled name="roles[{{ $employee->id }}]" class="role-dropdown border rounded p-2">
                                    <option value="admin" selected>Admin</option>
                                </select>
                            @endif
                        </div>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>
@endif