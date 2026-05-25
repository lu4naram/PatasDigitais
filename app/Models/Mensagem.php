<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mensagem extends Model
{
    protected $table = 'mensagens';
    
    protected $fillable = [
        'conversa_id',
        'remetente_id',
        'mensagem',
        'lida',
    ];
    
    protected $casts = [
        'lida' => 'boolean',
    ];
    
    // Relacionamentos
    public function conversa()
    {
        return $this->belongsTo(Conversa::class);
    }
    
    public function remetente()
    {
        return $this->belongsTo(User::class, 'remetente_id');
    }
    
    // Marcar como lida
    public function marcarComoLida()
    {
        $this->update(['lida' => true]);
    }
}