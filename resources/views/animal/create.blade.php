<form action="{{ route('animais.store') }}" method="POST" enctype="multipart/form-data" id="formAnimal">
    @csrf

    <div class="modal-content-grid">
        <div class="foto-upload-area">
            <div class="foto-upload" id="fotoUploadArea">
                <label class="foto-box">
                    <span>+ adicionar foto</span>
                    <input type="file" name="fotos[]" id="fotosInput" hidden multiple accept="image/*">
                </label>
            </div>
            <div id="previewFotos" class="preview-fotos"></div>
            <small class="text-muted">* A primeira foto será a capa</small>
        </div>

        <div class="form-area">
            <input type="text" name="nome" placeholder="Nome do animal" required>

            <textarea name="sobre" rows="4" placeholder="Sobre o animal..."></textarea>

            <div class="linha">
                <div class="campo idade-campo">
                    <label>Idade (meses):</label>
                    <input type="number" name="idade" id="idade" class="idade-number-input" value="0" min="0"
                        max="360" step="1">
                    <div id="idadeTexto" class="idade-texto"></div>
                </div>

                <div class="campo sexo-campo">
                    <label>Sexo:</label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="sexo" value="macho" checked> Macho
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="sexo" value="femea"> Fêmea
                        </label>
                    </div>
                </div>

                <div class="campo radio-campo">
                    <label>Castrado:</label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="castracao" value="0" checked> Não
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="castracao" value="1"> Sim
                        </label>
                    </div>
                </div>

                <div class="campo especie-campo">
                    <label>Espécie:</label>
                    <select name="especie_id" id="especieSelect" required>
                        <option value="">Selecione</option>
                        @foreach($especies as $especie)
                            <option value="{{ $especie->id }}">{{ $especie->nome }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="vacinas-container">
                <label>Vacinas:</label>
                <div class="vacinas-box" id="vacinasBox">
                    <div id="vacinasTags" class="vacinas-tags">
                    </div>
                    <div class="vacinas-input-wrapper">
                        <input type="text" id="vacinasInput" placeholder="Selecione uma vacina" class="vacinas-input"
                            autocomplete="off">
                        <div id="vacinasDropdown" class="vacinas-dropdown" style="display: none;">
                        </div>
                    </div>
                </div>
                <input type="hidden" name="vacinas" id="vacinasHidden" value="">
            </div>

            <button type="submit" class="btn-postar">Postar Anúncio</button>
        </div>
    </div>
</form>