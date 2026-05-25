<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversa extends Model
{
    protected $table = 'conversas';
    
    protected $fillable = [
        'animal_id',
        'adotante_id',
        'doador_id',
    ];
    
    // Relacionamentos
    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }
    
    public function adotante()
    {
        return $this->belongsTo(User::class, 'adotante_id');
    }
    
    public function doador()
    {
        return $this->belongsTo(User::class, 'doador_id');
    }
    
    public function mensagens()
    {
        return $this->hasMany(Mensagem::class);
    }
    
    // Método para pegar o outro usuário na conversa
    public function outroUsuario($userId)
    {
        if ($this->adotante_id == $userId) {
            return $this->doador;
        }
        return $this->adotante;
    }
    
    // Método para pegar o papel do usuário na conversa
    public function papelDoUsuario($userId)
    {
        if ($this->adotante_id == $userId) {
            return 'adotante';
        }
        if ($this->doador_id == $userId) {
            return 'doador';
        }
        return null;
    }
    
    // Última mensagem (acessório)
    public function getUltimaMensagemAttribute()
    {
        return $this->mensagens()->latest()->first();
    }
    
    // Contagem de mensagens não lidas
    public function mensagensNaoLidas($userId)
    {
        return $this->mensagens()
            ->where('remetente_id', '!=', $userId)
            ->where('lida', false)
            ->count();
    }
}