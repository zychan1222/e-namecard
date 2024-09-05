<dl class="divide-y divide-gray-100">
    <ul role="list" class="divide-y divide-gray-100">
        @foreach($employees as $emp)
        <li>
            <a href="{{ route('admin.employee.profile', $emp->id) }}"
                class="flex justify-between gap-x-6 py-5 hover:bg-gray-50">
                <div class="flex min-w-0 gap-x-4">
                    <img class="h-12 w-12 flex-none rounded-full bg-gray-50"
                        src="{{ $emp->profile_pic ? asset('storage/profile_pics/' . $emp->profile_pic) : asset('storage/default-user.jpg') }}"
                        alt="{{ $emp->name }}">

                    <div class="min-w-0 flex-auto">
                        <div class="flex items-center">
                            <p class="text-sm font-semibold leading-6 text-gray-900">{{ $emp->name }}</p>
                            
                            {{-- Check if the employee is the owner of the organization --}}
                            @if($emp->organization->owner_id === $emp->id)
                                <span class="ml-2 inline-flex items-center rounded bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">
                                    Owner
                                </span>
                            @else
                                {{-- Check if the employee is an administrator --}}
                                @if(\App\Models\Admin::where('employee_id', $emp->id)->exists())
                                    <span class="ml-2 inline-flex items-center rounded bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800">
                                        Administrator
                                    </span>
                                @endif
                            @endif
                        </div>
                        <p class="mt-1 truncate text-xs leading-5 text-gray-500">{{ $emp->user->email }}</p>
                        <p class="mt-1 truncate text-xs leading-5 text-gray-500">{{ $emp->phone }}</p>
                    </div>
                </div>

                <div class="hidden sm:flex sm:flex-col sm:items-end">
                    <p class="text-sm leading-6 text-gray-900">{{ $emp->department }}</p>
                    <p class="mt-1 text-xs leading-5 text-gray-500">{{ $emp->designation }}</p>
                </div>
            </a>
        </li>
        @endforeach
    </ul>
</dl>

{{ $employees->links() }}
