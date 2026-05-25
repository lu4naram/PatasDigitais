@extends('layout')

@section('conteudo')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Detalhes do Pedido de Adoção</h2>
        <a href="{{ route('meus-pedidos') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>

    @php
        $isDoador = auth()->id() === $adocao->doador_id;
        $outroUser = $isDoador ? $adocao->adotante : $adocao->doador;
        
        $statusClass = [
            'pendente' => 'warning',
            'aprovada' => 'success',
            'recusada' => 'danger',
        ][$adocao->status] ?? 'secondary';
        
        $statusIcon = [
            'pendente' => 'hourglass-split',
            'aprovada' => 'check-circle',
            'recusada' => 'x-circle',
        ][$adocao->status] ?? 'question-circle';
    @endphp

    <div class="row">
        <!-- Coluna da esquerda - Foto do pet -->
        <div class="col-md-5">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-paw"></i> Sobre o Pet</h5>
                </div>
                <div class="card-body">
                    @php
                        $foto = $adocao->animal->fotos->first()->caminho ?? 'sem-imagem.jpg';
                    @endphp
                    <img src="{{ asset('storage/' . $foto) }}" 
                         alt="Foto de {{ $adocao->animal->nome }}" 
                         class="img-fluid rounded mb-3">
                    
                    <h4>{{ $adocao->animal->nome }}</h4>
                    <p class="text-muted">{{ $adocao->animal->especie->nome ?? 'Espécie não informada' }}</p>
                    
                    <hr>
                    <p><strong>Idade:</strong> {{ $adocao->animal->idade_formatada }}</p>
                    <p><strong>Sexo:</strong> {{ $adocao->animal->sexo == 'macho' ? 'Macho' : 'Fêmea' }}</p>
                    <p><strong>Castrado:</strong> {{ $adocao->animal->castracao ? 'Sim' : 'Não' }}</p>
                    
                    <div class="mt-3">
                        <a href="{{ route('animais.show', $adocao->animal->id) }}" class="btn btn-outline-info w-100">
                            <i class="bi bi-eye"></i> Ver anúncio completo
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Coluna da direita - Informações do pedido -->
        <div class="col-md-7">
            <div class="card mb-3">
                <div class="card-header bg-{{ $statusClass }} text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-{{ $statusIcon }}"></i> 
                        Status: 
                        @if($adocao->status === 'pendente')
                            Aguardando resposta
                        @elseif($adocao->status === 'aprovada')
                            Aprovado!
                        @elseif($adocao->status === 'recusada')
                            Recusado
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    <h4>{{ $outroUser->name }}</h4>
                    <p><strong>Email:</strong> {{ $outroUser->email }}</p>
                    
                    @if($adocao->mensagem)
                        <div class="alert alert-light">
                            <strong><i class="bi bi-chat-quote"></i> Mensagem:</strong>
                            <p class="mt-2 mb-0">{{ $adocao->mensagem }}</p>
                        </div>
                    @endif
                    
                    <p class="text-muted small">
                        <i class="bi bi-calendar"></i> Solicitação feita em: {{ $adocao->created_at->format('d/m/Y \à\s H:i') }}
                    </p>
                    
                    @if($adocao->status === 'aprovada' && $adocao->data_adocao)
                        <p class="text-muted small">
                            <i class="bi bi-check-circle"></i> Aprovada em: {{ \Carbon\Carbon::parse($adocao->data_adocao)->format('d/m/Y H:i') }}
                        </p>
                    @endif
                </div>
            </div>
            
            <!-- Botões de ação (apenas para doador com pedido pendente) -->
            @if($isDoador && $adocao->status === 'pendente')
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex gap-3">
                            <form action="{{ route('pedidos-adocao.aceitar', $adocao) }}" method="POST" class="flex-grow-1">
                                @csrf
                                <button type="submit" class="btn btn-success w-100" 
                                        onclick="return confirm('Tem certeza que deseja ACEITAR a adoção de {{ $adocao->animal->nome }} para {{ $outroUser->name }}?')">
                                    <i class="bi bi-check-lg"></i> Aceitar Adoção
                                </button>
                            </form>
                            
                            <form action="{{ route('pedidos-adocao.recusar', $adocao) }}" method="POST" class="flex-grow-1">
                                @csrf
                                <button type="submit" class="btn btn-danger w-100"
                                        onclick="return confirm('Tem certeza que deseja RECUSAR a adoção de {{ $adocao->animal->nome }} para {{ $outroUser->name }}?')">
                                    <i class="bi bi-x-lg"></i> Recusar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
            
            <div class="mt-3">
                <a href="{{ route('conversas.show', ['animal' => $adocao->animal->id, 'doador' => $outroUser->id]) }}" 
                   class="btn btn-primary w-100">
                    <i class="bi bi-chat-dots"></i> Conversar com {{ $outroUser->name }}
                </a>
            </div>
        </div>
    </div>
@endsection