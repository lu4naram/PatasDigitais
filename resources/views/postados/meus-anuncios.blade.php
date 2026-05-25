@extends('layout')

@section('conteudo')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Meus Anúncios</h2>
        <button class="btn btn-success" id="btnNovoRegistro">
            <i class="bi bi-plus-lg"></i> Novo Anúncio
        </button>
    </div>

    @if($animais->count() > 0)
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            @foreach($animais as $animal)
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        @php
                            $foto = $animal->fotos->first();
                            $fotoUrl = $foto ? \App\Helpers\ImageHelper::url($foto->caminho) : asset('images/sem-imagem.jpg');
                            $idade = $animal->idade_formatada;
                        @endphp

                        <img src="{{ $fotoUrl }}" class="card-img-top" alt="Foto de {{ $animal->nome }}"
                            style="height: 180px; object-fit: cover;">

                        <div class="card-body">
                            <h5 class="card-title">{{ $animal->nome }}</h5>
                            <p class="card-text mb-1">
                                <strong>Idade:</strong> {{ $idade }}
                            </p>
                            <p class="card-text">
                                <strong>Status:</strong> 
                                <span class="badge {{ $animal->adotado ? 'bg-secondary' : 'bg-success' }}">
                                    {{ $animal->adotado ? 'Adotado' : 'Disponível' }}
                                </span>
                            </p>
                        </div>

                        <div class="card-footer d-flex justify-content-between">
                            <a href="{{ route('animais.show', $animal->id) }}" class="btn btn-info btn-sm">Ver</a>
                            <a href="{{ route('animais.edit', $animal->id) }}" class="btn btn-warning btn-sm">Editar</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-megaphone fs-1 text-muted"></i>
            <p class="mt-3 text-muted">Você ainda não tem nenhum anúncio.</p>
        </div>
    @endif

    <!-- MODAL (igual ao do index) -->
    <div id="modalAnimal" class="modal-overlay" style="display:none;">
        <div class="modal-animal">
            <button class="btn-close" onclick="fecharModal()">✖</button>
            <br>
            @include('animal.create', ['especies' => $especies, 'vacinas' => $vacinas])
        </div>
    </div>
@endsection
