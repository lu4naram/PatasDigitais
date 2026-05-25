<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorito extends Model
{
    protected $table = 'favoritos';
    
    protected $fillable = [
        'animal_id',
        'user_id',
    ];
    
    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}