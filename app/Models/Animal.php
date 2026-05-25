<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AnimalFoto;

class Animal extends Model
{
    protected $table = 'animais';

    public $incrementing = true;

    // app/Models/Animal.php

    protected $fillable = [
        'nome',
        'sexo',
        'idade',
        'sobre',
        'castracao',
        'especie_id',
        'user_id',
        'adotado',
    ];

    protected $casts = [
        'castracao' => 'boolean',
        'adotado' => 'boolean',
        'idade' => 'integer',
    ];


    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Verificar se o usuário atual é o dono do anúncio
    public function isOwner()
    {
        return auth()->check() && auth()->id() === $this->user_id;
    }

    public function getIdadeFormatadaAttribute()
    {
        $meses = $this->idade;

        if ($meses <= 0) {
            return 'Menos de 1 mês';
        }

        $anos = floor($meses / 12);
        $mesesRestantes = $meses % 12;

        $partes = [];
        if ($anos > 0) {
            $partes[] = $anos . ' ' . ($anos == 1 ? 'ano' : 'anos');
        }
        if ($mesesRestantes > 0) {
            $partes[] = $mesesRestantes . ' ' . ($mesesRestantes == 1 ? 'mês' : 'meses');
        }

        $texto = implode(' e ', $partes);

        return $texto;
    }

    public function especie()
    {
        return $this->belongsTo(Especie::class, 'especie_id');
    }
    public function fotos()
    {
        return $this->hasMany(AnimalFoto::class, 'animal_id');
    }
    public function vacinas()
    {
        return $this->belongsToMany(Vacina::class, 'animal_vacinas', 'animal_id', 'vacina_id')
            ->withTimestamps();
    }

    // Relacionamento com adoções
    public function solicitacoesAdocao()
    {
        return $this->hasMany(Adocao::class, 'animal_id');
    }

    public function adocaoAprovada()
    {
        return $this->hasOne(Adocao::class, 'animal_id')->where('status', 'aprovada');
    }

    public function podeSerAdotado()
    {
        return !$this->adotado && $this->solicitacoesAdocao()->where('status', 'aprovada')->doesntExist();
    }
    // Verificar se o animal foi salvo nos favoritos por um usuário
    public function foiSalvoPor($userId)
    {
        return $this->favoritos()->where('user_id', $userId)->exists();
    }
    public function favoritos()
    {
        return $this->hasMany(Favorito::class, 'animal_id');
    }
    // Verificar se o usuário tem uma solicitação pendente para este animal
    
    public function solicitacaoPendentePor($userId)
    {
        return $this->solicitacoesAdocao()
            ->where('adotante_id', $userId)
            ->where('status', 'pendente')
            ->exists();
    }
}
