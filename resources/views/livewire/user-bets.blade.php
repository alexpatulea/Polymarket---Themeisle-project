<div class="space-y-12">
   
    <div class="flex items-center gap-4">
        <h1 class="text-3xl font-black text-white uppercase tracking-tighter">{{ __('My Portfolio') }}</h1>
        <div class="h-1 flex-grow bg-slate-800 rounded-full"></div>
    </div>

    
    <section>
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-indigo-400 flex items-center gap-2">
                <span class="w-2 h-2 bg-indigo-500 rounded-full animate-pulse"></span>
                {{ __('Active Positions') }}
            </h2>
        </div>

        <div class="grid gap-4">
            @forelse($activeBets as $bet)
                @php 
                    
                    $rawTitle = $bet->outcome->market->title;
                    $titleArray = is_array($rawTitle) ? $rawTitle : json_decode($rawTitle, true);
                    $marketTitle = is_array($titleArray) ? ($titleArray[app()->getLocale()] ?? $titleArray['en'] ?? 'N/A') : $rawTitle;

                    
                    $rawName = $bet->outcome->name;
                    $nameArray = is_array($rawName) ? $rawName : json_decode($rawName, true);
                    $outcomeName = is_array($nameArray) ? ($nameArray[app()->getLocale()] ?? $nameArray['en'] ?? 'N/A') : $rawName;

                   
                    $totalPool = $bet->outcome->market->total_pool ?: 1;
                    $outcomePool = $bet->outcome->pool ?: 1;
                    $odds = number_format($totalPool / $outcomePool, 2);
                @endphp

                <div class="bg-slate-900 border border-slate-800 p-6 rounded-[1.5rem] flex flex-col md:flex-row md:items-center justify-between gap-4 transition-all hover:border-slate-700">
                    <div class="space-y-1">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">{{ __('Market') }}</p>
                        <h3 class="text-lg font-bold text-white">{{ $marketTitle }}</h3>
                    </div>
                    
                    <div class="flex gap-8 text-center md:text-left">
                        <div>
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">{{ __('Your Vote') }}</p>
                            <span class="text-indigo-400 font-black">{{ $outcomeName }}</span>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">{{ __('Stake') }}</p>
                            <span class="text-white font-black">{{ number_format($bet->amount / 100, 2) }} ¢</span>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">{{ __('Current Odds') }}</p>
                            <span class="text-emerald-400 font-black">x{{ $odds }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-slate-900/50 border border-slate-800 border-dashed p-8 rounded-[1.5rem] text-center text-slate-500 italic">
                    {{ __('No active positions at the moment.') }}
                </div>
            @endforelse
        </div>
        <div class="mt-4">{{ $activeBets->links('components.custom-pagination') }}</div>
    </section>

    
    <section>
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-slate-400 flex items-center gap-2">
                <span class="w-2 h-2 bg-slate-600 rounded-full"></span>
                {{ __('Closed Positions') }}
            </h2>
        </div>

        <div class="overflow-hidden bg-slate-900 border border-slate-800 rounded-[1.5rem]">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-950 text-slate-500 text-[10px] uppercase font-black tracking-widest">
                    <tr>
                        <th class="px-6 py-4">{{ __('Market') }}</th>
                        <th class="px-6 py-4 text-center">{{ __('Your Pick') }}</th>
                        <th class="px-6 py-4 text-center">{{ __('Status') }}</th>
                        <th class="px-6 py-4 text-right">{{ __('Result') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($resolvedBets as $bet)
                            
                            @php 
                            $rawTitle = $bet->outcome->market->title;
                            $titleArray = is_array($rawTitle) ? $rawTitle : json_decode($rawTitle, true);
                            $marketTitle = is_array($titleArray) ? ($titleArray[app()->getLocale()] ?? $titleArray['en'] ?? 'N/A') : $rawTitle;

                            $rawName = $bet->outcome->name;
                            $nameArray = is_array($rawName) ? $rawName : json_decode($rawName, true);
                            $outcomeName = is_array($nameArray) ? ($nameArray[app()->getLocale()] ?? $nameArray['en'] ?? 'N/A') : $rawName;
                            
                            $isWin = $bet->outcome->is_winner == 1;
                            $isArchived = $bet->outcome->market->status === 'archived';

                            // CALCULUL SUMEI REALE DE AFIȘAT
                            $displayAmount = $bet->amount; // Default: arătăm miza (pentru pierderi și refund)
                            if ($isWin) {
                                $totalPool = $bet->outcome->market->total_pool ?: 1;
                                $outcomePool = $bet->outcome->pool ?: 1;
                                // Matematica exactă din backend
                                $displayAmount = ($bet->amount / $outcomePool) * $totalPool;
                            }
                        @endphp
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-200 text-sm">{{ $marketTitle }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-xs font-black px-3 py-1 bg-slate-800 rounded-lg text-slate-300">{{ $outcomeName }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($isArchived)
                                    <span class="text-[10px] font-black text-slate-500 uppercase">{{ __('Refunded') }}</span>
                                @elseif($isWin)
                                    <span class="text-[10px] font-black text-emerald-500 uppercase bg-emerald-500/10 px-2 py-1 rounded border border-emerald-500/20">🏆 {{ __('Won') }}</span>
                                @else
                                    <span class="text-[10px] font-black text-red-400 uppercase">{{ __('Lost') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($isArchived)
                                    <span class="font-black text-slate-400" title="{{ __('Stake Refunded') }}">
                                        +{{ number_format($displayAmount / 100, 2) }} ¢
                                    </span>
                                @elseif($isWin)
                                    <span class="font-black text-emerald-400" title="{{ __('Total Payout') }}">
                                        +{{ number_format(floor($displayAmount) / 100, 2) }} ¢
                                    </span>
                                @else
                                    <span class="font-black text-slate-500" title="{{ __('Stake Lost') }}">
                                        -{{ number_format($displayAmount / 100, 2) }} ¢
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-500 text-sm italic">{{ __('No history found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $resolvedBets->links('components.custom-pagination') }}</div>
    </section>
</div>