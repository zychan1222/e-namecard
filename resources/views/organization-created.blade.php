<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }

        .text-center {
            text-align: center;
        }

        .card {
            display: block;
            padding: 20px;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            text-align: center;
            transition: transform 0.2s;
            margin: 10px;
            text-decoration: none; 
        }

        .card:hover {
            transform: scale(1.05);
        }

        h1, h2 {
            color: #333;
        }

        p {
            color: #666;
        }

        img {
            max-height: 80px;
        }

        @media (min-width: 640px) {
            .grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }
        }

        @media (min-width: 1024px) {
            .grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
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
    <!-- Flash messages -->
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

    <div class="container">
        <div class="text-center mb-8">
            <img class="mx-auto" src="{{ asset('storage/logo-no-bg.png') }}" alt="Your Company">
            <h1 class="mt-4 text-3xl font-bold">Welcome to E-Namecard</h1>
            <p class="mt-2">Your digital business card solution.</p>
        </div>

        <div class="grid">
            <!-- Log in -->
            <a href="{{ route('login') }}" class="card">
                <h2>Log in</h2>
                <p>Access your account by logging in with your credentials.</p>
            </a>

            <!-- Admin Log in -->
            <a href="{{ route('admin.login.form') }}" class="card">
                <h2>Admin Log in</h2>
                <p>Admins can log in to manage the system and users.</p>
            </a>
        </div>
    </div>
</body>
</html>