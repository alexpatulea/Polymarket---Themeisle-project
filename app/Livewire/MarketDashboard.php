<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Market;
use App\Models\Outcome;
use App\Models\Bet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; 

class MarketDashboard extends Component
{
    use WithPagination;

    public $statusFilter = 'active';
    public $sortField = 'created_at';
    public $createModalOpen = false;
    public $newMarketTitle = '';
    
    public $tradeModalOpen = false;
    public $selectedMarket = null;
    public $selectedOutcomeId = null;
    public $betAmount = '';

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedSortField()
    {
        $this->resetPage();
    }

    
    public function openTradeModal($marketId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->selectedMarket = Market::with('outcomes')->find($marketId);
        $this->selectedOutcomeId = $this->selectedMarket->outcomes->first()->id ?? null;
        $this->betAmount = '';
        $this->tradeModalOpen = true;
        
        $this->resetErrorBag();
    }

    public function closeTradeModal()
    {
        $this->tradeModalOpen = false;
        $this->selectedMarket = null;
        $this->selectedOutcomeId = null;
        $this->betAmount = '';
    }

    // Logica butoanelor rapide
    public function setQuickAmount($amount)
    {
        if ($amount === 'MAX') {
            // Luăm balanța și o transformăm în unități întregi (¢)
            $this->betAmount = floor(Auth::user()->balance / 100);
        } else {
            $current = (float)$this->betAmount;
            $this->betAmount = $current + $amount;
        }
    }

    
    public function placeBet()
    {
        $this->validate([
            'selectedOutcomeId' => 'required|exists:outcomes,id',
            'betAmount' => 'required|numeric|min:1',
        ]);
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $amountInCents = $this->betAmount * 100;

        if ($user->balance < $amountInCents) {
            $this->addError('betAmount', __('Insufficient funds.'));
            return;
        }

        $outcome = Outcome::find($this->selectedOutcomeId);
        $market = $this->selectedMarket;

        
        DB::transaction(function () use ($user, $outcome, $market, $amountInCents) {
            $user->decrement('balance', $amountInCents);
            $outcome->increment('pool', $amountInCents);
            $market->increment('total_pool', $amountInCents);

            Bet::create([
                'user_id' => $user->id,
                'outcome_id' => $outcome->id,
                'amount' => $amountInCents,
            ]);
        });
        $user->refresh(); 
        $this->dispatch('balance-updated', newBalance: $user->balance);

        $this->closeTradeModal();
        session()->flash('message', __('Trade successful! Your prediction has been placed.'));
    }

    public function render()
    {
        $query = Market::with('outcomes')->where('status', $this->statusFilter);

        if ($this->sortField === 'total_pool') {
            $query->orderBy('total_pool', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return view('livewire.market-dashboard', [
            'markets' => $query->paginate(20)
        ]);
    }
    public function openCreateModal()
    {
        $this->newMarketTitle = '';
        $this->createModalOpen = true;
    }

    public function closeCreateModal()
    {
        $this->createModalOpen = false;
        $this->resetValidation('newMarketTitle');
    }

    public function createMarket()
    {
        $this->validate([
            'newMarketTitle' => 'required|string|min:10|max:255|ends_with:?',
        ], [
            'newMarketTitle.required' => 'The question is required.',
            'newMarketTitle.min' => 'The question must be at least 10 characters long.',
            'newMarketTitle.ends_with' => 'The market title must be a question and end with a question mark (?).',
        ]);

        try {
            
            $tr = new \Stichoza\GoogleTranslate\GoogleTranslate();
            
            
            $titleRo = $tr->setSource()->setTarget('ro')->translate($this->newMarketTitle);
            
            
            $titleEn = $tr->setSource()->setTarget('en')->translate($this->newMarketTitle);
        } catch (\Exception $e) {
           
            $titleRo = $this->newMarketTitle;
            $titleEn = $this->newMarketTitle;
        }

        
        $market = \App\Models\Market::create([
            'title' => ['en' => $titleEn, 'ro' => $titleRo], 
            'status' => 'active',
            'total_pool' => 0,
        ]);

        
        \App\Models\Outcome::create([
            'market_id' => $market->id,
            'name' => ['en' => 'YES', 'ro' => 'DA'],
            'pool' => 0,
        ]);

        \App\Models\Outcome::create([
            'market_id' => $market->id,
            'name' => ['en' => 'NO', 'ro' => 'NU'],
            'pool' => 0,
        ]);

        $this->closeCreateModal();
        session()->flash('message', 'Market created successfully! You can now place bets on it.');
    }
}