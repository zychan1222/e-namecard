<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Organization</title>
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

        .organization-button {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            background-color: #ffffff;
            transition: background-color 0.3s, border-color 0.3s;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            font-weight: 600;
            color: #4a5568;
            text-align: left;
        }

        .organization-button:hover {
            background-color: #f7fafc;
            border-color: #cbd5e0;
        }

        .organization-logo {
            flex: 0 0 60px;
            height: 60px;
            border-radius: 5px;
            overflow: hidden;
            margin-right: 12px;
        }

        .organization-logo img {
            width: 100%;
            height: auto;
            object-fit: cover;
        }
    </style>
</head>
<body>
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
    <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
        <div class="card mx-auto sm:w-full sm:max-w-sm p-8 bg-white rounded-lg shadow-md">
            <div class="sm:mx-auto sm:w-full sm:max-w-sm">
                <img class="mx-auto h-20 w-auto mb-4" src="{{ asset('storage/logo-no-bg.png') }}" alt="Your Company">
                <h2 class="text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">Select Your Organization</h2>
            </div>

            <div class="mt-10 sm:mx-auto sm:w-full">
                @if (session('employeeEntries') && count(session('employeeEntries')) > 0)
                    <form action="{{ route('select.organization.post') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            @foreach (session('employeeEntries') as $employee)
                                <button type="submit" name="employee_id" value="{{ $employee->id }}" class="organization-button mb-2">
                                    <div class="organization-logo">
                                        @if ($employee->organization)
                                            <img src="{{ $employee->organization->logo ? asset('storage/logo/' . $employee->organization->logo) : asset('storage/default-logo.jpg') }}" alt="Organization Logo">
                                        @else
                                            <img src="{{ asset('storage/default-logo.jpg') }}" alt="Default Organization Logo">
                                        @endif
                                    </div>
                                    <div>
                                        {{ $employee->organization ? $employee->organization->name : 'No Organization' }}
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </form>
                @else
                    <div class="alert alert-warning" role="alert">
                        No employee entries available.
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>