<div class="space-y-8">
    
    @if (session()->has('message'))
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-xl flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-xl">✅</span>
                <span class="font-medium">{{ session('message') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-400/50 hover:text-emerald-400">✖</button>
        </div>
    @endif

    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 flex flex-col sm:flex-row justify-between items-center gap-4 shadow-sm">
        
        <div class="flex flex-col sm:flex-row items-center gap-4 w-full sm:w-auto">
            <div class="flex items-center space-x-3 w-full sm:w-auto">
                <span class="text-sm font-medium text-slate-400 uppercase tracking-wider">{{ __('Status:') }}</span>
                <select wire:model.live="statusFilter" class="bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:w-auto py-2 px-3 transition-colors">
                    <option value="active">🟢 {{ __('Active Markets') }}</option>
                    <option value="resolved">🏁 {{ __('Resolved') }}</option>
                    <option value="archived">📦 {{ __('Archived') }}</option>
                </select>
            </div>

            <div class="flex items-center space-x-3 w-full sm:w-auto">
                <span class="text-sm font-medium text-slate-400 uppercase tracking-wider">{{ __('Sort:') }}</span>
                <select wire:model.live="sortField" class="bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:w-auto py-2 px-3 transition-colors">
                    <option value="created_at"> {{ __('Newest First') }}</option>
                    <option value="total_pool"> {{ __('Highest Volume') }}</option>
                </select>
            </div>
        </div>

        @auth
            <button wire:click="openCreateModal" class="w-full sm:w-auto bg-gradient-to-r from-emerald-500 to-emerald-400 hover:from-emerald-400 hover:to-emerald-300 text-slate-950 px-6 py-2 rounded-xl text-sm font-black uppercase tracking-widest transition-all shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/40 shrink-0">
                + {{ __('Create Market') }}
            </button>
        @endauth

        @guest
            <div x-data="{ showGuestError: false }" class="relative w-full sm:w-auto">
                <button @click="showGuestError = true; setTimeout(() => showGuestError = false, 4000)" class="w-full sm:w-auto bg-gradient-to-r from-emerald-500 to-emerald-400 hover:from-emerald-400 hover:to-emerald-300 text-slate-950 px-6 py-2 rounded-xl text-sm font-black uppercase tracking-widest transition-all shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/40 shrink-0 opacity-90">
                    + {{ __('Create Market') }}
                </button>
                
                <div x-show="showGuestError" x-transition style="display: none;" class="absolute right-0 sm:right-auto sm:left-1/2 sm:-translate-x-1/2 top-full mt-3 w-64 bg-slate-900 border border-rose-500/30 text-slate-300 p-4 rounded-xl shadow-2xl z-50 text-sm text-center">
                    <span class="text-rose-400 text-2xl block mb-2">🔒</span>
                    <p class="font-medium">{{ __('You must be logged in to create a new market.') }}</p>
                    <a href="{{ route('login') }}" class="inline-block mt-3 bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-1.5 rounded-lg text-xs font-bold transition-colors">{{ __('Log in here') }}</a>
                </div>
            </div>
        @endguest
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($markets as $market)
            <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden hover:border-slate-700 hover:shadow-[0_0_30px_-5px_rgba(79,70,229,0.15)] transition-all duration-300 flex flex-col group">
                
                <div class="p-6 flex flex-col flex-grow">
                    <div class="flex justify-between items-start gap-4 mb-6">
                        <h3 class="text-lg font-semibold text-slate-100 leading-snug group-hover:text-indigo-300 transition-colors line-clamp-3" title="{{ $market->title }}">
                            {{ $market->title }}
                        </h3>
                        <span class="shrink-0 inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border
                            {{ $market->status === 'active' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-slate-800 text-slate-400 border-slate-700' }}">
                            {{ __(ucfirst($market->status)) }}
                        </span>
                    </div>

                    @php $safeTotal = $market->total_pool > 0 ? $market->total_pool : 1; @endphp

                    <div class="space-y-4 mt-auto">
                        @foreach ($market->outcomes as $outcome)
                            @php $percentage = ($outcome->pool / $safeTotal) * 100; @endphp
                            <div class="relative">
                                <div class="flex justify-between text-sm font-medium mb-1.5">
                                    <span class="text-slate-300">{{ __($outcome->name) }}</span>
                                    <span class="text-slate-400">{{ number_format($percentage, 0) }}%</span>
                                </div>
                                <div class="w-full bg-slate-800 rounded-full h-2 overflow-hidden">
                                    <div class="bg-gradient-to-r from-indigo-500 to-cyan-400 h-full rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-slate-950/50 border-t border-slate-800 p-4 flex justify-between items-center shrink-0">
                    <div class="flex flex-col">
                        <span class="text-xs text-slate-500 uppercase tracking-wider font-semibold">{{ __('Pool') }}</span>
                        <span class="text-sm font-medium text-slate-200">{{ number_format($market->total_pool / 100, 2) }} ¢</span>
                    </div>
                    
                    <button wire:click="openTradeModal({{ $market->id }})" class="bg-indigo-600 hover:bg-indigo-500 text-white px-5 py-2 rounded-xl text-sm font-semibold transition-all shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/40">
                        {{ __('Trade') }}
                    </button>
                </div>
                
            </div>
        @empty
            <div class="col-span-full flex flex-col items-center justify-center py-16 px-4 bg-slate-900 border border-slate-800 rounded-2xl border-dashed">
                <span class="text-4xl mb-4">🌌</span>
                <h3 class="text-lg font-medium text-slate-200">{{ __('No markets found') }}</h3>
                <p class="text-slate-500 mt-1">{{ __('Change your filters or wait for new predictions.') }}</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $markets->links('components.custom-pagination') }}
    </div>

    @if($tradeModalOpen)
        <template x-teleport="body">
            
            <div x-data="{ amount: $wire.entangle('betAmount'), selected: $wire.entangle('selectedOutcomeId') }"
                 class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-950/90 backdrop-blur-md">
                 
                <div @click.away="$wire.closeTradeModal()"
                     class="relative w-full max-w-4xl bg-slate-900 border border-slate-800 shadow-2xl rounded-[2.5rem] overflow-hidden flex flex-col md:flex-row animate-in zoom-in-95 duration-200">
                    
                    <button wire:click="closeTradeModal" class="absolute top-5 right-5 text-slate-500 hover:text-white transition-colors z-50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>

                    @if($selectedMarket)
                        
                        <div class="w-full md:w-[45%] p-8 bg-slate-950/50 border-b md:border-b-0 md:border-r border-slate-800 flex flex-col justify-center relative">
                            <h3 class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-8 text-center">{{ __('Current Odds') }}</h3>
                            
                            @php
                                $modalSafeTotal = $selectedMarket->total_pool > 0 ? $selectedMarket->total_pool : 0;
                                $bgGradients = ['from-indigo-500 to-indigo-400', 'from-slate-500 to-slate-400']; 
                                $activeBorders = ['indigo-500', 'slate-400']; // Numele culorilor pentru Tailwind
                                $activeOddsColors = ['text-indigo-400', 'text-slate-300'];
                            @endphp

                            <div class="space-y-6 relative">
                                @foreach($selectedMarket->outcomes as $index => $outcome)
                                    @php
                                        $rawName = $outcome->name;
                                        $nameArray = is_array($rawName) ? $rawName : json_decode($rawName, true);
                                        $label = is_array($nameArray) ? ($nameArray[app()->getLocale()] ?? $nameArray['en'] ?? 'N/A') : $rawName;
                                        
                                        $odds = $outcome->pool > 0 ? ($modalSafeTotal / $outcome->pool) : 0;
                                        $percent = $modalSafeTotal > 0 ? round(($outcome->pool / $modalSafeTotal) * 100) : 0;
                                        $formattedOdds = $odds > 0 ? 'x' . number_format($odds, 2) : 'x0.00';
                                        
                                        $gradient = $bgGradients[$index % count($bgGradients)];
                                        $activeBorderColor = $activeBorders[$index % count($activeBorders)];
                                        $oddsTextColor = $activeOddsColors[$index % count($activeOddsColors)];
                                    @endphp

                                    <div class="relative p-6 rounded-[1.5rem] border-2 transition-all duration-200"
                                         :class="selected == {{ $outcome->id }} 
                                            ? 'border-{{ $activeBorderColor }} bg-slate-800/80 shadow-[0_0_20px_-5px_rgba(99,102,241,0.2)] scale-[1.02]' 
                                            : 'border-slate-800/50 bg-slate-900/30 opacity-60 hover:opacity-100'">
                                        
                                        <div class="flex justify-between items-end mb-4 relative z-10">
                                            <span class="text-2xl font-black uppercase tracking-wider transition-colors"
                                                  :class="selected == {{ $outcome->id }} ? 'text-white' : 'text-slate-500'">
                                                {{ $label }}
                                            </span>
                                            <span class="text-3xl font-black tracking-tighter transition-colors"
                                                  :class="selected == {{ $outcome->id }} ? '{{ $oddsTextColor }}' : 'text-slate-600'">
                                                {{ $formattedOdds }}
                                            </span>
                                        </div>
                                        
                                        <div class="w-full bg-slate-950 rounded-full h-3 overflow-hidden relative z-10 shadow-inner opacity-80"
                                             :class="selected == {{ $outcome->id }} ? 'opacity-100' : ''">
                                            <div class="bg-gradient-to-r {{ $gradient }} h-full rounded-full transition-all duration-1000 ease-out" style="width: {{ $percent }}%"></div>
                                        </div>
                                    </div>

                                    @if(!$loop->last)
                                        <div class="absolute left-1/2 -translate-x-1/2 top-1/2 -translate-y-1/2 z-20">
                                            <span class="bg-slate-900 text-slate-500 font-black italic text-xs px-3 py-1.5 rounded-full border border-slate-800 shadow-xl">VS</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            <div class="mt-12 text-center">
                                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">{{ __('Total Market Volume') }}</span>
                                <div class="text-2xl font-black text-white mt-1">{{ number_format($modalSafeTotal / 100, 2) }} ¢</div>
                            </div>
                        </div>

                        <div class="w-full md:w-[55%] p-8 flex flex-col justify-center">
                            
                            <div class="text-center mb-8 mt-2 pr-4">
                                <h2 class="text-2xl font-black text-white mb-2">{{ __('Make your Move') }}</h2>
                                @php
                                    $rawTitle = $selectedMarket->title;
                                    $titleArray = is_array($rawTitle) ? $rawTitle : json_decode($rawTitle, true);
                                    $marketTitle = is_array($titleArray) ? ($titleArray[app()->getLocale()] ?? $titleArray['en'] ?? 'N/A') : $rawTitle;
                                @endphp
                                <p class="text-slate-400 text-sm leading-relaxed">{{ $marketTitle }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-8">
                                @foreach($selectedMarket->outcomes as $outcome)
                                    <label class="relative cursor-pointer group">
                                        <input type="radio" name="outcome_selection" x-model="selected" value="{{ $outcome->id }}" class="peer sr-only">
                                        
                                        <div class="py-4 rounded-2xl border-2 border-slate-800 bg-slate-950 text-slate-500 font-black text-sm uppercase tracking-widest text-center transition-all peer-checked:border-indigo-500 peer-checked:text-white peer-checked:bg-indigo-600/10 group-hover:border-slate-700">
                                            @php
                                                $rawOutName = $outcome->name;
                                                $outNameArr = is_array($rawOutName) ? $rawOutName : json_decode($rawOutName, true);
                                                $outLabel = is_array($outNameArr) ? ($outNameArr[app()->getLocale()] ?? $outNameArr['en'] ?? 'N/A') : $rawOutName;
                                            @endphp
                                            {{ $outLabel }}
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            <div class="mb-8">
                                <div class="relative bg-slate-950 rounded-3xl border-2 border-slate-800 p-2 focus-within:border-indigo-500 transition-colors">
                                    <input type="number" x-model="amount" placeholder="0" min="1" class="w-full bg-transparent border-none text-white text-4xl font-black text-center focus:ring-0 focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none py-4" autofocus>
                                    <span class="absolute right-6 top-1/2 -translate-y-1/2 text-slate-600 font-bold text-xl">¢</span>
                                </div>
                                @error('betAmount') <p class="text-red-400 text-xs font-bold mt-3 text-center uppercase tracking-tighter">{{ $message }}</p> @enderror

                                <div class="flex justify-center gap-2 mt-4">
                                    <button type="button" @click="amount = (Number(amount) || 0) + 10" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-400 text-[10px] font-black hover:bg-slate-700 hover:text-white transition-all uppercase tracking-widest">+10</button>
                                    <button type="button" @click="amount = (Number(amount) || 0) + 50" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-400 text-[10px] font-black hover:bg-slate-700 hover:text-white transition-all uppercase tracking-widest">+50</button>
                                    <button type="button" @click="amount = (Number(amount) || 0) + 100" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-400 text-[10px] font-black hover:bg-slate-700 hover:text-white transition-all uppercase tracking-widest">+100</button>
                                    <button type="button" wire:click="setQuickAmount('MAX')" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-400 text-[10px] font-black hover:bg-slate-700 hover:text-white transition-all uppercase tracking-widest">ALL IN</button>
                                </div>
                            </div>

                            <button wire:click="placeBet" wire:loading.attr="disabled" class="w-full bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-400 text-white py-5 rounded-[1.5rem] font-black text-lg shadow-xl shadow-indigo-500/20 active:scale-95 transition-all disabled:opacity-50 flex items-center justify-center relative">
                                <span wire:loading.remove wire:target="placeBet">{{ __('CONFIRM TRANSACTION') }}</span>
                                <span wire:loading wire:target="placeBet">
                                    <svg class="animate-spin h-6 w-6 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </span>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </template>
    @endif

    @if($createModalOpen)
        <template x-teleport="body">
            <div class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-950/90 backdrop-blur-md">
                 
                <div @click.away="$wire.closeCreateModal()" class="relative w-full max-w-lg bg-slate-900 border border-slate-800 shadow-2xl rounded-[2.5rem] overflow-hidden flex flex-col animate-in zoom-in-95 duration-200 p-8">
                    
                    <button wire:click="closeCreateModal" class="absolute top-5 right-5 text-slate-500 hover:text-white transition-colors z-50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>

                    <div class="text-center mb-8 mt-2">
                        <div class="w-16 h-16 bg-emerald-500/10 rounded-2xl border border-emerald-500/20 flex items-center justify-center mx-auto mb-4 text-emerald-400 text-2xl">
                            💡
                        </div>
                        <h2 class="text-2xl font-black text-white mb-2">{{ __('Create a New Market') }}</h2>
                        <p class="text-slate-400 text-sm leading-relaxed">{{ __('Ask a clear question that can be answered with YES or NO.') }}</p>
                    </div>

                    <form wire:submit.prevent="createMarket">
                        <div class="mb-6">
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">{{ __('Market Question') }}</label>
                            <textarea wire:model="newMarketTitle" rows="3" placeholder="{{ __('e.g., Will humanity reach Mars by 2030?') }}" class="w-full bg-slate-950 border-2 border-slate-800 rounded-2xl text-white p-4 focus:ring-0 focus:border-emerald-500 transition-colors resize-none"></textarea>
                            @error('newMarketTitle') <span class="text-rose-400 text-xs font-bold mt-2 block">{{ __($message) }}</span> @enderror
                        </div>

                        <div class="flex gap-4 p-4 bg-slate-950/50 rounded-2xl border border-slate-800/50 mb-8 items-center justify-between">
                            <span class="text-xs font-black text-slate-400 uppercase tracking-widest">{{ __('Generated Outcomes:') }}</span>
                            <div class="flex gap-2">
                                <span class="px-3 py-1 bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 rounded-lg text-[10px] font-black tracking-widest">YES</span>
                                <span class="px-3 py-1 bg-slate-800 text-slate-400 border border-slate-700 rounded-lg text-[10px] font-black tracking-widest">NO</span>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-gradient-to-r from-emerald-500 to-emerald-400 hover:from-emerald-400 hover:to-emerald-300 text-slate-950 py-4 rounded-[1.5rem] font-black text-lg shadow-xl shadow-emerald-500/20 active:scale-95 transition-all">
                            <span wire:loading.remove wire:target="createMarket">{{ __('PUBLISH MARKET') }}</span>
                            <span wire:loading wire:target="createMarket">{{ __('Creating...') }}</span>
                        </button>
                    </form>
                </div>
            </div>
        </template>
    @endif

</div>