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
            <h1 class="text-xl font-bold text-indigo-600">Fibre Bond Industries</h1>
            <nav class="space-x-4 text-sm text-gray-700">
                <!-- <a href="/" class="hover:text-indigo-600">Home</a>
                <a href="/apply" class="hover:text-indigo-600">Apply</a>
                <a href="/admin/login" class="hover:text-indigo-600">Admin</a> -->
            </nav>
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
