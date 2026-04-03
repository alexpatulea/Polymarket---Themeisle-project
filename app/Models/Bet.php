<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bet extends Model
{
    protected $fillable = ['user_id', 'outcome_id', 'amount'];
    public function user()
    {
    return $this->belongsTo(User::class);
    }

    public function outcome()
    {
    return $this->belongsTo(Outcome::class);
    }
}
