import "./bootstrap";

document.addEventListener("DOMContentLoaded", () => {
    const modalAnimal = document.getElementById("modalAnimal");
    const btnNovoRegistro = document.getElementById("btnNovoRegistro");
    const btnFecharModal = document.querySelector("#modalAnimal .btn-close");

    const fotosInput = document.getElementById("fotosInput");
    const previewContainer = document.getElementById("previewFotos");

    const especieSelect = document.getElementById("especieSelect");
    const vacinasInput = document.getElementById("vacinasInput");
    const vacinasTags = document.getElementById("vacinasTags");
    const vacinasHidden = document.getElementById("vacinasHidden");
    const vacinasDropdown = document.getElementById("vacinasDropdown");
    const vacinasBox = document.getElementById("vacinasBox");

    let fotosSelecionadas = [];
    let vacinasSelecionadas = [];
    let todasVacinas = [];

    const openModal = () => modalAnimal && (modalAnimal.style.display = "flex");
    const closeModal = () =>
        modalAnimal && (modalAnimal.style.display = "none");

    if (btnNovoRegistro) btnNovoRegistro.addEventListener("click", openModal);
    if (btnFecharModal) btnFecharModal.addEventListener("click", closeModal);

    window.abrirModal = openModal;
    window.fecharModal = closeModal;

    const renderFotos = () => {
        if (!previewContainer) return;
        previewContainer.innerHTML = "";
        fotosSelecionadas.forEach((foto, i) => {
            const img = document.createElement("img");
            img.src = foto.src;
            img.className = "preview-foto-item";
            if (i === 0) img.classList.add("capa");
            img.onclick = () => {
                if (i === 0) return;
                const item = fotosSelecionadas.splice(i, 1)[0];
                fotosSelecionadas.unshift(item);
                renderFotos();
            };
            previewContainer.appendChild(img);
        });
    };

    if (fotosInput) {
        fotosInput.addEventListener("change", (e) => {
            Array.from(e.target.files).forEach((file) => {
                const reader = new FileReader();
                reader.onload = (ev) => {
                    fotosSelecionadas.push({ src: ev.target.result, file });
                    renderFotos();
                };
                reader.readAsDataURL(file);
            });
        });
    }

    async function carregarVacinas(especieId) {
        if (!especieId) {
            todasVacinas = [];
            return;
        }

        try {
            const res = await fetch(`/buscar-vacinas/${especieId}`);
            todasVacinas = res.ok ? await res.json() : [];
        } catch {
            todasVacinas = [];
        }
    }

    const atualizarTags = () => {
        if (!vacinasTags) return;
        vacinasTags.innerHTML = "";

        vacinasSelecionadas.forEach((nome) => {
            const tag = document.createElement("span");
            tag.className = "tag-vacina";

            const text = document.createElement("span");
            text.textContent = nome;

            const remove = document.createElement("span");
            remove.textContent = "✖";
            remove.className = "remove-vacina";
            remove.onclick = () => {
                vacinasSelecionadas = vacinasSelecionadas.filter(
                    (v) => v !== nome,
                );
                atualizarTags();
                if (vacinasHidden)
                    vacinasHidden.value = JSON.stringify(vacinasSelecionadas);
            };

            tag.appendChild(text);
            tag.appendChild(remove);
            vacinasTags.appendChild(tag);
        });
    };

    const renderDropdown = (filter = "") => {
        if (!vacinasDropdown) return;

        if (!todasVacinas.length) {
            vacinasDropdown.style.display = "none";
            return;
        }

        vacinasDropdown.innerHTML = "";

        const lista = todasVacinas.filter(
            (v) =>
                v.nome.toLowerCase().includes(filter.toLowerCase()) &&
                !vacinasSelecionadas.includes(v.nome),
        );

        if (!lista.length) {
            vacinasDropdown.style.display = "none";
            return;
        }

        lista.forEach((v) => {
            const item = document.createElement("div");
            item.className = "vacinas-dropdown-item";
            item.textContent = v.nome;

            item.onclick = () => {
                vacinasSelecionadas.push(v.nome);
                atualizarTags();
                if (vacinasHidden)
                    vacinasHidden.value = JSON.stringify(vacinasSelecionadas);
                if (vacinasInput) vacinasInput.value = "";
                vacinasDropdown.style.display = "none";
            };

            vacinasDropdown.appendChild(item);
        });

        vacinasDropdown.style.display = "block";
    };

    if (vacinasInput) {
        vacinasInput.addEventListener("focus", () => {
            if (especieSelect?.value) renderDropdown(vacinasInput.value);
        });

        vacinasInput.addEventListener("input", () => {
            if (especieSelect?.value) renderDropdown(vacinasInput.value);
        });

        vacinasInput.addEventListener("blur", () => {
            setTimeout(() => (vacinasDropdown.style.display = "none"), 150);
        });
    }

    document.addEventListener("click", (e) => {
        if (vacinasBox && !vacinasBox.contains(e.target)) {
            vacinasDropdown.style.display = "none";
        }
    });

    if (especieSelect) {
        especieSelect.addEventListener("change", async () => {
            vacinasSelecionadas = [];
            atualizarTags();
            if (vacinasHidden) vacinasHidden.value = "";
            if (vacinasInput) vacinasInput.value = "";

            await carregarVacinas(especieSelect.value);

            if (vacinasInput === document.activeElement) {
                renderDropdown("");
            } else {
                vacinasDropdown.style.display = "none";
            }
        });
    }

    // ========== CARREGAR VACINAS NA EDIÇÃO ==========
    // Verifica se está na página de edição (form tem data-vacinas)
    const formAnimal = document.getElementById("formAnimal");
    if (formAnimal && formAnimal.dataset.vacinas) {
        try {
            const vacinasDoAnimal = JSON.parse(formAnimal.dataset.vacinas);
            vacinasSelecionadas = Array.isArray(vacinasDoAnimal)
                ? vacinasDoAnimal
                : [];
            atualizarTags();
            if (vacinasHidden)
                vacinasHidden.value = JSON.stringify(vacinasSelecionadas);
        } catch (e) {
            console.error("Erro ao carregar vacinas:", e);
        }
    }

    window.removerVacina = (nome) => {
        vacinasSelecionadas = vacinasSelecionadas.filter((v) => v !== nome);
        atualizarTags();
        if (vacinasHidden)
            vacinasHidden.value = JSON.stringify(vacinasSelecionadas);
    };

    window.removerFoto = (btn, fotoId) => {
        if (!confirm("Tem certeza que deseja remover esta foto?")) return;

        const input = document.createElement("input");
        input.type = "hidden";
        input.name = "fotos_remover[]";
        input.value = fotoId;

        const form = document.getElementById("formAnimal");
        if (form) form.appendChild(input);

        btn.closest(".preview-foto-item-wrapper")?.remove();
    };

    if (especieSelect?.value) {
        carregarVacinas(especieSelect.value);
    }

    // Adicione ao seu app.js

function formatarIdade(meses) {
    if (meses <= 0) return 'Menos de 1 mês';
    
    const anos = Math.floor(meses / 12);
    const mesesRestantes = meses % 12;
    
    let texto = '';
    if (anos > 0) {
        texto += anos + ' ' + (anos === 1 ? 'ano' : 'anos');
    }
    if (mesesRestantes > 0) {
        if (anos > 0) texto += ' e ';
        texto += mesesRestantes + ' ' + (mesesRestantes === 1 ? 'mês' : 'meses');
    }
    
    return texto;
}

// Event listener para atualizar o texto em tempo real
document.addEventListener('DOMContentLoaded', function() {
    const idadeInput = document.getElementById('idade');
    
    if (idadeInput) {
        const textoDiv = document.getElementById('idadeTexto');
        
        const atualizarTexto = () => {
            const valor = parseInt(idadeInput.value) || 0;
            textoDiv.textContent = formatarIdade(valor);
        };
        
        idadeInput.addEventListener('input', atualizarTexto);
        atualizarTexto(); // valor inicial
    }
});

// ========== MODAL DE ADOÇÃO ==========
let adocaoForm = null;

window.showAdocaoModal = function(event, animalNome) {
    event.preventDefault();
    adocaoForm = event.target;
    
    // Verificar se o modal existe, se não, criar dinamicamente
    let modal = document.getElementById('adocaoModal');
    
    if (!modal) {
        // Criar modal se não existir
        modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.id = 'adocaoModal';
        modal.tabIndex = '-1';
        modal.innerHTML = `
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
                            <textarea id="mensagem" class="form-control" rows="3" placeholder="Diga um pouco sobre você e por que quer adotar..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="confirmarAdocao">Confirmar Solicitação</button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }
    
    document.getElementById('animalNome').textContent = animalNome;
    
    // Inicializar e mostrar modal com Bootstrap
    const modalElement = document.getElementById('adocaoModal');
    const modalInstance = new bootstrap.Modal(modalElement);
    modalInstance.show();
    
    // Configurar botão de confirmação
    const confirmarBtn = document.getElementById('confirmarAdocao');
    const oldConfirm = confirmarBtn.cloneNode(true);
    confirmarBtn.parentNode.replaceChild(oldConfirm, confirmarBtn);
    
    oldConfirm.addEventListener('click', function() {
        const mensagem = document.getElementById('mensagem').value;
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'mensagem';
        input.value = mensagem;
        adocaoForm.appendChild(input);
        adocaoForm.submit();
    });
    
    return false;
};

// Limpar mensagem ao fechar o modal
document.addEventListener('DOMContentLoaded', function() {
    const modalElement = document.getElementById('adocaoModal');
    if (modalElement) {
        modalElement.addEventListener('hidden.bs.modal', function() {
            const mensagemTextarea = document.getElementById('mensagem');
            if (mensagemTextarea) {
                mensagemTextarea.value = '';
            }
        });
    }
});

});

