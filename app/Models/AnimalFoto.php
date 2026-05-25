<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnimalFoto extends Model
{
    protected $table = 'animal_fotos';

    protected $fillable = [
        'animal_id',
        'caminho',
    ];

    public function animal()
    {
        return $this->belongsTo(Animal::class, 'animal_id');
    }
}