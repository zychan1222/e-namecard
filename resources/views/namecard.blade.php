<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Namecard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js"></script>
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
<body class="bg-gray-100">
    @include('partials.header')
    <header class="bg-white shadow">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">{{ $pageTitle }}</h1>
        </div>
    </header>

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

    <div class="flex justify-center mt-10">
        <div id="capture" class="bg-white shadow-lg rounded-lg w-full sm:w-96 h-full sm:h-48 flex">
            <!-- Left side (60%) -->
            <div class="w-3/5 p-4">
                <h2 class="text-xl font-bold">{{ $userOrg->user->name }}</h2>
                <p class="mt-2">{{ $user->designation }}</p>
                <p class="mt-1">{{ $user->department }}</p>
                <p class="mt-1">{{ $userOrg->organization->name }}</p>
                <p class="mt-1">{{ $userOrg->user->phone }}</p>
            </div>

            <!-- Right side (40%) -->
            <div class="w-2/5 p-4 flex flex-col items-center justify-center">
                <img id="profile-pic" src="{{ $userOrg->user->profile_pic ? asset('storage/profile_pics/' . $userOrg->user->profile_pic) : asset('storage/default-user.jpg') }}" alt="Profile Picture" class="w-16 h-16 rounded-full mb-2 border-2 border-gray-300">
                <div id="qr-code">{!! $qrCode !!}</div>
            </div>
        </div>
    </div>

    <div class="flex justify-center mt-6">
        <button onclick="captureDiv()" class="flex items-center justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            Capture and Save Image
        </button>
    </div>

    <div class="flex justify-center mt-6 space-x-4">
        <a href="whatsapp://send?text={{ urlencode(route('download.vcard.page', ['userOrg' => $userOrg->id])) }}" class="flex items-center justify-center rounded-md bg-green-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600">
            Share via WhatsApp
        </a>

        <a href="tg://msg_url?url={{ urlencode(route('download.vcard.page', ['userOrg' => $userOrg->id])) }}" class="flex items-center justify-center rounded-md bg-blue-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
            Share via Telegram
        </a>

        <button onclick="sendViaMessenger()" class="flex items-center justify-center rounded-md bg-blue-800 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-800">
            Share via Messenger
        </button>
    </div>

    <script>
        function captureDiv() {
            console.log("Button clicked");

            var element = document.getElementById('capture');
            var images = element.getElementsByTagName('img');
            var loadedImages = 0;

            function allImagesLoaded() {
                console.log("All images loaded");

                html2canvas(element).then(function(canvas) {
                    var imgData = canvas.toDataURL("image/png");
                    var link = document.createElement('a');
                    link.href = imgData;
                    link.download = 'captured-image.png';
                    link.click();
                });
            }

            for (var i = 0; i < images.length; i++) {
                images[i].onload = function() {
                    loadedImages++;
                    if (loadedImages === images.length) {
                        allImagesLoaded();
                    }
                };
                if (images[i].complete) {
                    images[i].onload();
                }
            }
        }

        window.fbAsyncInit = function() {
            FB.init({
                appId      : '1013877700343527',
                xfbml      : true,
                version    : 'v10.0'
            });
        };

        function sendViaMessenger() {
            var messageData = {
                link: '{{ route('download.vcard.page', ['userOrg' => $userOrg->id]) }}'
            };

            console.log('Message Data:', messageData);

            FB.ui({
                method: 'send',
                display: 'popup', 
                link: messageData.link,
            });
        }
    </script>
</body>
</html>
