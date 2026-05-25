@extends('layout')

@section('conteudo')
    <!-- Modal para criar espécie -->
<div id="modalCriarEspecie" class="modal-overlay" style="display:none;">
    <div class="modal-box">
        <h2>Adicionar Espécie</h2>
        <form id="formCriarEspecie" method="POST" action="{{ route('especies.store') }}">
            @csrf
            <div class="mb-3">
                <label for="nome" class="form-label">Espécie:</label>
                <input type="text" id="nome" name="nome" class="form-control" required>
            </div>
            <div class="modal-buttons">
                <button type="button" class="btn btn-cancel" id="btnCancelar">Cancelar</button>
                <button type="submit" class="btn btn-confirm">Confirmar</button>
            </div>
        </form>
    </div>
</div>

@endsection