<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Livewire\Attributes\Layout;
use Illuminate\Pagination\LengthAwarePaginator;

class Leaderboard extends Component
{
    use WithPagination;

    #[Layout('components.layouts.app')]
    public function render()
    {
        $users = User::with(['bets.outcome.market'])->get()->map(function ($user) {
            
            $netProfit = 0;
            $totalBets = 0;
            $wonBets = 0;

            foreach ($user->bets as $bet) {
                
                if ($bet->outcome->market->status === 'resolved') {
                    $totalBets++;
                    
                    if ($bet->outcome->is_winner == 1) {
                        
                        $marketTotalPool = $bet->outcome->market->total_pool ?: 1;
                        $outcomePool = $bet->outcome->pool ?: 1;
                        
                        $payout = ($bet->amount / $outcomePool) * $marketTotalPool;
                        
                        $netProfit += ($payout - $bet->amount);
                        $wonBets++;
                    } else {
                        $netProfit -= $bet->amount;
                    }
                }
            }

            $user->calculated_net_profit = $netProfit;
            $user->win_rate = $totalBets > 0 ? round(($wonBets / $totalBets) * 100) : 0;
            $user->total_resolved_bets = $totalBets;

            return $user;
        });

        
        $sortedUsers = $users->sortByDesc('calculated_net_profit')->values();

        
        $perPage = 20;
        $currentPage = $this->getPage(); 
        $items = $sortedUsers->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $rankedUsers = new LengthAwarePaginator(
            $items,
            $sortedUsers->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('livewire.leaderboard', [
            'rankedUsers' => $rankedUsers
        ]);
    }
}