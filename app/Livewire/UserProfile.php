<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use App\Models\User;

class UserProfile extends Component
{
    public $plainTextToken = null;

    #[Layout('components.layouts.app')]
    public function render()
    {
        /** @var User $user */
        $user = Auth::user();

        return view('livewire.user-profile', [
            'user' => $user,
            'tokens' => $user->tokens,
        ]);
    }

    public function generateToken()
    {
        /** @var User $user */
        $user = Auth::user();

        // Ștergem cheile vechi pentru siguranță
        $user->tokens()->delete();

        // Creăm cheia nouă
        $token = $user->createToken('TradingBotToken');
        
        // O salvăm în variabilă ca să i-o arătăm o singură dată
        $this->plainTextToken = $token->plainTextToken;

        session()->flash('message', 'API Key generated successfully!');
    }

    public function revokeTokens()
    {
        /** @var User $user */
        $user = Auth::user();
        
        $user->tokens()->delete();
        $this->plainTextToken = null;
        session()->flash('message', 'All API Keys revoked for your security.');
    }
}