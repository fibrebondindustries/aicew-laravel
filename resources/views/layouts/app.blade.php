<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AICEW - AI Candidate Evaluation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900 font-sans">

    <header class="bg-white shadow py-4">
        <div class="max-w-6xl mx-auto px-4 flex justify-between items-center">
              <!-- Logo and Company Name -->
        <div class="flex items-center space-x-3">
            <img src="{{ asset('images/logo1.png') }}" alt="Logo" class="h-10 w-auto">
        </div>
        </div>
        
    </header>

    <main class="max-w-6xl mx-auto px-4 py-12">
        @yield('content')
    </main>

    <footer class="text-center py-6 text-xs text-gray-500">
        &copy; {{ date('Y') }} Fibre Bond Industries. All rights reserved.
    </footer>

</body>
</html>
