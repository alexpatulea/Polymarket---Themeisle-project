<?php

namespace App\Http\Controllers;

use App\Models\Market;
use Illuminate\Http\Request;

class MarketController extends Controller
{
    public function index(Request $request)
    {
        
        $status = $request->query('status', 'active');
        $sortBy = $request->query('sort', 'created_at');

        
        $query = Market::with('outcomes')->where('status', $status);

        
        if ($sortBy === 'total_pool') {
            $query->orderBy('total_pool', 'desc');
        } else {
            $query->orderBy('created_at', 'desc'); 
        }

        
        $markets = $query->paginate(20);

        
        return view('markets.index', compact('markets'));
    }
}