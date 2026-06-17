// Caminhos corretos baseados na sua estrutura de pastas
const URL_API_AMBIENTE = () => sgmUrl('api/ambientes.php');
const URL_API_BLOCO = () => sgmUrl('api/api_bloco.php'); 

document.addEventListener("DOMContentLoaded", () => {
    listarAmbientes();
    listarBlocosParaSelect();
});

// ==========================================
// FUNÇÕES DE AMBIENTE
// ==========================================

async function listarAmbientes() {
    try {
        const response = await fetch(URL_API_AMBIENTE());
        const result = await response.json();
        const tbody = document.getElementById('tabelaAmbientesBody');
        tbody.innerHTML = '';

        if (result.success) {
            result.data.forEach(ambiente => {
                tbody.innerHTML += `
                    <tr>
                        <td>${ambiente.id_ambiente}</td>
                        <td>${ambiente.nome}</td>
                        <td>${ambiente.nome_bloco}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-warning me-2" onclick="abrirModalEditar(${ambiente.id_ambiente}, '${ambiente.nome}', ${ambiente.id_bloco})">
                                <i class="bi bi-pencil-square"></i> Editar
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="excluirAmbiente(${ambiente.id_ambiente})">
                                <i class="bi bi-trash"></i> Excluir
                            </button>
                        </td>
                    </tr>
                `;
            });
        } else {
            tbody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">${result.message}</td></tr>`;
        }
    } catch (error) {
        console.error("Erro ao buscar ambientes:", error);
    }
}

async function criarAmbiente() {
    const nome = document.getElementById('nomeAmbiente').value;
    const id_bloco = document.getElementById('blocoAmbiente').value;

    if (!nome || !id_bloco) {
        alert("Preencha o nome e selecione o bloco!");
        return;
    }

    try {
        const response = await fetch(URL_API_AMBIENTE(), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ nome: nome, id_bloco: id_bloco })
        });
        
        const result = await response.json();
        if (result.success) {
            alert(result.message);
            document.getElementById('formCriarAmbiente').reset();
            document.getElementById('closeModalAmbiente').click();
            listarAmbientes();
        } else {
            alert("Erro: " + result.message);
        }
    } catch (error) {
        console.error("Erro:", error);
    }
}

function mostrarChamadosVinculados(mensagem, chamados) {
    const tbody = document.getElementById('chamadosVinculadosBody');
    const msg = document.getElementById('chamadosVinculadosMsg');
    if (!tbody) { alert(mensagem); return; }
    msg.innerText = mensagem;
    tbody.innerHTML = chamados.map(c => `
        <tr>
            <td>#${c.id_chamado}</td>
            <td>${c.descricao_problema.substring(0, 50)}${c.descricao_problema.length > 50 ? '...' : ''}</td>
            <td>${c.status}</td>
            <td>${c.ambiente_nome || c.solicitante_nome || c.prioridade || '-'}</td>
            <td><a href="gestor_detalhes.php?id=${c.id_chamado}" class="btn btn-sm btn-light">Ver</a></td>
        </tr>`).join('');
    
    // Fecha o modal de gerenciamento se estiver aberto para não sobrepor
    const modalGerenciar = bootstrap.Modal.getInstance(document.getElementById('modalGerenciarBlocos'));
    if (modalGerenciar) modalGerenciar.hide();

    new bootstrap.Modal(document.getElementById('modalChamadosVinculados')).show();
}

async function excluirAmbiente(id_ambiente) {
    if (confirm("Deseja realmente excluir este ambiente?")) {
        try {
            const response = await fetch(URL_API_AMBIENTE(), {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_ambiente: id_ambiente })
            });
            const result = await response.json();
            if (result.success) {
                listarAmbientes();
            } else if (result.chamados_vinculados && result.chamados_vinculados.length) {
                mostrarChamadosVinculados(result.message, result.chamados_vinculados);
            } else {
                alert("Erro: " + result.message);
            }
        } catch (error) {
            console.error("Erro:", error);
        }
    }
}

// ==========================================
// FUNÇÕES DE EDIÇÃO DE AMBIENTE (NOVO)
// ==========================================

// Função que preenche o modal com os dados atuais e o abre
function abrirModalEditar(id, nome, id_bloco) {
    document.getElementById('editIdAmbiente').value = id;
    document.getElementById('editNomeAmbiente').value = nome;
    document.getElementById('editBlocoAmbiente').value = id_bloco;
    
    // Mostra o modal usando o Bootstrap via JavaScript
    const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEditarAmbiente'));
    modal.show();
}

// Função que envia as alterações para o banco via PUT
async function salvarEdicaoAmbiente() {
    const id = document.getElementById('editIdAmbiente').value;
    const nome = document.getElementById('editNomeAmbiente').value;
    const id_bloco = document.getElementById('editBlocoAmbiente').value;

    if (!nome || !id_bloco) {
        alert("Preencha todos os campos para atualizar!");
        return;
    }

    try {
        const response = await fetch(URL_API_AMBIENTE(), {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_ambiente: id, nome: nome, id_bloco: id_bloco })
        });
        
        const result = await response.json();
        if (result.success) {
            alert(result.message);
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalEditarAmbiente'));
            if (modal) modal.hide();
            listarAmbientes(); // Atualiza a tabela com o novo nome
        } else {
            alert("Erro: " + result.message);
        }
    } catch (error) {
        console.error("Erro ao atualizar ambiente:", error);
    }
}

