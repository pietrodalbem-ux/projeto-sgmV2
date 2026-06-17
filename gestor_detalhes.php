<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    header("Location: login.php");
    exit;
}
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$nome_exibicao = $_SESSION['user_nome'];
$primeira_letra = strtoupper(substr($nome_exibicao, 0, 1));
?>
<!DOCTYPE html>
<html lang="pt-br">
<?php include 'layout/head.php'; ?>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <i class="ph-fill ph-shield-check"></i>
                <h2>SGM | Gestor</h2>
            </div>
            
            <ul class="nav-links">
                <li class="nav-item"><a href="gestor_dashboard.php" class="nav-link"><i class="ph ph-chart-line"></i><span>Dashboard</span></a></li>
                <li class="nav-item"><a href="gestor_chamados.php" class="nav-link active"><i class="ph ph-list-bullets"></i><span>Chamados</span></a></li>
                <li class="nav-item"><a href="gestor_ambientes.php" class="nav-link"><i class="ph ph-buildings"></i><span>Ambientes</span></a></li>
                <li class="nav-item"><a href="gestor_tecnicos.php" class="nav-link"><i class="ph ph-wrench"></i><span>Técnicos</span></a></li>
                <li class="nav-item"><a href="gestor_usuarios.php" class="nav-link"><i class="ph ph-users"></i><span>Usuários</span></a></li>
            </ul>

            <div class="mt-auto">
                <a href="api/logout.php" class="nav-link text-danger"><i class="ph ph-sign-out"></i><span>Sair</span></a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="topbar">
                <div class="page-title d-flex align-items-center">
                    <button class="menu-toggle" onclick="toggleSidebar()"><i class="ph ph-list"></i></button>
                    <h1>Detalhes do Chamado #<?= $id ?></h1>
                </div>

                <div class="topbar-actions">
                    <!-- Notificações -->
                    <div class="dropdown me-3">
                        <button class="btn border-0 position-relative p-1" type="button" data-bs-toggle="dropdown" id="btnNotificacoes">
                            <i class="ph-fill ph-bell fs-4 text-muted"></i>
                            <span id="notif-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none; font-size: 0.6rem;">0</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-0 overflow-hidden" style="width: 320px; border-radius: 16px;" id="listaNotificacoes">
                            <li class="p-3 border-bottom bg-light">
                                <h6 class="mb-0 fw-bold">Notificações</h6>
                            </li>
                            <div id="notif-items" style="max-height: 350px; overflow-y: auto;">
                                <!-- Itens via JS -->
                            </div>
                        </ul>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small">Olá, <strong><?= $nome_exibicao ?></strong></span>
                        <div style="width: 32px; height: 32px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.8rem;"><?= $primeira_letra ?></div>
                    </div>
                </div>

            </header>


            <div class="p-4">
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="card h-100">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <h2 class="h5 fw-bold mb-0">Informações da Solicitação</h2>
                                <span id="badgeStatus" class="status-badge">CARREGANDO...</span>
                            </div>
                            
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="text-muted small fw-bold text-uppercase d-block mb-1">Solicitante</label>
                                    <p id="txtSolicitante" class="fw-medium mb-0">-</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small fw-bold text-uppercase d-block mb-1">Local / Ambiente</label>
                                    <p id="txtLocal" class="fw-medium mb-0">-</p>
                                </div>
                                <div class="col-12">
                                    <label class="text-muted small fw-bold text-uppercase d-block mb-1">Descrição do Problema</label>
                                    <div class="p-3 bg-main rounded border mb-3" id="txtDescricao">...</div>
                                </div>
                                <div class="col-12" id="boxFoto" style="display:none;">
                                    <label class="text-muted small fw-bold text-uppercase d-block mb-1">Evidências Visuais</label>
                                    <div id="containerFotos" class="d-flex flex-wrap gap-2"></div>
                                </div>

                            </div>

                            <div class="mt-4 pt-4 border-top">
                                <h5 class="fw-bold mb-3"><i class="ph ph-pencil-simple text-warning"></i> Editar Dados do Chamado</h5>
                                <form id="formEditarChamado" enctype="multipart/form-data">
                                    <input type="hidden" name="id_chamado" value="<?= $id ?>">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold">Bloco</label>
                                            <select id="editBloco" class="form-control" onchange="carregarAmbientesEdit(this.value)"></select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold">Ambiente</label>
                                            <select id="editAmbiente" name="id_ambiente" class="form-control" required></select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold">Tipo de Serviço</label>
                                            <select id="editTipo" name="id_tipo" class="form-control" required></select>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small fw-bold">Descrição</label>
                                            <textarea id="editDescricao" name="descricao" class="form-control" rows="3" required></textarea>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small fw-bold">Nova foto (opcional)</label>
                                            <input type="file" id="editFoto" name="foto" accept="image/*" class="form-control">
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-warning">Salvar Dados</button>
                                            <button type="button" class="btn btn-outline-danger ms-2" onclick="excluirChamado()">Excluir Chamado</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card mb-4">
                            <h5 class="fw-bold mb-4 d-flex align-items-center gap-2">
                                <i class="ph ph-user-gear text-primary"></i> Atribuição
                            </h5>
                            <form id="formAtribuir">
                                <div class="mb-3">
                                    <label class="text-muted small fw-bold text-uppercase mb-2">Técnico Responsável</label>
                                    <select id="selectTecnico" class="form-control" required>
                                        <option value="">Selecione...</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="text-muted small fw-bold text-uppercase mb-2">Prioridade</label>
                                    <select id="prioridade" class="form-control" required>
                                        <option value="baixa">Baixa</option>
                                        <option value="media">Média</option>
                                        <option value="alta">Alta</option>
                                        <option value="urgente">Urgente</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="text-muted small fw-bold text-uppercase mb-2">Status do Chamado</label>
                                    <select id="selectStatus" class="form-control" required>
                                        <option value="aberto">Aberto</option>
                                        <option value="agendado">Agendado</option>
                                        <option value="em_execucao">Em Execução</option>
                                        <option value="concluido">Concluído</option>
                                        <option value="fechado">Fechado</option>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="text-muted small fw-bold text-uppercase mb-2">Previsão de Conclusão</label>
                                    <input type="date" id="data_previsao" class="form-control">
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Confirmar Alterações</button>
                            </form>
                        </div>

                        <div class="card">
                            <h5 class="fw-bold mb-3"><i class="ph ph-chat-circle-dots text-primary"></i> Novo Comentário</h5>
                            <form id="formComentario" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <textarea id="txtComentario" class="form-control" rows="3" placeholder="Descreva o que foi feito..." required></textarea>
                                </div>
                                <div class="mb-3">
                                    <input type="file" id="fotoComentario" accept="image/*" class="form-control">
                                    <small class="text-muted">Imagem opcional</small>
                                </div>
                                <button type="submit" class="btn btn-outline-primary w-100">Registrar Atualização</button>
                            </form>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card mt-4">
                            <h5 class="fw-bold mb-4 d-flex align-items-center gap-2">
                                <i class="ph ph-clock-counter-clockwise text-primary"></i> Histórico de Atualizações
                            </h5>
                            <div id="muralComentarios" class="timeline">
                                <p class="text-muted small">Nenhuma atualização registrada.</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>

    <!-- Modal editar comentário -->
    <div class="modal fade" id="modalEditarComentario" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content card p-4">
                <h5 class="card-title mb-3">Editar Comentário</h5>
                <form id="formEditarComentario" enctype="multipart/form-data">
                    <input type="hidden" id="editComId">
                    <div class="mb-3">
                        <textarea id="editComTexto" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <input type="file" id="editComFoto" accept="image/*" class="form-control">
                        <small class="text-muted">Deixe vazio para manter a imagem atual</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light w-50" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary w-50">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/notificacoes.js"></script>
    <script>
        const userId = <?= $_SESSION['user_id'] ?>;
        let modalVisualizar;
        let modalEditar;
        let comentariosCache = [];

        document.addEventListener('DOMContentLoaded', () => {
            modalVisualizar = new bootstrap.Modal(document.getElementById('modalVerImagem'));
            modalEditar = new bootstrap.Modal(document.getElementById('modalEditarComentario'));
        });

        function abrirModalImagem(src) {
            document.getElementById('imgModalVisualizar').src = src;
            modalVisualizar.show();
        }

        async function carregar() {
            try {
                const tecJson = await sgmFetch('api/usuarios.php?acao=listar&perfil=tecnico');
                const tecnicos = sgmAsList(tecJson).data;
                const select = document.getElementById('selectTecnico');
                tecnicos.forEach(t => { select.innerHTML += `<option value="${t.id_usuario}">${t.nome}</option>`; });

                const c = sgmAsObject(await sgmFetch(`api/gestor_chamados.php?id=<?= $id ?>`));
                const full = sgmAsObject(await sgmFetch(`api/chamados.php?id=<?= $id ?>`));
                const resBlocos = sgmAsList(await sgmFetch('api/localizacoes.php?acao=listar_blocos'));
                const resTipos = sgmAsList(await sgmFetch('api/localizacoes.php?acao=listar_tipos'));

                const preencher = (id, dados, cId, cNome, padrao) => {
                    const s = document.getElementById(id);
                    if (!dados) return;
                    s.innerHTML = `<option value="">${padrao}</option>` + dados.map(i => `<option value="${i[cId]}">${i[cNome]}</option>`).join('');
                };
                preencher('editBloco', resBlocos.data, 'id_bloco', 'nome', 'Bloco...');
                preencher('editTipo', resTipos.data, 'id_tipo', 'nome', 'Tipo...');

                if (!c) {
                    alert('Não foi possível carregar os dados do chamado.');
                    return;
                }

                document.getElementById('txtSolicitante').innerText = c.solicitante_nome;
                document.getElementById('txtLocal').innerText = `${c.bloco_nome} - ${c.ambiente_nome}`;
                document.getElementById('txtDescricao').innerText = c.descricao_problema;
                document.getElementById('editDescricao').value = c.descricao_problema;
                if (full && full.id_bloco) {
                    document.getElementById('editBloco').value = full.id_bloco;
                    await carregarAmbientesEdit(full.id_bloco, full.id_ambiente);
                }
                if (full && full.id_tipo_servico) document.getElementById('editTipo').value = full.id_tipo_servico;

                const badge = document.getElementById('badgeStatus');
                badge.innerText = c.status.toUpperCase().replace('_', ' ');
                badge.className = `badge ${c.status === 'aberto' ? 'status-aberto' : 'status-concluido'}`;

                if (c.id_tecnico) document.getElementById('selectTecnico').value = c.id_tecnico;
                if (c.prioridade) document.getElementById('prioridade').value = c.prioridade;
                if (c.status) document.getElementById('selectStatus').value = c.status;
                if (c.data_previsao_conclusao) document.getElementById('data_previsao').value = c.data_previsao_conclusao;

                if (c.fotos && c.fotos.length > 0) {
                    document.getElementById('boxFoto').style.display = 'block';
                    document.getElementById('containerFotos').innerHTML = c.fotos.map(f => `<div class="p-2 border rounded bg-main d-inline-block"><img src="${f}" style="max-height: 250px; border-radius: 8px; cursor: pointer;" onclick="abrirModalImagem(this.src)"></div>`).join('');
                } else if (c.foto) {
                    document.getElementById('boxFoto').style.display = 'block';
                    document.getElementById('containerFotos').innerHTML = `<div class="p-2 border rounded bg-main d-inline-block"><img src="${c.foto}" style="max-height: 250px; border-radius: 8px; cursor: pointer;" onclick="abrirModalImagem(this.src)"></div>`;
                }

                carregarMural();
            } catch (e) { console.error(e); }
        }

        function renderMural(comentarios) {
            comentariosCache = comentarios;
            const container = document.getElementById('muralComentarios');
            if (!comentarios.length) {
                container.innerHTML = '<p class="text-muted small">Nenhuma atualização registrada.</p>';
                return;
            }
            container.innerHTML = comentarios.map(com => {
                const isOwn = parseInt(com.id_usuario) === userId;
                const acoes = isOwn ? `
                    <div class="d-flex gap-1 mt-2">
                        <button class="btn btn-sm btn-outline-warning" onclick="abrirEditarComentario(${com.id_comentario})">Editar</button>
                        <button class="btn btn-sm btn-outline-danger" onclick="excluirComentario(${com.id_comentario})">Excluir</button>
                    </div>` : '';
                return `
                    <div class="timeline-item mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold small text-main">${com.usuario_nome} <span class="badge bg-light text-muted fw-normal" style="font-size: 0.6rem;">${com.perfil.toUpperCase()}</span></span>
                            <small class="text-muted" style="font-size: 0.65rem;">${new Date(com.data_envio).toLocaleString('pt-BR')}</small>
                        </div>
                        <p class="mb-1 small text-muted bg-light p-2 rounded">${com.texto}</p>
                        ${com.caminho_arquivo ? `<div class="mt-2"><img src="${com.caminho_arquivo}" class="img-thumbnail img-comment-thumbnail" style="max-width: 120px; max-height: 80px; object-fit: cover; cursor: pointer; transition: transform 0.2s;" onclick="abrirModalImagem(this.src)" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'"></div>` : ''}
                        ${acoes}
                    </div>`;
            }).join('');
        }

        async function carregarMural() {
            try {
                const result = await sgmFetch(`api/comentarios.php?acao=listar&id_chamado=<?= $id ?>`);
                if(result.success) renderMural(result.data || []);
            } catch (e) { console.error(e); }
        }

        function abrirEditarComentario(id) {
            const com = comentariosCache.find(c => parseInt(c.id_comentario) === parseInt(id));
            if (!com) return;
            document.getElementById('editComId').value = com.id_comentario;
            document.getElementById('editComTexto').value = com.texto;
            document.getElementById('editComFoto').value = '';
            modalEditar.show();
        }

        document.getElementById('formEditarComentario').onsubmit = async (e) => {
            e.preventDefault();
            const fd = new FormData();
            fd.append('acao', 'atualizar');
            fd.append('id_comentario', document.getElementById('editComId').value);
            fd.append('texto', document.getElementById('editComTexto').value);
            const foto = document.getElementById('editComFoto');
            if (foto.files.length) fd.append('foto', foto.files[0]);
            const data = await sgmFetch('api/comentarios.php', { method: 'POST', body: fd });
            if (data.success) {
                modalEditar.hide();
                renderMural(data.data);
            } else alert(data.message);
        };

        async function excluirComentario(id) {
            if (!confirm('Excluir este comentário?')) return;
            const fd = new FormData();
            fd.append('acao', 'excluir');
            fd.append('id_comentario', id);
            const data = await sgmFetch('api/comentarios.php', { method: 'POST', body: fd });
            if (data.success) renderMural(data.data || []);
            else alert(data.message);
        };


        async function carregarAmbientesEdit(id_bloco, id_ambiente = null) {
            const sel = document.getElementById('editAmbiente');
            if (!id_bloco) return;
            const ambientes = sgmAsList(await sgmFetch(`api/localizacoes.php?acao=listar_ambientes&id_bloco=${id_bloco}`)).data;
            sel.innerHTML = ambientes.map(a => `<option value="${a.id_ambiente}">${a.nome}</option>`).join('');
            if (id_ambiente) sel.value = id_ambiente;
        }

        document.getElementById('formEditarChamado').onsubmit = async (e) => {
            e.preventDefault();
            const fd = new FormData(e.target);
            fd.append('id_ambiente', document.getElementById('editAmbiente').value);
            fd.append('id_tipo', document.getElementById('editTipo').value);
            fd.append('descricao', document.getElementById('editDescricao').value);
            const data = await sgmFetch('api/atualizar_chamado.php', { method: 'POST', body: fd });
            if (data.success) { alert(data.message); location.reload(); }
            else alert(data.message);
        };

        async function excluirChamado() {
            if (!confirm('Tem certeza que deseja excluir este chamado permanentemente?')) return;
            const data = await sgmFetch('api/chamados.php', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_chamado: <?= $id ?> })
            });
            if (data.success) { alert(data.message); window.location.href = 'gestor_chamados.php'; }
            else alert(data.message);
        }

        document.getElementById('formAtribuir').onsubmit = async (e) => {
            e.preventDefault();
            const payload = {
                id_chamado: <?= $id ?>,
                id_tecnico: document.getElementById('selectTecnico').value,
                prioridade: document.getElementById('prioridade').value,
                status: document.getElementById('selectStatus').value,
                data_previsao_conclusao: document.getElementById('data_previsao').value
            };
            const result = await sgmFetch('api/atribuir_chamado.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            if (result.success) {
                alert("Atribuído com sucesso!");
                window.location.href = 'gestor_chamados.php';
            } else {
                alert("Erro: " + result.message);
            }
        };

        document.getElementById('formComentario').onsubmit = async (e) => {
            e.preventDefault();
            const fd = new FormData();
            fd.append('acao', 'salvar');
            fd.append('id_chamado', <?= $id ?>);
            fd.append('texto', document.getElementById('txtComentario').value);
            const foto = document.getElementById('fotoComentario');
            if (foto.files.length) fd.append('foto', foto.files[0]);
            const data = await sgmFetch('api/comentarios.php', { method: 'POST', body: fd });
            if (data.success) {
                document.getElementById('formComentario').reset();
                carregarMural();
            } else alert(data.message);
        };

        carregar();
    </script>

    <!-- Modal para visualização de imagem em tamanho maior -->
    <div class="modal fade" id="modalVerImagem" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-body p-0 text-center position-relative">
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    <img id="imgModalVisualizar" src="" class="img-fluid rounded shadow-lg" style="max-height: 85vh; border: 3px solid rgba(255, 255, 255, 0.25);">
                </div>
            </div>
        </div>
    </div>
</body>
</html>
