<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\Adocao;
use App\Models\Favorito;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class AdocaoController extends Controller
{
    // Solicitar adoção
    public function solicitar(Request $request, Animal $animal)
    {
        // Verificar se é o próprio anúncio
        if (auth()->id() === $animal->user_id) {
            return back()->with('error', 'Você não pode solicitar adoção do seu próprio animal.');
        }

        // Verificar se animal já foi adotado
        if ($animal->adotado) {
            return back()->with('error', 'Este animal já foi adotado.');
        }

        // Verificar se já existe solicitação pendente
        $solicitacaoExistente = Adocao::where('animal_id', $animal->id)
            ->where('adotante_id', auth()->id())
            ->whereIn('status', ['pendente', 'aprovada'])
            ->first();

        if ($solicitacaoExistente) {
            return back()->with('error', 'Você já possui uma solicitação para este animal.');
        }

        try {
            Adocao::create([
                'animal_id' => $animal->id,
                'adotante_id' => auth()->id(),
                'doador_id' => $animal->user_id,
                'status' => 'pendente',
                'mensagem' => $request->mensagem,  // SALVAR A MENSAGEM
            ]);

            return back()->with('success', 'Solicitação de adoção enviada ao anunciante!');

        } catch (Exception $e) {
            Log::error('Erro ao solicitar adoção: ' . $e->getMessage());
            return back()->with('error', 'Erro ao enviar solicitação.');
        }
    }

    // Salvar nos favoritos
    public function favoritar(Animal $animal)
    {
        // Verificar se é o próprio anúncio
        if (auth()->id() === $animal->user_id) {
            return back()->with('error', 'Você não pode favoritar seu próprio anúncio.');
        }

        try {
            $favorito = Favorito::where('animal_id', $animal->id)
                ->where('user_id', auth()->id())
                ->first();

            if ($favorito) {
                $favorito->delete();
                $message = 'Anúncio removido dos favoritos.';
            } else {
                Favorito::create([
                    'animal_id' => $animal->id,
                    'user_id' => auth()->id(),
                ]);
                $message = 'Anúncio salvo nos favoritos!';
            }

            return back()->with('success', $message);

        } catch (Exception $e) {
            Log::error('Erro ao favoritar: ' . $e->getMessage());
            return back()->with('error', 'Erro ao processar solicitação.');
        }
    }

    // Notificações do doador
    public function notificacoes()
    {
        $notificacoes = Adocao::where('doador_id', auth()->id())
            ->where('status', 'pendente')
            ->with(['animal', 'adotante'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('notificacoes', compact('notificacoes'));
    }

    // Responder solicitação
    public function responder(Request $request, Adocao $adocao)
    {
        // Verificar se é o doador
        if (auth()->id() !== $adocao->doador_id) {
            return back()->with('error', 'Sem permissão.');
        }

        $request->validate([
            'status' => 'required|in:aprovada,recusada',
        ]);

        try {
            if ($request->status === 'aprovada') {
                $adocao->aprovar();
                $message = 'Solicitação aprovada! O animal foi marcado como adotado.';
            } else {
                $adocao->recusar();
                $message = 'Solicitação recusada.';
            }

            return back()->with('success', $message);

        } catch (Exception $e) {
            Log::error('Erro ao responder solicitação: ' . $e->getMessage());
            return back()->with('error', 'Erro ao processar solicitação.');
        }
    }

    // Minhas solicitações (como adotante)
    public function minhasSolicitacoes()
    {
        $solicitacoes = Adocao::where('adotante_id', auth()->id())
            ->with(['animal', 'doador'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('minhas-solicitacoes', compact('solicitacoes'));
    }

    // Solicitações recebidas (como doador)
    public function solicitacoesRecebidas()
    {
        $solicitacoes = Adocao::where('doador_id', auth()->id())
            ->with(['animal', 'adotante'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('solicitacoes-recebidas', compact('solicitacoes'));
    }
    public function anunciosSalvos()
    {
        $favoritos = Favorito::where('user_id', auth()->id())
            ->with(['animal.fotos', 'animal.usuario'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Mudar para a pasta "salvos"
        return view('salvos.anuncios-salvos', compact('favoritos'));
    }
    // Listar pedidos de adoção recebidos (para o doador)
    public function pedidosRecebidos()
    {
        $pedidos = Adocao::where('doador_id', auth()->id())
            ->with(['animal.fotos', 'adotante'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('adocoes.meus-pedidos', compact('pedidos'));
    }

    // Visualizar pedido específico
    public function verPedido(Adocao $adocao)
    {
        // Verificar se o usuário é o doador
        if (auth()->id() !== $adocao->doador_id) {
            return redirect()->route('pedidos-adocao')->with('error', 'Sem permissão.');
        }

        $adocao->load(['animal.fotos', 'adotante']);

        return view('adocoes.ver-meu-pedido', compact('adocao'));
    }

    // Aceitar pedido de adoção
    public function aceitarPedido(Adocao $adocao)
    {
        if (auth()->id() !== $adocao->doador_id) {
            return redirect()->route('pedidos-adocao')->with('error', 'Sem permissão.');
        }

        if ($adocao->status !== 'pendente') {
            return back()->with('error', 'Este pedido já foi respondido.');
        }


        try {
            DB::beginTransaction();

            // Aprovar adoção
            $adocao->update([
                'status' => 'aprovada',
                'data_adocao' => now(),
            ]);

            // Marcar animal como adotado
            $adocao->animal->update(['adotado' => true]);

            DB::commit();

            cache()->forget('user_' . auth()->id() . '_pedidos_pendentes');
            return redirect()->route('pedidos-adocao')
                ->with('success', "Adoção de {$adocao->animal->nome} para {$adocao->adotante->name} aprovada!");

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro ao aceitar pedido: ' . $e->getMessage());
            return back()->with('error', 'Erro ao aceitar pedido.');
        }
    }

    // Recusar pedido de adoção
    public function recusarPedido(Adocao $adocao)
    {
        if (auth()->id() !== $adocao->doador_id) {
            return redirect()->route('pedidos-adocao')->with('error', 'Sem permissão.');
        }

        if ($adocao->status !== 'pendente') {
            return back()->with('error', 'Este pedido já foi respondido.');
        }


        try {
            $adocao->update(['status' => 'recusada']);

            cache()->forget('user_' . auth()->id() . '_pedidos_pendentes');
            return redirect()->route('pedidos-adocao')
                ->with('success', "Pedido de {$adocao->adotante->name} para {$adocao->animal->nome} recusado.");

        } catch (Exception $e) {
            Log::error('Erro ao recusar pedido: ' . $e->getMessage());
            return back()->with('error', 'Erro ao recusar pedido.');
        }
    }
    // Listar todos os pedidos do usuário (recebidos e feitos)
    public function meusPedidos()
    {
        $pedidosRecebidos = Adocao::where('doador_id', auth()->id())
            ->with(['animal.fotos', 'adotante'])
            ->orderBy('created_at', 'desc')
            ->get();

        $pedidosFeitos = Adocao::where('adotante_id', auth()->id())
            ->with(['animal.fotos', 'doador'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('adocoes.meus-pedidos', compact('pedidosRecebidos', 'pedidosFeitos'));
    }

    // Visualizar pedido específico (feito pelo usuário)
    public function verMeuPedido(Adocao $adocao)
    {
        // Verificar se o usuário é o adotante
        if (auth()->id() !== $adocao->adotante_id && auth()->id() !== $adocao->doador_id) {
            return redirect()->route('meus-pedidos')->with('error', 'Sem permissão.');
        }

        $adocao->load(['animal.fotos', 'adotante', 'doador']);

        return view('adocoes.ver-meu-pedido', compact('adocao'));
    }
}
