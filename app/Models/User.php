<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Verificar se é admin
    public function isAdmin()
    {
        return $this->role === 'ADM';
    }

    // Verificar se é cliente
    public function isCliente()
    {
        return $this->role === 'CLI';
    }

    // Relacionamento com animais
    public function animais()
    {
        return $this->hasMany(Animal::class, 'user_id');
    }

    // Adoções que o usuário solicitou (como adotante)
    public function adocoesSolicitadas()
    {
        return $this->hasMany(Adocao::class, 'adotante_id');
    }

    // Adoções que o usuário recebeu (como doador)
    public function adocoesRecebidas()
    {
        return $this->hasMany(Adocao::class, 'doador_id');
    }

    // Notificações não lidas (adoções pendentes para o doador)
    public function notificacoesNaoLidas()
    {
        return $this->adocoesRecebidas()
            ->where('status', 'pendente')
            ->count();
    }
    // Contar pedidos de adoção pendentes (como doador)
    public function pedidosPendentesCount()
    {
        return Adocao::where('doador_id', $this->id)
            ->where('status', 'pendente')
            ->count();
    }
}