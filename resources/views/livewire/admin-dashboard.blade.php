<div class="space-y-8 text-white">
    {{-- HEADER PAGINĂ ADMIN --}}
    <div class="flex items-center gap-4">
        <h1 class="text-3xl font-black uppercase tracking-tighter text-transparent bg-clip-text bg-gradient-to-r from-rose-400 to-orange-400">
            {{ __('Admin Command Center') }}
        </h1>
        <div class="h-1 flex-grow bg-slate-800 rounded-full"></div>
    </div>
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" 
             class="bg-emerald-500/20 border border-emerald-500/50 text-emerald-400 px-6 py-4 rounded-xl font-bold flex justify-between items-center">
            <span>{{ session('message') }}</span>
            <button @click="show = false" class="text-emerald-400 hover:text-white">&times;</button>
        </div>
    @endif

    <section>
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-slate-300 flex items-center gap-2">
                <span class="w-2 h-2 bg-rose-500 rounded-full animate-pulse"></span>
                {{ __('Active Markets Pending Resolution') }}
            </h2>
        </div>

        <div class="grid gap-6">
            @forelse($markets as $market)
                @php
                    // Logica de extragere titlu (identică cu ce am făcut la UserBets)
                    $rawTitle = $market->title;
                    $titleArray = is_array($rawTitle) ? $rawTitle : json_decode($rawTitle, true);
                    $marketTitle = is_array($titleArray) ? ($titleArray[app()->getLocale()] ?? $titleArray['en'] ?? 'N/A') : $rawTitle;
                @endphp

                <div class="bg-slate-900 border border-slate-800 p-6 rounded-[1.5rem] flex flex-col lg:flex-row lg:items-center justify-between gap-6 transition-all hover:border-slate-700">
                    
                    {{-- INFO PIAȚĂ --}}
                    <div class="space-y-2 flex-1">
                        <div class="flex items-center gap-3">
                            <span class="px-2 py-1 text-[10px] font-black uppercase tracking-widest bg-slate-800 text-slate-400 rounded-md">ID: {{ $market->id }}</span>
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">{{ __('Total Pool') }}: {{ number_format($market->total_pool / 100, 2) }} ¢</span>
                        </div>
                        <h3 class="text-xl font-bold text-white">{{ $marketTitle }}</h3>
                    </div>

                    {{-- BUTOANE DE ACȚIUNE --}}
                    <div class="flex flex-wrap items-center gap-3">
                        
                        {{-- Generăm un buton de WIN pentru fiecare opțiune (YES / NO) --}}
                        @foreach($market->outcomes as $outcome)
                            @php
                                $rawName = $outcome->name;
                                $nameArray = is_array($rawName) ? $rawName : json_decode($rawName, true);
                                $outcomeName = is_array($nameArray) ? ($nameArray[app()->getLocale()] ?? $nameArray['en'] ?? 'N/A') : $rawName;
                            @endphp
                            
                            <button 
                                wire:click="resolveMarket({{ $market->id }}, {{ $outcome->id }})"
                                wire:confirm="{{ __('Are you sure you want to resolve this market as WINNER for: ') }} {{ $outcomeName }}? {{ __('This will distribute funds and cannot be undone.') }}"
                                class="bg-emerald-500/10 hover:bg-emerald-500 hover:text-white text-emerald-400 border border-emerald-500/20 px-4 py-2 rounded-xl text-sm font-black transition-colors shadow-lg shadow-emerald-500/5"
                            >
                                {{ __('Winner') }}: {{ $outcomeName }}
                            </button>
                        @endforeach

                        <div class="w-px h-8 bg-slate-800 hidden lg:block mx-2"></div>

                        {{-- Buton de Refund (Arhivare) --}}
                        <button 
                            wire:click="archiveMarket({{ $market->id }})"
                            wire:confirm="{{ __('Are you sure you want to ARCHIVE this market? All users will get their original stakes refunded.') }}"
                            class="bg-rose-500/10 hover:bg-rose-500 hover:text-white text-rose-400 border border-rose-500/20 px-4 py-2 rounded-xl text-sm font-black transition-colors"
                        >
                            {{ __('Archive (Refund)') }}
                        </button>
                    </div>
                </div>
            @empty
                <div class="bg-slate-900/50 border border-slate-800 border-dashed p-12 rounded-[1.5rem] text-center">
                    <h3 class="text-lg font-bold text-slate-300 mb-1">{{ __('All caught up!') }}</h3>
                    <p class="text-slate-500 font-medium">{{ __('No active markets waiting for resolution.') }}</p>
                </div>
            @endforelse
        </div>
        
        <div class="mt-8">
            {{ $markets->links('components.custom-pagination') }}
        </div>
    </section>
</div>