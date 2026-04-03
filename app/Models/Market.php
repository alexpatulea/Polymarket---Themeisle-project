<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use App\Models\Outcome; // Opțional, dar recomandat

class Market extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = ['title', 'status', 'total_pool'];
    public $translatable = ['title'];
    protected $casts = [
    'title' => 'array',
];

    
    public function outcomes()
    {
        
        return $this->hasMany(Outcome::class);
    }
}