<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Market;
use App\Models\Outcome;
use App\Models\Bet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;

class MarketDetail extends Component
{
    public $market;
    public $betAmount = '';
    public $selectedOutcomeId = null;

    public function mount($id)
    {
        $this->market = Market::with('outcomes')->findOrFail($id);
    }

    public function setQuickAmount($amount)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($amount === 'MAX') {
            $this->betAmount = floor($user->balance / 100);
        } else {
            $this->betAmount = ((int)$this->betAmount) + $amount;
        }
    }

    public function placeBet()
    {
        
        $this->validate([
            'betAmount' => 'required|numeric|min:1',
            'selectedOutcomeId' => 'required|exists:outcomes,id',
        ], [
            'betAmount.required' => 'Enter an amount',
            'betAmount.min' => 'Minimum 1 ¢',
            'selectedOutcomeId.required' => 'Select YES or NO',
        ]);

        $betAmountCents = (int) ($this->betAmount * 100);
        
        /** @var \App\Models\User $user */
        $user = Auth::user();

        
        if ($user->balance < $betAmountCents) {
            $this->addError('betAmount', 'Insufficient funds!');
            return;
        }

        
        DB::transaction(function () use ($betAmountCents, $user) {
            
            
            $user->balance -= $betAmountCents;
            $user->save();

           
            Bet::create([
                'user_id' => $user->id,
                'outcome_id' => $this->selectedOutcomeId,
                'amount' => $betAmountCents,
            ]);

            
            $outcome = Outcome::find($this->selectedOutcomeId);
            $outcome->pool += $betAmountCents;
            $outcome->save();

            
            $this->market->total_pool += $betAmountCents;
            $this->market->save();
        });

        
        session()->flash('message', 'Transaction confirmed!');
        $this->betAmount = '';
        $this->selectedOutcomeId = null;
        
        
        $this->market->refresh(); 
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.market-detail');
    }
}