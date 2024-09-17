<dl class="divide-y divide-gray-100">
    <ul role="list" class="divide-y divide-gray-100">
        @foreach($userOrganizations as $userOrg)
        <li>
            <a href="{{ route('admin.employee.profile', $userOrg->user->id) }}"
                class="flex justify-between gap-x-6 py-5 hover:bg-gray-50">
                <div class="flex min-w-0 gap-x-4">
                    <img class="h-12 w-12 flex-none rounded-full bg-gray-50"
                        src="{{ $userOrg->user->profile_pic ? asset('storage/profile_pics/' . $userOrg->user->profile_pic) : asset('storage/default-user.jpg') }}"
                        alt="{{ $userOrg->user->name }}">

                    <div class="min-w-0 flex-auto">
                        <div class="flex items-center">
                            <p class="text-sm font-semibold leading-6 text-gray-900">{{ $userOrg->user->name }}</p>
                            {{-- Check the role of the user in the organization --}}
                            @php
                                $roleName = $userOrg->organization->owner_id === $userOrg->user->id ? 'Owner' : (\App\Models\Role::find($userOrg->role_id)->name ?? '');
                            @endphp

                            @if($roleName === 'Owner')
                                <span class="ml-2 inline-flex items-center rounded bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">
                                    Owner
                                </span>
                            @elseif($roleName === 'Admin')
                                <span class="ml-2 inline-flex items-center rounded bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800">
                                    Admin
                                </span>
                            @elseif($roleName === 'User')
                                <span class="ml-2 inline-flex items-center rounded bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-800">
                                    User
                                </span>
                            @endif
                        </div>
                        <p class="mt-1 truncate text-xs leading-5 text-gray-500">{{ $userOrg->user->email }}</p>
                        <p class="mt-1 truncate text-xs leading-5 text-gray-500">{{ $userOrg->user->phone }}</p>
                    </div>
                </div>

                <div class="hidden sm:flex sm:flex-col sm:items-end">
                    <p class="text-sm leading-6 text-gray-900">{{ $userOrg->user->department }}</p>
                    <p class="mt-1 text-xs leading-5 text-gray-500">{{ $userOrg->user->designation }}</p>
                </div>
            </a>
        </li>
        @endforeach
    </ul>
</dl>

{{ $userOrganizations->appends($currentQueryParams)->links() }}