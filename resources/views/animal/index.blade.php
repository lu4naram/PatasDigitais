@extends('layout')

@section('conteudo')
    <div class="d-flex justify-content-end">
        @auth
            <button class="btn btn-success mb-3" id="btnNovoRegistro">
                Novo Registro
            </button>
        @endauth
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
        @foreach($animais as $animal)
            <div class="col">
                <div class="card h-100 shadow-sm">
                    @php
                        $foto = $animal->fotos->first();
                        $fotoUrl = $foto ? \App\Helpers\ImageHelper::url($foto->caminho) : asset('images/sem-imagem.jpg');
                    @endphp

                    <img src="{{ $fotoUrl }}" class="card-img-top" alt="Foto de {{ $animal->nome }}"
                        style="height: 180px; object-fit: cover;">

                    <div class="card-body">
                        <h5 class="card-title">{{ $animal->nome }}</h5>
                        <p class="card-text mb-1">
                            <strong>Idade:</strong> {{ $animal->idade_formatada }}
                        </p>
                        <p class="card-text">
                            <strong>Castrado:</strong> {{ $animal->castracao ? 'Sim' : 'Não' }}
                        </p>
                    </div>

                    <div class="card-footer d-flex">
                        <a href="{{ route('animais.show', $animal->id) }}" class="btn btn-info btn-sm ms-auto">
                            Ver
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- MODAL -->
    <div id="modalAnimal" class="modal-overlay" style="display:none;">
        <div class="modal-animal">
            <button class="btn-close" onclick="fecharModal()">✖</button>
            <br>
            @include('animal.create', ['especies' => $especies, 'vacinas' => $vacinas])
        </div>
    </div>
@endsection