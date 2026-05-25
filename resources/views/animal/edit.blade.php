@extends('layout')

@section('conteudo')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Editar Animal</h2>
            <a href="{{ url()->previous() }}" class="btn btn-secondary"> <i class="bi bi-arrow-left"></i> Voltar</a>
        </div>

        <div class="animal-edit-card">
            <form action="{{ route('animais.update', $animal->id) }}" method="POST" enctype="multipart/form-data"
                id="formAnimal" data-vacinas='@json($animal->vacinas->pluck("nome"))'>
                @csrf
                @method('PUT')


                <div class="edit-content-grid">
                    <!-- Área de fotos -->
                    <div class="foto-edit-area">
                        <div class="foto-upload" id="fotoUploadArea">
                            <label class="foto-box">
                                <span>+ adicionar foto</span>
                                <input type="file" name="fotos[]" id="fotosInput" hidden multiple accept="image/*">
                            </label>
                        </div>
                        <div id="previewFotos" class="preview-fotos">
                            <!-- Fotos existentes -->
                            @foreach($animal->fotos as $foto)
    @php
        $foto = $animal->fotos->first();
                        $fotoUrl = $foto ? \App\Helpers\ImageHelper::url($foto->caminho) : asset('images/sem-imagem.jpg');
    @endphp
    <img src="{{ $fotoUrl }}" class="preview-foto-item {{ $loop->first ? 'capa' : '' }}">
@endforeach
                        </div>
                        <small class="text-muted">* A primeira foto será a capa</small>
                    </div>

                    <!-- Área do formulário -->
                    <div class="form-area">
                        <input type="text" name="nome" placeholder="Nome do animal" required value="{{ $animal->nome }}">

                        <textarea name="sobre" rows="4" placeholder="Sobre o animal...">{{ $animal->sobre }}</textarea>

                        <div class="linha">
                            <div class="campo idade-campo">
                                <label>Idade (meses):</label>
                                <input type="number" name="idade" id="idade" class="idade-number-input"
                                    value="{{ $animal->idade }}" min="0" max="360" step="1">
                                <div id="idadeTexto" class="idade-texto"></div>
                            </div>

                            <div class="campo radio-campo">
                                <label>Castrado:</label>
                                <div class="radio-group">
                                    <label class="radio-label">
                                        <input type="radio" name="castracao" value="0" {{ !$animal->castracao ? 'checked' : '' }}> Não
                                    </label>
                                    <label class="radio-label">
                                        <input type="radio" name="castracao" value="1" {{ $animal->castracao ? 'checked' : '' }}> Sim
                                    </label>
                                </div>
                            </div>

                            <div class="campo sexo-campo">
                                <label>Sexo:</label>
                                <div class="radio-group">
                                    <label class="radio-label">
                                        <input type="radio" name="sexo" value="macho" {{ $animal->sexo == 'macho' ? 'checked' : '' }}> Macho
                                    </label>
                                    <label class="radio-label">
                                        <input type="radio" name="sexo" value="femea" {{ $animal->sexo == 'femea' ? 'checked' : '' }}> Fêmea
                                    </label>
                                </div>
                            </div>

                            <div class="campo especie-campo">
                                <label>Espécie:</label>
                                <select name="especie_id" id="especieSelect" required>
                                    <option value="">Selecione</option>
                                    @foreach($especies as $especie)
                                        <option value="{{ $especie->id }}" {{ $animal->especie_id == $especie->id ? 'selected' : '' }}>
                                            {{ $especie->nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Box de vacinas -->
                        <div class="vacinas-container">
                            <label>Vacinas:</label>
                            <div class="vacinas-box" id="vacinasBox">
                                <div id="vacinasTags" class="vacinas-tags">
                                    <!-- Vacinas selecionadas aparecerão aqui -->
                                </div>
                                <div class="vacinas-input-wrapper">
                                    <input type="text" id="vacinasInput" placeholder="Selecione uma vacina"
                                        class="vacinas-input" autocomplete="off">
                                    <div id="vacinasDropdown" class="vacinas-dropdown" style="display: none;">
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="vacinas" id="vacinasHidden" value="">
                        </div>

                        <button type="submit" class="btn-postar">Atualizar Anúncio</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


@endsection