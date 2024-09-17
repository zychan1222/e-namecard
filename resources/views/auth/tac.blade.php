<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User TAC Login</title>
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
        .card {
            display: block;
            padding: 40px; 
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            transition: transform 0.2s;
            margin: 20px auto; 
            max-width: 500px;
            width: 90%; 
        }  
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full">
                <!-- Flash messages -->
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
    <div class="card">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <img class="mx-auto h-20 w-auto" src="{{ asset('storage/logo-no-bg.png') }}" alt="Your Company">
            <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">User TAC Login</h2>
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
            <form class="space-y-6" method="POST" action="{{ route('login.tac') }}">
            @csrf
                    <!-- Hidden email field -->
                    <input type="hidden" name="email" value="{{ $email }}">
                    <div>
                        <label for="tac_code" class="block text-sm font-medium leading-6 text-gray-900">Enter TAC Code</label>
                        <div class="mt-2">
                            <input id="tac_code" name="tac_code" type="text" required class="block w-full rounded-md px-3 border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                    </div>
                    @if ($errors->any())
                        <div class="mb-4">
                            <ul class="list-disc list-inside text-sm text-red-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                <div>
                    <button type="submit" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Login</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
