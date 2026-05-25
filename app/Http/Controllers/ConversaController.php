<?php

namespace App\Http\Controllers;

use App\Models\Conversa;
use App\Models\Mensagem;
use App\Models\Animal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Adocao; 
use Exception;

class ConversaController extends Controller
{
    // Listar todas as conversas do usuário
    public function index()
    {
        $conversas = Conversa::where('adotante_id', auth()->id())
            ->orWhere('doador_id', auth()->id())
            ->with(['animal', 'adotante', 'doador', 'mensagens' => function($q) {
                $q->latest()->limit(1);
            }])
            ->orderBy('updated_at', 'desc')
            ->get();
        
        $animais = Animal::with('fotos');
        
        return view('conversas.index', compact('conversas', 'animais'));
    }
    
    // Mostrar conversa específica (por animal e doador)
    public function show(Animal $animal, User $doador = null)
    {
        // Se não passou o doador, tenta buscar pela solicitação de adoção
        if (!$doador) {
            $adocao = Adocao::where('animal_id', $animal->id)
                ->where(function($q) {
                    $q->where('adotante_id', auth()->id())
                      ->orWhere('doador_id', auth()->id());
                })
                ->first();
            
            if ($adocao) {
                $doador = $adocao->doador_id == auth()->id() ? $adocao->adotante : $adocao->doador;
            } else {
                $doador = $animal->usuario;
            }
        }
        
        // Buscar ou criar conversa
        $conversa = Conversa::where('animal_id', $animal->id)
            ->where(function($q) use ($doador) {
                $q->where('adotante_id', auth()->id())
                  ->where('doador_id', $doador->id);
            })
            ->orWhere(function($q) use ($doador) {
                $q->where('adotante_id', $doador->id)
                  ->where('doador_id', auth()->id());
            })
            ->first();
        
        if (!$conversa) {
            $conversa = Conversa::create([
                'animal_id' => $animal->id,
                'adotante_id' => auth()->id(),
                'doador_id' => $doador->id,
            ]);
        }
        
        // Marcar mensagens como lidas
        Mensagem::where('conversa_id', $conversa->id)
            ->where('remetente_id', '!=', auth()->id())
            ->update(['lida' => true]);
        
        $mensagens = $conversa->mensagens()->with('remetente')->orderBy('created_at', 'asc')->get();
        
        return view('conversas.show', compact('conversa', 'animal', 'doador', 'mensagens'));
    }
    
    // Enviar mensagem
    public function send(Request $request, Conversa $conversa)
    {
        $request->validate([
            'mensagem' => 'required|string|max:1000',
        ]);
        
        try {
            Mensagem::create([
                'conversa_id' => $conversa->id,
                'remetente_id' => auth()->id(),
                'mensagem' => $request->mensagem,
                'lida' => false,
            ]);
            
            $conversa->touch(); // atualiza updated_at
            
            return redirect()->back()->with('success', 'Mensagem enviada!');
            
        } catch (Exception $e) {
            Log::error('Erro ao enviar mensagem: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao enviar mensagem.');
        }
    }
    
    // Iniciar conversa a partir de uma solicitação de adoção
    public function iniciarPorAdocao(Adocao $adocao)
    {
        $conversa = Conversa::firstOrCreate([
            'animal_id' => $adocao->animal_id,
            'adotante_id' => $adocao->adotante_id,
            'doador_id' => $adocao->doador_id,
        ]);
        
        return redirect()->route('conversas.show', [
            'animal' => $adocao->animal_id,
            'doador' => $conversa->outroUsuario(auth()->id())->id
        ]);
    }
}
