<div class="max-w-6xl mx-auto space-y-8 text-white pb-12 mt-8 px-4">
    
    <div class="flex items-center gap-4 mb-8">
        <h1 class="text-3xl font-black uppercase tracking-tighter text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-cyan-400">
            {{ __('My Profile') }}
        </h1>
        <div class="h-1 flex-grow bg-slate-800 rounded-full"></div>
    </div>

    @if (session()->has('message'))
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-xl flex items-center justify-between mb-6">
            <span class="font-medium">{{ session('message') }}</span>
            <button onclick="this.parentElement.remove()" class="text-emerald-400/50 hover:text-emerald-400">✖</button>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
        
        <div class="md:col-span-5 space-y-6">
            <div class="bg-slate-900 border border-slate-800 rounded-[2.5rem] p-8 shadow-xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500/10 blur-3xl rounded-full -mr-10 -mt-10"></div>

                <div class="flex items-center gap-6 mb-8 relative z-10">
                    <div class="w-20 h-20 rounded-full bg-slate-950 border-4 border-slate-800 flex items-center justify-center text-slate-300 font-black text-2xl uppercase shadow-inner">
                        {{ substr($user->name, 0, 2) }}
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-white">{{ $user->name }}</h2>
                        <span class="text-sm font-bold text-slate-500 uppercase tracking-widest">{{ $user->role === 'admin' ? '👑 Admin' : '👤 Trader' }}</span>
                    </div>
                </div>

                <div class="space-y-4 relative z-10">
                    <div class="bg-slate-950/50 p-4 rounded-2xl border border-slate-800">
                        <span class="text-xs font-black text-slate-500 uppercase tracking-widest block mb-1">{{ __('Email Address') }}</span>
                        <span class="text-slate-200 font-medium">{{ $user->email }}</span>
                    </div>
                    
                    <div class="bg-slate-950/50 p-4 rounded-2xl border border-slate-800">
                        <span class="text-xs font-black text-slate-500 uppercase tracking-widest block mb-1">{{ __('Current Wallet Balance') }}</span>
                        <span class="text-2xl font-black text-emerald-400">{{ number_format($user->balance / 100, 2) }} ¢</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="md:col-span-7 space-y-6">
            <div class="bg-slate-900 border border-slate-800 rounded-[2.5rem] p-8 shadow-xl">
                
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h3 class="text-xl font-black text-white flex items-center gap-2">
                            <span class="text-indigo-400">⚡</span> {{ __('Developer API') }}
                        </h3>
                        <p class="text-sm text-slate-400 mt-1 leading-relaxed">
                            {{ __('Generate an API key to allow your trading bots to place bets automatically. Keep this key secret.') }}
                        </p>
                    </div>
                </div>

                @if($plainTextToken)
                    <div class="bg-emerald-500/10 border border-emerald-500/30 p-6 rounded-2xl mb-8 animate-in fade-in zoom-in duration-300">
                        <h4 class="text-emerald-400 font-black uppercase tracking-widest text-xs mb-2 flex items-center gap-2">
                            <span>⚠️</span> {{ __('Copy your key now!') }}
                        </h4>
                        <p class="text-sm text-slate-300 mb-3">{{ __('For your security, it will not be shown again.') }}</p>
                        
                        <div class="relative bg-slate-950 rounded-xl p-4 border border-slate-800 font-mono text-emerald-300 text-sm break-all select-all selection:bg-emerald-500/30">
                            {{ $plainTextToken }}
                        </div>
                    </div>
                @endif

                <div class="mt-8">
                    @if($tokens->count() > 0)
                        <div class="flex items-center justify-between p-4 bg-slate-950 rounded-2xl border border-indigo-500/20">
                            <div class="flex items-center gap-3">
                                <span class="relative flex h-3 w-3">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                                </span>
                                <span class="font-bold text-slate-300 text-sm">{{ __('Active Token Found') }}</span>
                            </div>
                            <button wire:click="revokeTokens" class="text-xs font-black text-rose-400 hover:text-rose-300 uppercase tracking-widest transition-colors px-3 py-1.5 bg-rose-500/10 rounded-lg">
                                {{ __('Revoke Key') }}
                            </button>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center p-8 bg-slate-950/50 rounded-2xl border border-slate-800 border-dashed text-center">
                            <span class="text-3xl mb-3 opacity-50 text-slate-400">🔌</span>
                            <p class="text-slate-500 text-sm font-medium">{{ __('No active API keys found.') }}</p>
                        </div>
                    @endif
                </div>

                <div class="mt-8 border-t border-slate-800 pt-8">
                    <button wire:click="generateToken" wire:loading.attr="disabled" class="w-full bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-400 text-white py-4 rounded-2xl font-black tracking-widest shadow-lg shadow-indigo-500/20 transition-all active:scale-95 flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="generateToken">{{ __('GENERATE NEW API KEY') }}</span>
                        <span wire:loading wire:target="generateToken">{{ __('GENERATING...') }}</span>
                    </button>
                    <p class="text-[10px] text-slate-600 text-center uppercase tracking-widest font-black mt-3">
                        {{ __('Generating a new key will invalidate any existing keys.') }}
                    </p>
                </div>

            </div>
        </div>

    </div>
</div>