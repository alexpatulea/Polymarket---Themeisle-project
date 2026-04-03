<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
class Outcome extends Model
{
   use HasTranslations;
    protected $fillable = ['market_id', 'name', 'pool', 'is_winner'];
    public $translatable = ['name']; 
    protected $casts = [
    'name' => 'array',
];
   public function market()
    {
    return $this->belongsTo(Market::class);
    }

public function bets()
    {
    return $this->hasMany(Bet::class);
    }
    
}
