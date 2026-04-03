<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Market;
use App\Models\Outcome;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use App\Models\Bet;

class AdminDashboard extends Component
{
    use WithPagination;

    protected $paginationTheme = 'components.custom-pagination';

    
    public function mount()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

       
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        
        $markets = Market::where('status', 'active')
            ->with('outcomes')
            ->latest()
            ->paginate(20); 

        return view('livewire.admin-dashboard', [
            'markets' => $markets,
        ]);
    }
    
    public function archiveMarket($marketId)
    {
        DB::transaction(function () use ($marketId) {
            $market = Market::findOrFail($marketId);
            
            if ($market->status !== 'active') {
                return; 
            }

           
            $market->update(['status' => 'archived']);

            
            $bets = Bet::whereHas('outcome', function($query) use ($marketId) {
                $query->where('market_id', $marketId);
            })->with('user')->get();

           
            foreach ($bets as $bet) {
                $bet->user->increment('balance', $bet->amount);
            }
        });

        
        session()->flash('message', 'Market Archived. All stakes have been refunded.');
    }

    
    public function resolveMarket($marketId, $winningOutcomeId)
    {
        DB::transaction(function () use ($marketId, $winningOutcomeId) {
            $market = Market::findOrFail($marketId);
            $winningOutcome = Outcome::findOrFail($winningOutcomeId);

            if ($market->status !== 'active') {
                return; 
            }

           
            if ($winningOutcome->pool == 0 && $market->total_pool > 0) {
                $this->archiveMarket($marketId);
                return;
            }

            
            $market->update(['status' => 'resolved']);
            $winningOutcome->update(['is_winner' => true]);

            
            $winningBets = Bet::where('outcome_id', $winningOutcomeId)->with('user')->get();

            
            foreach ($winningBets as $bet) {
               
                $percentageOwned = $bet->amount / $winningOutcome->pool;
                
                
                $payout = $percentageOwned * $market->total_pool;

                
                $bet->user->increment('balance', (int) floor($payout));
            }
        });

        session()->flash('message', 'Market Resolved! Payouts have been distributed.');
    }
}