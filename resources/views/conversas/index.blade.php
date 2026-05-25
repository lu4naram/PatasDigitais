@extends('layout')

@section('conteudo')
    <h2 class="mb-4">Conversas</h2>
    
    @if($conversas->count() > 0)
        <div class="list-group">
            @foreach($conversas as $conversa)
                @php
                    $outroUser = $conversa->outroUsuario(auth()->id());
                    $ultimaMensagem = $conversa->ultima_mensagem;
                    $naoLidas = $conversa->mensagensNaoLidas(auth()->id());
                    $animal = $conversa->animal;
                    $foto = $animal->fotos->first();
                    $fotoUrl = $foto ? \App\Helpers\ImageHelper::url($foto->caminho) : asset('images/sem-imagem.jpg');
                @endphp
                <a href="{{ route('conversas.show', ['animal' => $conversa->animal_id, 'doador' => $outroUser->id]) }}" 
                   class="list-group-item list-group-item-action">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $fotoUrl }}" class="card-img-top" alt="Foto de {{ $animal->nome }}" 
                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                            <div>
                                <strong>{{ $conversa->animal->nome }}</strong>
                                <br>
                                <small class="text-muted">com {{ $outroUser->name }}</small>
                                @if($ultimaMensagem)
                                    <br>
                                    <small class="text-muted">{{ Str::limit($ultimaMensagem->mensagem, 40) }}</small>
                                @endif
                            </div>
                        </div>
                        @if($naoLidas > 0)
                            <span class="badge bg-danger rounded-pill">{{ $naoLidas }}</span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-chat-dots fs-1 text-muted"></i>
            <p class="mt-3 text-muted">Você ainda não tem nenhuma conversa.</p>
            <p class="text-muted">Quando alguém se interessar por um dos seus animais ou você solicitar uma adoção, a conversa aparecerá aqui.</p>
        </div>
    @endif
@endsection