// ==========================================
// FUNÇÕES DE BLOCO
// ==========================================

async function listarBlocosParaSelect() {
    try {
        const response = await fetch(URL_API_BLOCO());
        const result = await response.json();
        const selectCriar = document.getElementById('blocoAmbiente');
        const selectEditar = document.getElementById('editBlocoAmbiente'); // Select do modal de edição
        
        const optionDefault = '<option value="" selected disabled>Selecione o bloco</option>';
        selectCriar.innerHTML = optionDefault;
        selectEditar.innerHTML = optionDefault;

        if (result.success && result.data) {
            result.data.forEach(bloco => {
                const optionHtml = `<option value="${bloco.id_bloco}">${bloco.nome}</option>`;
                selectCriar.innerHTML += optionHtml;
                selectEditar.innerHTML += optionHtml;
            });
        }
    } catch (error) {
        console.error("Erro ao buscar blocos:", error);
    }
}

async function criarBloco() {
    const nome = document.getElementById('nomeBloco').value;
    const descricao = document.getElementById('descBloco').value;

    if (!nome) {
        alert("Preencha o nome do bloco!");
        return;
    }

    try {
        const response = await fetch(URL_API_BLOCO(), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ nome: nome, descricao: descricao })
        });
        
        const result = await response.json();
        if (result.success) {
            alert(result.message);
            document.getElementById('formCriarBloco').reset();
            document.getElementById('closeModalBloco').click();
            listarBlocosParaSelect(); 
        } else {
            alert("Erro: " + result.message);
        }
    } catch (error) {
        console.error("Erro:", error);
    }
}

// ==========================================
// FUNÇÕES DE GERENCIAMENTO DE BLOCOS (EXCLUIR)
// ==========================================

// Abre o modal e lista todos os blocos na tabela interna dele
async function abrirModalGerenciarBlocos() {
    try {
        const response = await fetch(URL_API_BLOCO());
        const result = await response.json();
        const tbody = document.getElementById('listaBlocosBody');
        tbody.innerHTML = '';

        if (result.success && result.data) {
            result.data.forEach(bloco => {
                tbody.innerHTML += `
                    <tr>
                        <td class="text-muted">${bloco.id_bloco}</td>
                        <td class="fw-medium">${bloco.nome}</td>
                        <td class="text-center d-flex gap-1 justify-content-center">
                            <button class="btn btn-sm btn-outline-warning" onclick="abrirEditarBloco(${bloco.id_bloco}, '${bloco.nome.replace(/'/g, "\\'")}', '${(bloco.descricao || '').replace(/'/g, "\\'")}')">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="excluirBloco(${bloco.id_bloco})">
                                <i class="bi bi-trash-fill text-danger"></i>
                            </button>
                        </td>
                    </tr>`;
            });
        } else {
            tbody.innerHTML = `<tr><td colspan="3" class="text-center text-muted">Nenhum bloco encontrado.</td></tr>`;
        }
        
        // Abre o modal (usa getOrCreateInstance para evitar múltiplas instâncias)
        const modalEl = document.getElementById('modalGerenciarBlocos');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    } catch (error) {
        console.error("Erro ao buscar blocos para gerenciamento:", error);
    }
}

function abrirEditarBloco(id, nome, descricao) {
    // Fecha o modal de gerenciamento antes de abrir o de edição
    const modalGerenciar = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalGerenciarBlocos'));
    if (modalGerenciar) modalGerenciar.hide();

    document.getElementById('editIdBloco').value = id;
    document.getElementById('editNomeBloco').value = nome;
    document.getElementById('editDescBloco').value = descricao || '';
    
    const modalEditar = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEditarBloco'));
    modalEditar.show();
}

async function salvarEdicaoBloco() {
    const id = document.getElementById('editIdBloco').value;
    const nome = document.getElementById('editNomeBloco').value;
    const descricao = document.getElementById('editDescBloco').value;
    if (!nome) { alert('Informe o nome do bloco.'); return; }
    const res = await fetch(URL_API_BLOCO(), {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id_bloco: id, nome, descricao })
    });
    const result = await res.json();
    if (result.success) {
        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEditarBloco')).hide();
        abrirModalGerenciarBlocos();
        listarBlocosParaSelect();
        listarAmbientes();
    } else {
        alert(result.message);
    }
}

async function excluirBloco(id_bloco) {
    if (!confirm('Tem certeza que deseja excluir este bloco? Só é possível se não houver chamados vinculados aos seus ambientes.')) return;
    try {
        const response = await fetch(URL_API_BLOCO(), {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_bloco: id_bloco })
        });
        const result = await response.json();

        if (result.success) {
            abrirModalGerenciarBlocos();
            listarBlocosParaSelect();
            listarAmbientes();
        } else if (result.chamados_vinculados && result.chamados_vinculados.length) {
            // Fecha o modal de gerenciamento para não sobrepor com a lista de bloqueio
            const modalGerenciar = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalGerenciarBlocos'));
            if (modalGerenciar) modalGerenciar.hide();
            mostrarChamadosVinculados(result.message, result.chamados_vinculados);
        } else {
            alert('Erro: ' + result.message);
        }
    } catch (error) {
        console.error('Erro ao excluir bloco:', error);
    }
}