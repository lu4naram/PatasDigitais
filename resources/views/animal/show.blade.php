@extends('layout')

@section('conteudo')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Detalhes do Animal</h2>
            <div class="d-flex gap-3">
                @auth
                    @if(auth()->user()->isAdmin() || auth()->id() === $animal->user_id)
                        <a href="{{ route('animais.edit', $animal->id) }}" class="btn btn-warning">
                            <i class="bi bi-pencil-fill"></i> Editar
                        </a>
                    @endif
                @endauth
                <a href="{{ route('animais.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
            </div>
        </div>

        <!-- Alertas -->
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

        <div class="animal-detail-card">
            <div class="detail-content-grid">

                <div class="foto-detail-area">
                    <div class="foto-principal">
                        @php
                            $fotoPrincipal = $animal->fotos->first();
                            $fotoPrincipalUrl = $fotoPrincipal ? Storage::disk('r2')->url($fotoPrincipal->caminho) : '';
                        @endphp

                        @if($fotoPrincipalUrl)
                            <img src="{{ $fotoPrincipalUrl }}" alt="Foto de {{ $animal->nome }}" id="fotoPrincipal">
                        @endif
                    </div>

                    @if($animal->fotos->count() > 1)
                        <div class="foto-miniaturas">
                            @foreach($animal->fotos as $foto)
                                @php
                                    $fotoUrl = Storage::disk('r2')->url($foto->caminho);
                                @endphp
                                <img src="{{ $fotoUrl }}" class="foto-miniatura"
                                    onclick="document.getElementById('fotoPrincipal').src = this.src">
                            @endforeach
                        </div>
                    @endif
                    <small class="text-muted">* Clique nas miniaturas para trocar a foto principal</small>
                </div>

                <div class="info-detail-area">
                    <div class="info-group">
                        <label>Nome:</label>
                        <p class="info-value">{{ $animal->nome }}</p>
                    </div>

                    <div class="info-group">
                        <label>Sobre:</label>
                        <p class="info-value">{{ $animal->sobre ?: 'Não informado' }}</p>
                    </div>

                    <div class="linha-detail">
                        <div class="info-group">
                            <label>Idade:</label>
                            <p class="info-value">{{ $animal->idade_formatada }}</p>
                        </div>
                        <div class="info-group">
                            <label>Sexo:</label>
                            <p class="info-value">{{ $animal->sexo == 'macho' ? 'Macho' : 'Fêmea' }}</p>
                        </div>
                        <div class="info-group">
                            <label>Castrado:</label>
                            <p class="info-value">{{ $animal->castracao ? 'Sim' : 'Não' }}</p>
                        </div>
                        <div class="info-group">
                            <label>Espécie:</label>
                            <p class="info-value">{{ $animal->especie->nome ?? 'Não informada' }}</p>
                        </div>
                    </div>

                    <div class="info-group">
                        <label>Vacinas:</label>
                        <div class="vacinas-tags-detail">
                            @if($animal->vacinas->count() > 0)
                                @foreach($animal->vacinas as $vacina)
                                    <span class="tag-vacina-detail">{{ $vacina->nome }}</span>
                                @endforeach
                            @else
                                <p class="info-value">Nenhuma vacina registrada</p>
                            @endif
                        </div>
                    </div>

                    <div class="status-group">
                        <label>Status de Adoção:</label>
                        <p class="status-badge {{ $animal->adotado ? 'status-adotado' : 'status-disponivel' }}">
                            {{ $animal->adotado ? 'Adotado' : 'Disponível para adoção' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Botões de ação inferiores -->
            @auth

                {{-- Usuário comum (não é o dono do animal) --}}
                @if(auth()->id() !== $animal->user_id && !$animal->adotado)

                    <div class="d-flex justify-content-end gap-3 mt-4 pt-3 border-top">

                        <!-- Botão Favoritar -->
                        <form action="{{ route('favoritar', $animal) }}" method="POST">
                            @csrf

                            <button type="submit"
                                class="btn {{ $animal->foiSalvoPor(auth()->id()) ? 'btn-success' : 'btn-outline-secondary' }}">

                                <i class="bi bi-bookmark{{ $animal->foiSalvoPor(auth()->id()) ? '-fill' : '' }}"></i>

                                {{ $animal->foiSalvoPor(auth()->id()) ? 'Salvo' : 'Salvar' }}
                            </button>
                        </form>

                        <!-- Botão Adotar -->
                        <form action="{{ route('solicitar-adocao', $animal) }}" method="POST"
                            onsubmit="return showAdocaoModal(event, '{{ $animal->nome }}')">

                            @csrf

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-heart-fill"></i> Adotar
                            </button>
                        </form>

                        <!-- Botão Conversa -->
                        @if($animal->solicitacaoPendentePor(auth()->id()))
                            <a href="{{ route('conversas.show', [
                                'animal' => $animal->id,
                                'doador' => $animal->user_id
                            ]) }}" class="btn btn-info">

                                <i class="bi bi-chat-dots"></i>
                            </a>
                        @endif

                    </div>

                @endif


                {{-- Dono do anúncio --}}
                @if(auth()->id() === $animal->user_id && !$animal->adotado)

                    <div class="d-flex justify-content-end gap-3 mt-4 pt-3 border-top">

                        <form action="{{ route('animais.destroy', $animal->id) }}" method="POST"
                            onsubmit="return confirm('Tem certeza que deseja excluir este anúncio?')">

                            @csrf
                            @method('DELETE')

                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash-fill"></i> Excluir
                            </button>
                        </form>

                    </div>

                @endif

            @endauth


            @guest

                <div class="d-flex justify-content-end gap-3 mt-4 pt-3 border-top">

                    <a href="{{ route('login') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-bookmark"></i> Salvar (faça login)
                    </a>

                    <a href="{{ route('login') }}" class="btn btn-primary">
                        <i class="bi bi-heart-fill"></i> Adotar (faça login)
                    </a>

                </div>

            @endguest
        </div>
    </div>
@endsection

<!-- Modal para confirmação de adoção -->
<div class="modal fade" id="adocaoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Solicitação de Adoção</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Você está prestes a solicitar a adoção de <strong id="animalNome"></strong>.</p>
                <p class="text-muted">O anunciante receberá sua solicitação e poderá entrar em contato.</p>
                <div class="mb-3">
                    <label for="mensagem" class="form-label">Mensagem para o anunciante (opcional):</label>
                    <textarea id="mensagem" class="form-control" rows="3"
                        placeholder="Diga um pouco sobre você e por que quer adotar..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmarAdocao">Confirmar Solicitação</button>
            </div>
        </div>
    </div>
</div>