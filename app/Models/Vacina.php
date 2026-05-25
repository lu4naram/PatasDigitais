<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vacina extends Model
{
    protected $table = 'vacinas';
    
    protected $fillable = [
        'nome',
        'descricao',
    ];

    // Relacionamento com animais (muitos-para-muitos)
    public function animais()
    {
        return $this->belongsToMany(Animal::class, 'animal_vacinas', 'vacina_id', 'animal_id')
                    ->withTimestamps();
    }
}