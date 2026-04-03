<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Bet;
use Illuminate\Support\Facades\Auth; 
use Livewire\Attributes\Layout;    

class UserBets extends Component
{
    use WithPagination;

    protected $paginationTheme = 'components.custom-pagination';

    
    #[Layout('components.layouts.app')] 
    public function render()
    {
        
        $userId = Auth::id();

        $activeBets = Bet::where('user_id', $userId)
            ->whereHas('outcome.market', function ($query) {
                $query->where('status', 'active');
            })
            ->with(['outcome.market'])
            ->latest()
            ->paginate(20, pageName: 'activePage'); 

        $resolvedBets = Bet::where('user_id', $userId)
            ->whereHas('outcome.market', function ($query) {
                $query->whereIn('status', ['resolved', 'archived']);
            })
            ->with(['outcome.market'])
            ->latest()
            ->paginate(20, pageName: 'resolvedPage'); 

        return view('livewire.user-bets', [
            'activeBets' => $activeBets,
            'resolvedBets' => $resolvedBets,
        ]);
    }
}