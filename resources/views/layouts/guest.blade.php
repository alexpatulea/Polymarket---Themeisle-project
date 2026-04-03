<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
    <style>
    /* Forțăm input-urile să aibă fundal închis și text deschis */
    input { 
        background-color: #1e293b !important; /* slate-800 */
        color: #f1f5f9 !important; /* slate-100 */
        border-color: #334155 !important; /* slate-700 */
    }
    label { 
        color: #94a3b8 !important; /* slate-400 */
    }
    /* Stil pentru butonul de Login (indigo) */
    button[type="submit"] {
        background-color: #4f46e5 !important;
    }
</style>
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