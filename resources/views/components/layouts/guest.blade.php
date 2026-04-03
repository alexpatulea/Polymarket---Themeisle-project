<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
</head>
<body class="bg-slate-950 text-slate-200 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">
                Poly<span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-cyan-400">Themeisle</span>
            </h1>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-slate-900 border border-slate-800 shadow-md overflow-hidden sm:rounded-2xl">
            {{ $slot }}
        </div>
    </div>
    @livewireScripts
</body>
</html>