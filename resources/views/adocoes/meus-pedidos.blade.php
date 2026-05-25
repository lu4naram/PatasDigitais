@extends('layout')

@section('conteudo')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Meus Pedidos de Adoção</h2>
        <a href="{{ route('animais.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Abas -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#recebidos" type="button" role="tab">
                <i class="bi bi-inbox"></i> Recebidos 
                @if($pedidosRecebidos->where('status', 'pendente')->count() > 0)
                    <span class="badge bg-danger">{{ $pedidosRecebidos->where('status', 'pendente')->count() }}</span>
                @endif
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#feitos" type="button" role="tab">
                <i class="bi bi-send"></i> Feitos
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- TAB: PEDIDOS RECEBIDOS (sou doador) -->
        <div class="tab-pane fade show active" id="recebidos" role="tabpanel">
            @if($pedidosRecebidos->count() > 0)
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    @foreach($pedidosRecebidos as $pedido)
                        @php
                            $animal = $pedido->animal;
                            $adotante = $pedido->adotante;
                            $foto = $animal->fotos->first();
                            $fotoUrl = $foto ? \App\Helpers\ImageHelper::url($foto->caminho) : asset('images/sem-imagem.jpg');
                            
                            $statusClass = [
                                'pendente' => 'warning',
                                'aprovada' => 'success',
                                'recusada' => 'danger',
                                'cancelada' => 'secondary',
                            ][$pedido->status] ?? 'secondary';
                            
                            $statusText = [
                                'pendente' => 'Pendente',
                                'aprovada' => 'Aprovada',
                                'recusada' => 'Recusada',
                                'cancelada' => 'Cancelada',
                            ][$pedido->status] ?? $pedido->status;
                        @endphp
                        
                        <div class="col">
                            <div class="card h-100 shadow-sm">
                                <img src="{{ $fotoUrl }}" class="card-img-top" alt="Foto de {{ $animal->nome }}" style="height: 180px; object-fit: cover;">
                                
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title mb-0">{{ $animal->nome }}</h5>
                                        <span class="badge bg-{{ $statusClass }}">{{ $statusText }}</span>
                                    </div>
                                    
                                    <p class="card-text">
                                        <strong>{{ $adotante->name }}</strong> quer adotar {{ $animal->nome }}!
                                    </p>
                                    
                                    @if($pedido->mensagem)
                                        <p class="card-text small text-muted">
                                            <i class="bi bi-chat-quote"></i> "{{ Str::limit($pedido->mensagem, 60) }}"
                                        </p>
                                    @endif
                                    
                                    <p class="card-text small text-muted">
                                        <i class="bi bi-calendar"></i> {{ $pedido->created_at->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                                
                                <div class="card-footer d-flex justify-content-between gap-2">
                                    <a href="{{ route('meus-pedidos.ver', $pedido) }}" class="btn btn-info btn-sm flex-grow-1">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                    
                                    <a href="{{ route('conversas.show', ['animal' => $animal->id, 'doador' => $adotante->id]) }}" 
                                       class="btn btn-primary btn-sm flex-grow-1">
                                        <i class="bi bi-chat-dots"></i> Conversar
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <p class="mt-3 text-muted">Você não recebeu nenhum pedido de adoção.</p>
                </div>
            @endif
        </div>

        <!-- TAB: PEDIDOS FEITOS (sou adotante) -->
        <div class="tab-pane fade" id="feitos" role="tabpanel">
            @if($pedidosFeitos->count() > 0)
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    @foreach($pedidosFeitos as $pedido)
                        @php
                            $animal = $pedido->animal;
                            $doador = $pedido->doador;
                            $foto = $animal->fotos->first();
                            $fotoUrl = $foto ? \App\Helpers\ImageHelper::url($foto->caminho) : asset('images/sem-imagem.jpg');
                            
                            $statusClass = [
                                'pendente' => 'warning',
                                'aprovada' => 'success',
                                'recusada' => 'danger',
                                'cancelada' => 'secondary',
                            ][$pedido->status] ?? 'secondary';
                            
                            $statusText = [
                                'pendente' => 'Aguardando resposta',
                                'aprovada' => 'Aprovado!',
                                'recusada' => 'Recusado',
                                'cancelada' => 'Cancelado',
                            ][$pedido->status] ?? $pedido->status;
                        @endphp
                        
                        <div class="col">
                            <div class="card h-100 shadow-sm">
                                <img src="{{ $fotoUrl }}" class="card-img-top" alt="Foto de {{ $animal->nome }}" style="height: 180px; object-fit: cover;">
                                
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title mb-0">{{ $animal->nome }}</h5>
                                        <span class="badge bg-{{ $statusClass }}">{{ $statusText }}</span>
                                    </div>
                                    
                                    <p class="card-text">
                                        Você solicitou a adoção de <strong>{{ $animal->nome }}</strong>
                                    </p>
                                    <p class="card-text small text-muted">
                                        Anunciante: <strong>{{ $doador->name }}</strong>
                                    </p>
                                    
                                    @if($pedido->status === 'aprovada')
                                        <div class="alert alert-success py-1 px-2 mt-2">
                                            <small><i class="bi bi-check-circle"></i> Adoção aprovada! Entre em contato com o doador.</small>
                                        </div>
                                    @elseif($pedido->status === 'recusada')
                                        <div class="alert alert-danger py-1 px-2 mt-2">
                                            <small><i class="bi bi-x-circle"></i> Adoção recusada. Tente outros animais.</small>
                                        </div>
                                    @endif
                                    
                                    <p class="card-text small text-muted mt-2">
                                        <i class="bi bi-calendar"></i> Solicitado em: {{ $pedido->created_at->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                                
                                <div class="card-footer d-flex justify-content-between gap-2">
                                    <a href="{{ route('meus-pedidos.ver', $pedido) }}" class="btn btn-info btn-sm flex-grow-1">
                                        <i class="bi bi-eye"></i> Ver detalhes
                                    </a>
                                    
                                    <a href="{{ route('conversas.show', ['animal' => $animal->id, 'doador' => $doador->id]) }}" 
                                       class="btn btn-primary btn-sm flex-grow-1">
                                        <i class="bi bi-chat-dots"></i> Conversar
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-send fs-1 text-muted"></i>
                    <p class="mt-3 text-muted">Você ainda não fez nenhum pedido de adoção.</p>
                    <a href="{{ route('animais.index') }}" class="btn btn-primary">Explorar animais</a>
                </div>
            @endif
        </div>
    </div>
@endsection
