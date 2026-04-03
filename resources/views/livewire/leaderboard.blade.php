<div class="max-w-5xl mx-auto space-y-8 text-white pb-12 mt-8">
    
    <div class="flex flex-col items-center justify-center mb-12 text-center space-y-4">
        <span class="text-6xl drop-shadow-[0_0_15px_rgba(251,191,36,0.3)]">🏆</span>
        <h1 class="text-4xl font-black uppercase tracking-tighter text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-orange-500">
            {{ __('Global Leaderboard') }}
        </h1>
        <p class="text-slate-400 font-medium">{{ __('Top traders ranked by realized net profit.') }}</p>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-[2.5rem] overflow-hidden shadow-2xl">
        
        <div class="hidden md:grid grid-cols-12 gap-4 p-6 bg-slate-950/80 border-b border-slate-800 text-[10px] font-black text-slate-500 uppercase tracking-widest text-center items-center">
            <div class="col-span-1">{{ __('Rank') }}</div>
            <div class="col-span-5 text-left pl-2">{{ __('Trader') }}</div>
            <div class="col-span-2">{{ __('Win Rate') }}</div>
            <div class="col-span-2">{{ __('Resolved Bets') }}</div>
            <div class="col-span-2 text-right pr-4">{{ __('Net Profit') }}</div>
        </div>

        <div class="divide-y divide-slate-800/50">
            @forelse($rankedUsers as $index => $user)
                @php
                    $rank = $index + 1;
                    $isPositive = $user->calculated_net_profit >= 0;
                    $profitFormatted = number_format(abs($user->calculated_net_profit) / 100, 2);
                    
                    // Culorile pentru Podium
                    $rankBg = 'bg-slate-800 text-slate-400 border border-slate-700';
                    if ($rank === 1) $rankBg = 'bg-gradient-to-br from-amber-300 to-amber-500 text-amber-950 shadow-[0_0_20px_-3px_rgba(251,191,36,0.6)] border-none font-black';
                    if ($rank === 2) $rankBg = 'bg-gradient-to-br from-slate-200 to-slate-400 text-slate-900 shadow-[0_0_20px_-3px_rgba(203,213,225,0.4)] border-none font-black';
                    if ($rank === 3) $rankBg = 'bg-gradient-to-br from-orange-500 to-orange-700 text-orange-100 shadow-[0_0_20px_-3px_rgba(194,65,12,0.4)] border-none font-black';
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 p-6 items-center hover:bg-slate-800/30 transition-colors relative group">
                    
                    <div class="col-span-1 flex justify-between md:justify-center items-center">
                        <span class="text-xs font-bold text-slate-500 uppercase md:hidden">{{ __('Rank') }}</span>
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-sm {{ $rankBg }} transition-transform group-hover:scale-110">
                            {{ $rank }}
                        </div>
                    </div>

                    <div class="col-span-5 flex items-center gap-4 mt-2 md:mt-0">
                        <div class="w-12 h-12 rounded-full bg-slate-950 border-2 border-slate-800 flex items-center justify-center text-slate-400 font-black uppercase shrink-0 text-sm shadow-inner">
                            {{ substr($user->name, 0, 2) }}
                        </div>
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-200 text-lg">{{ $user->name }}</span>
                            <span class="text-[10px] text-slate-500 uppercase tracking-widest font-black md:hidden mt-1">
                                {{ __('Win Rate: ') }} <span class="text-indigo-400">{{ $user->win_rate }}%</span>
                            </span>
                        </div>
                    </div>

                    <div class="col-span-2 hidden md:flex flex-col items-center justify-center w-full px-4">
                        <span class="text-xs font-black text-slate-300 mb-1.5">{{ $user->win_rate }}%</span>
                        <div class="w-full bg-slate-950 rounded-full h-1.5 overflow-hidden border border-slate-800/50 shadow-inner">
                            <div class="bg-indigo-500 h-full rounded-full transition-all duration-1000" style="width: {{ $user->win_rate }}%"></div>
                        </div>
                    </div>

                    <div class="col-span-2 hidden md:flex justify-center">
                        <span class="px-4 py-1.5 bg-slate-950 border border-slate-800 text-slate-400 rounded-xl text-xs font-black shadow-inner">
                            {{ $user->total_resolved_bets }}
                        </span>
                    </div>

                    <div class="col-span-1 md:col-span-2 flex justify-between md:justify-end items-center mt-4 md:mt-0 md:pr-4 pt-4 md:pt-0 border-t border-slate-800 md:border-t-0">
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest md:hidden">{{ __('Net Profit') }}</span>
                        <div class="text-xl font-black {{ $isPositive ? 'text-emerald-400' : 'text-rose-500' }} drop-shadow-md">
                            {{ $isPositive ? '+' : '-' }}{{ $profitFormatted }} ¢
                        </div>
                    </div>

                </div>
            @empty
                <div class="p-16 text-center flex flex-col items-center justify-center">
                    <span class="text-5xl mb-4 opacity-50">👻</span>
                    <h3 class="text-lg font-bold text-slate-300">{{ __('It\'s a ghost town here') }}</h3>
                    <p class="text-slate-500 font-medium mt-1">{{ __('No markets have been resolved yet. Payouts pending.') }}</p>
                </div>
            @endforelse
        </div>
    </div>
    <div class="mt-8">
            {{ $rankedUsers->links('components.custom-pagination') }}
        </div>
</div>