@extends('layout')

@section('conteudo')
@php
    $animal = $conversa->animal;
    $foto = $animal->fotos->first();
    $fotoUrl = $foto ? \App\Helpers\ImageHelper::url($foto->caminho) : asset('images/sem-imagem.jpg');
@endphp
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <img src="{{ $fotoUrl }}" class="card-img-top" alt="Foto de {{ $animal->nome }}" 
                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 50%;">
            <div>
                <h2 class="mb-0">{{ $animal->nome }}</h2>
                <small class="text-muted">Conversa com {{ $doador->name }}</small>
            </div>
        </div>
        <a href="{{ route('conversas') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>
    
    <div class="card">
        <div class="card-body" style="height: 450px; overflow-y: auto;" id="mensagensContainer">
            @foreach($mensagens as $mensagem)
                <div class="mb-3 {{ $mensagem->remetente_id == auth()->id() ? 'text-end' : 'text-start' }}">
                    <div class="d-inline-block p-3 rounded {{ $mensagem->remetente_id == auth()->id() ? 'bg-primary text-white' : 'bg-light' }}" 
                         style="max-width: 70%;">
                        <small>{{ $mensagem->mensagem }}</small>
                        <br>
                        <small class="{{ $mensagem->remetente_id == auth()->id() ? 'text-white-50' : 'text-muted' }}" style="font-size: 10px;">
                            {{ $mensagem->created_at->format('H:i - d/m/Y') }}
                            @if($mensagem->lida && $mensagem->remetente_id != auth()->id())
                                <i class="bi bi-check2-all"></i>
                            @endif
                        </small>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="card-footer">
            <form action="{{ route('conversas.send', $conversa) }}" method="POST">
                @csrf
                <div class="input-group">
                    <input type="text" name="mensagem" class="form-control" placeholder="Digite sua mensagem..." required>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send"></i> Enviar
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        const container = document.getElementById('mensagensContainer');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
        
        // Auto-refresh a cada 10 segundos (opcional)
        setInterval(function() {
            location.reload();
        }, 10000);
    </script>
@endsection
