<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Market;
use App\Models\Outcome;
use App\Models\Bet;

class ApiController extends Controller
{
    // 1. Listarea piețelor (cu tot cu opțiuni)
    public function listMarkets()
    {
        $markets = Market::with('outcomes')->orderBy('created_at', 'desc')->paginate(10);
        return response()->json($markets);
    }

    // 2. Vizualizarea unei singure piețe
    public function viewMarket($id)
    {
        $market = Market::with('outcomes')->find($id);

        if (!$market) {
            return response()->json(['error' => 'Market not found'], 404);
        }

        return response()->json($market);
    }

    // 3. Crearea unei noi piețe de către un bot
    public function createMarket(Request $request)
    {
        $request->validate([
            'title' => 'required|string|min:10|max:255|ends_with:?',
        ]);

        // La fel ca în interfață, salvăm pentru ambele limbi inițial
        $market = Market::create([
            'title' => ['en' => $request->title, 'ro' => $request->title],
            'status' => 'active',
            'total_pool' => 0,
        ]);

        Outcome::create(['market_id' => $market->id, 'name' => ['en' => 'YES', 'ro' => 'DA'], 'pool' => 0]);
        Outcome::create(['market_id' => $market->id, 'name' => ['en' => 'NO', 'ro' => 'NU'], 'pool' => 0]);

        // Încărcăm opțiunile proaspăt create pentru a le returna în răspuns
        $market->load('outcomes');

        return response()->json([
            'message' => 'Market created successfully',
            'market' => $market
        ], 201);
    }

    // 4. Plasarea unui pariu automat
    public function placeBet(Request $request)
    {
        $request->validate([
            'market_id' => 'required|exists:markets,id',
            'outcome_id' => 'required|exists:outcomes,id',
            'amount' => 'required|integer|min:1', // Suma în cenți
        ]);

        $user = $request->user(); // Bot-ul (Utilizatorul) autentificat prin token
        $market = Market::findOrFail($request->market_id);
        $outcome = Outcome::findOrFail($request->outcome_id);

        // Validări de siguranță
        if ($market->status !== 'active') {
            return response()->json(['error' => 'Market is not active'], 400);
        }

        if ($outcome->market_id !== $market->id) {
            return response()->json(['error' => 'Outcome does not belong to this market'], 400);
        }

        if ($user->balance < $request->amount) {
            return response()->json(['error' => 'Insufficient balance'], 400);
        }

        // Executarea tranzacției
        $user->balance -= $request->amount;
        $user->save();

        $bet = Bet::create([
            'user_id' => $user->id,
            'outcome_id' => $outcome->id,
            'amount' => $request->amount,
        ]);

        $outcome->pool += $request->amount;
        $outcome->save();

        $market->total_pool += $request->amount;
        $market->save();

        return response()->json([
            'message' => 'Bet placed successfully',
            'new_balance' => $user->balance,
            'bet' => $bet
        ], 200);
    }
}