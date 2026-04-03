<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Prediction Market - PolyThemeisle') }}</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><defs><linearGradient id='g' x1='0%' y1='0%' x2='100%' y2='100%'><stop offset='0%' stop-color='%236366f1'/><stop offset='100%' stop-color='%2322d3ee'/></linearGradient></defs><path d='M50 5 L10 25 L10 75 L50 95 L90 75 L90 25 Z' fill='url(%23g)' opacity='0.2'/><path d='M30 75 L30 55 Q 30 40 45 40 L55 40 Q 70 40 70 55 L70 60 Q 70 75 55 75 L30 75 Z' stroke='url(%23g)' stroke-width='6' fill='none'/><path d='M70 60 L85 40' stroke='url(%23g)' stroke-width='6' stroke-linecap='round'/><path d='M85 40 L75 40 M85 40 L85 50' stroke='url(%23g)' stroke-width='6' stroke-linecap='round' stroke-linejoin='round'/></svg>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
    @livewireStyles
</head>
<body class="bg-slate-950 text-slate-200 antialiased selection:bg-indigo-500/30">
    <div x-data="{ showSplash: !sessionStorage.getItem('splashShown') }"
         x-init="if (showSplash) { setTimeout(() => { showSplash = false; sessionStorage.setItem('splashShown', 'true'); }, 2500); }"
         x-show="showSplash"
         x-transition:leave="transition ease-in duration-500"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;"
         class="fixed inset-0 z-[999999] bg-slate-950 flex flex-col items-center justify-center"
    >
        <style>
            /* Animație rotire 3D (720 grade = 2 rotații) */
            @keyframes spin-3d {
                0% { transform: perspective(1000px) rotateY(0deg); }
                100% { transform: perspective(1000px) rotateY(720deg); }
            }
            .animate-spin-3d {
                animation: spin-3d 1.5s cubic-bezier(0.65, 0, 0.35, 1) forwards;
            }

            /* Animație grafic de trading (lumanari care urca si coboara) */
            @keyframes chart-bounce {
                0%, 100% { height: 20%; }
                50% { height: 100%; }
            }
            .chart-bar {
                animation: chart-bounce 1s ease-in-out infinite;
                transform-origin: bottom;
            }
            /* Întârzieri pentru a face graficul să pară natural */
            .delay-1 { animation-delay: 0.1s; }
            .delay-2 { animation-delay: 0.3s; }
            .delay-3 { animation-delay: 0.15s; }
            .delay-4 { animation-delay: 0.4s; }
            .delay-5 { animation-delay: 0.2s; }
        </style>

        <div class="animate-spin-3d mb-12 relative">
            <div class="absolute inset-0 bg-indigo-500 blur-[30px] opacity-30 rounded-full"></div>
            
            <svg class="w-28 h-28 text-indigo-400 relative z-10 drop-shadow-[0_0_15px_rgba(99,102,241,0.5)]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 2L3 7l9 5 9-5-9-5z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 7v10l-9 5-9-5V7" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 22V12" />
            </svg>
        </div>

        <div class="flex items-end justify-center gap-2 h-16 w-32 border-b-2 border-slate-800 pb-1">
            <div class="w-4 bg-emerald-500 rounded-sm chart-bar delay-1 shadow-[0_0_10px_rgba(16,185,129,0.3)]"></div>
            <div class="w-4 bg-emerald-400 rounded-sm chart-bar delay-2 shadow-[0_0_10px_rgba(52,211,153,0.3)]"></div>
            <div class="w-4 bg-rose-500 rounded-sm chart-bar delay-3 shadow-[0_0_10px_rgba(244,63,94,0.3)]"></div>
            <div class="w-4 bg-indigo-400 rounded-sm chart-bar delay-4 shadow-[0_0_10px_rgba(129,140,248,0.3)]"></div>
            <div class="w-4 bg-emerald-500 rounded-sm chart-bar delay-5 shadow-[0_0_10px_rgba(16,185,129,0.3)]"></div>
        </div>
        
        <div class="mt-8 text-slate-500 font-black tracking-[0.4em] text-xs uppercase animate-pulse">
            {{ __('Loading Market Data') }}...
        </div>
    </div>
    
    <header class="bg-slate-900/80 backdrop-blur-md border-b border-slate-800 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            
            <div class="grid grid-cols-3 items-center">
                
                <div class="flex justify-start">
                    <a href="{{ route('dashboard') }}" class="group transition-transform hover:scale-105">
                        <h1 class="text-2xl font-bold tracking-tight">
                            Poly<span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-cyan-400">Themeisle</span>
                        </h1>
                    </a>
                </div>

                <div class="flex flex-col items-center gap-1.5">
                    <div x-data="{ balance: {{ auth()->user()->balance ?? 0 }} }"
                        @balance-updated.window="balance = $event.detail.newBalance"
                        class="flex items-center gap-2 bg-slate-800 border border-slate-700 rounded-full px-4 py-1.5 shadow-inner">
                        
                        <div class="w-2 h-2 rounded-full {{ auth()->check() ? 'bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.8)]' : 'bg-slate-600' }}"></div>
                        
                        <span class="text-sm font-medium text-slate-200">
                            {{ __('Wallet:') }} 
                            <span x-text="(balance / 100).toFixed(2)">
                                {{ number_format((auth()->user()->balance ?? 0) / 100, 2) }}
                            </span> ¢
                        </span>
                        
                    </div>
                    
                    <div class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest">
                        <a href="{{ route('lang.switch', 'en') }}" 
                           class="{{ app()->getLocale() === 'en' ? 'text-indigo-400' : 'text-slate-500 hover:text-indigo-300' }} transition-colors">
                            EN
                        </a>
                        <span class="text-slate-800">/</span>
                        <a href="{{ route('lang.switch', 'ro') }}" 
                           class="{{ app()->getLocale() === 'ro' ? 'text-indigo-400' : 'text-slate-500 hover:text-indigo-300' }} transition-colors">
                            RO
                        </a>
                    </div>
                </div>

                <div class="flex justify-end items-center gap-4">
                    @auth
                        <div x-data="{ open: false }" 
                             @mouseenter="open = true" 
                             @mouseleave="open = false" 
                             class="relative py-2">
                            
                            <button class="flex items-center group focus:outline-none">
                                <div class="w-10 h-10 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center overflow-hidden transition-all group-hover:border-indigo-500 group-hover:shadow-[0_0_15px_rgba(99,102,241,0.2)] shadow-lg">
                                    <svg class="w-6 h-6 text-slate-400 group-hover:text-indigo-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            </button>

                            <div x-show="open" 
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                 class="absolute right-0 mt-1 w-56 bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl z-50 py-2 overflow-hidden">
                                
                                <div class="px-4 py-3 border-b border-slate-800/50 mb-1 bg-slate-800/30">
                                    <p class="text-[10px] text-slate-500 uppercase font-black tracking-widest mb-0.5">{{ __('User Profile') }}</p>
                                    <p class="text-sm font-bold text-white truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-[11px] text-slate-400 truncate">{{ auth()->user()->email }}</p>
                                </div>

                                <a href="{{ route('user.bets') }}" class="group flex items-center px-4 py-2 text-sm text-slate-400 hover:bg-indigo-600/10 hover:text-white transition-colors">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-700 mr-2 group-hover:bg-indigo-500"></span>
                                    {{ __('My Bets') }}
                                </a>
                                @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="group flex items-center px-4 py-2 text-sm text-rose-400 hover:bg-rose-600/10 hover:text-rose-300 transition-colors">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-700 mr-2 group-hover:bg-rose-500"></span>
                                    {{ __('Admin Panel') }}
                                </a>
                                @endif
                                <a href="{{ route('leaderboard') }}" class="group flex items-center px-4 py-2 text-sm text-slate-400 hover:bg-indigo-600/10 hover:text-white transition-colors">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-700 mr-2 group-hover:bg-indigo-500"></span>
                                    {{ __('Leaderboard') }}
                                </a>
                                <a href="{{ route('profile') }}" class="group flex items-center px-4 py-2 text-sm text-slate-400 hover:bg-indigo-600/10 hover:text-white transition-colors">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-700 mr-2 group-hover:bg-indigo-500"></span>
                                    {{ __('Profile') }}
                                </a>
                                <hr class="border-slate-800 my-1">

                                <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="group flex items-center w-full px-4 py-2 text-sm text-slate-400 hover:bg-red-500/10 hover:text-red-400 transition-all duration-200">
                                            <svg class="w-4 h-4 mr-2 text-slate-500 group-hover:text-red-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                            <span class="font-medium">{{ __('Logout') }}</span>
                                        </button>
                                </form>
                            </div>
                        </div>
                    @endauth

                    @guest
                        <div class="flex items-center gap-3">
                            <a href="{{ route('login') }}" class="text-sm font-bold text-slate-400 hover:text-white transition-colors px-3 py-2">
                                {{ __('Login') }}
                            </a>
                            <a href="{{ route('register') }}" class="bg-indigo-600 hover:bg-indigo-500 text-white px-5 py-2.5 rounded-full text-sm font-black transition-all shadow-lg shadow-indigo-500/20 active:scale-95">
                                {{ __('Join Now') }}
                            </a>
                        </div>
                    @endguest
                </div>

            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>