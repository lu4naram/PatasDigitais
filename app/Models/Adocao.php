<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Adocao extends Model
{
    protected $table = 'adocoes';

    protected $fillable = [
        'adotante_id',
        'doador_id',
        'animal_id',
        'status',
        'mensagem',  // NOVO CAMPO
        'data_adocao',
    ];

    protected $casts = [
        'data_adocao' => 'datetime',
    ];

    // Relacionamentos
    public function adotante()
    {
        return $this->belongsTo(User::class, 'adotante_id');
    }

    public function doador()
    {
        return $this->belongsTo(User::class, 'doador_id');
    }

    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }

    // Métodos úteis
    public function isPendente()
    {
        return $this->status === 'pendente';
    }

    public function isAprovada()
    {
        return $this->status === 'aprovada';
    }

    public function isRecusada()
    {
        return $this->status === 'recusada';
    }

    public function aprovar()
    {
        $this->update([
            'status' => 'aprovada',
            'data_adocao' => now(),
        ]);
        $this->animal->update(['adotado' => true]);
    }

    public function recusar()
    {
        $this->update(['status' => 'recusada']);
    }
}