<!DOCTYPE html>
<html lang="pt-br">
<?php 
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'tecnico') {
    header("Location: login.php");
    exit;
}
include 'layout/head.php'; 
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = (int)$_SESSION['user_id'];
$nome_exibicao = $_SESSION['user_nome'];
$primeira_letra = strtoupper(substr($nome_exibicao, 0, 1));
?>

<body>
    <div class="app-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <i class="ph-fill ph-wrench"></i>
                <h2>SGM | Técnico</h2>
            </div>
            <ul class="nav-links">
                <li class="nav-item">
                    <a href="tecnico_minhas_tarefas.php" class="nav-link active">
                        <i class="ph ph-clipboard-text"></i>
                        <span>Minhas Tarefas</span>
                    </a>
                </li>
            </ul>
            <div class="mt-auto">
                <a href="api/logout.php" class="nav-link text-danger">
                    <i class="ph ph-sign-out"></i>
                    <span>Sair</span>
                </a>
            </div>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <div class="page-title d-flex align-items-center">
                    <button class="menu-toggle" onclick="toggleSidebar()"><i class="ph ph-list"></i></button>
                    <h1>Detalhes da Tarefa #<?= $id ?></h1>
                </div>
                <div class="topbar-actions">
                    <span class="text-muted small">Olá, <strong><?= htmlspecialchars($nome_exibicao) ?></strong></span>
                </div>
            </header>

            <div class="p-4">
                <div id="alertaErro" class="alert alert-danger d-none"></div>
                <div class="row g-4" id="conteudoTarefa" style="display:none;">
                    <div class="col-lg-8">
                        <div class="card h-100">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <h2 class="h5 fw-bold mb-0">Informações do Chamado</h2>
                                <span id="badgeStatus" class="status-badge">—</span>
                            </div>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="text-muted small fw-bold text-uppercase d-block mb-1">Local / Ambiente</label>
                                    <p id="txtLocal" class="fw-medium mb-0">-</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small fw-bold text-uppercase d-block mb-1">Prioridade</label>
                                    <p id="txtPrioridade" class="fw-medium mb-0">-</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small fw-bold text-uppercase d-block mb-1">Prazo de Conclusão</label>
                                    <p id="txtPrazo" class="fw-medium mb-0 text-primary">-</p>
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
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card mb-4">
                            <h5 class="fw-bold mb-4"><i class="ph ph-check-circle text-success"></i> Atualizar Status</h5>
                            <form id="formStatus">
                                <div class="mb-3">
                                    <label class="text-muted small fw-bold text-uppercase mb-2">Novo Status</label>
                                    <select id="selectStatus" class="form-control" required>
                                        <option value="aberto">Aberto</option>
                                        <option value="em_execucao">Em Execução</option>
                                        <option value="concluido">Concluído</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Salvar Status</button>
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
                        <div class="card">
                            <h5 class="fw-bold mb-4"><i class="ph ph-clock-counter-clockwise text-primary"></i> Histórico de Atualizações</h5>
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
    <script>
        const idChamado = <?= $id ?>;
        const userId = <?= $user_id ?>;
        let modalEditar;
        let modalVisualizar;
        let comentariosCache = [];

        document.addEventListener('DOMContentLoaded', () => {
            modalEditar = new bootstrap.Modal(document.getElementById('modalEditarComentario'));
            modalVisualizar = new bootstrap.Modal(document.getElementById('modalVerImagem'));
            carregar();
        });

        async function carregar() {
            try {
                const json = await sgmFetch(`api/gestor_chamados.php?id=${idChamado}`);
                const c = sgmAsObject(json);

                if (!c || parseInt(c.id_tecnico) !== userId) {
                    document.getElementById('alertaErro').classList.remove('d-none');
                    document.getElementById('alertaErro').innerText = 'Tarefa não encontrada ou não atribuída a você.';
                    return;
                }

                document.getElementById('conteudoTarefa').style.display = 'flex';
                document.getElementById('txtLocal').innerText = `${c.bloco_nome} - ${c.ambiente_nome}`;
                document.getElementById('txtPrioridade').innerText = c.prioridade.toUpperCase();
                document.getElementById('txtDescricao').innerText = c.descricao_problema;
                document.getElementById('selectStatus').value = c.status;
                document.getElementById('txtPrazo').innerText = c.data_previsao_conclusao
                    ? new Date(c.data_previsao_conclusao).toLocaleDateString('pt-BR')
                    : 'Sem prazo definido';

                const badge = document.getElementById('badgeStatus');
                badge.innerText = c.status.toUpperCase().replace('_', ' ');
                badge.className = `badge ${c.status === 'aberto' ? 'status-aberto' : 'status-concluido'}`;

                if (c.fotos && c.fotos.length > 0) {
                    document.getElementById('boxFoto').style.display = 'block';
                    document.getElementById('containerFotos').innerHTML = c.fotos.map(f => `<img src="${f}" style="max-height: 250px; border-radius: 8px; cursor: pointer;" onclick="abrirModalImagem(this.src)">`).join('');
                } else if (c.foto) {
                    document.getElementById('boxFoto').style.display = 'block';
                    document.getElementById('containerFotos').innerHTML = `<img src="${c.foto}" style="max-height: 250px; border-radius: 8px; cursor: pointer;" onclick="abrirModalImagem(this.src)">`;
                }

                carregarMural();
            } catch (e) {
                document.getElementById('alertaErro').classList.remove('d-none');
                document.getElementById('alertaErro').innerText = 'Erro ao carregar tarefa.';
            }
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
                            <span class="fw-bold small">${com.usuario_nome}</span>
                            <small class="text-muted">${new Date(com.data_envio).toLocaleString('pt-BR')}</small>
                        </div>
                        <p class="mb-1 small bg-light p-2 rounded mt-1">${com.texto}</p>
                        ${com.caminho_arquivo ? `
                        <div class="mt-2">
                            <img src="${com.caminho_arquivo}" class="img-thumbnail img-comment-thumbnail" style="max-width: 120px; max-height: 80px; object-fit: cover; cursor: pointer; transition: transform 0.2s;" onclick="abrirModalImagem(this.src)" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                        </div>` : ''}
                        ${acoes}
                    </div>`;
            }).join('');
        }

        function abrirEditarComentario(id) {
            const com = comentariosCache.find(c => parseInt(c.id_comentario) === parseInt(id));
            if (!com) return;
            document.getElementById('editComId').value = com.id_comentario;
            document.getElementById('editComTexto').value = com.texto;
            document.getElementById('editComFoto').value = '';
            modalEditar.show();
        }

        async function carregarMural() {
            const result = await sgmFetch(`api/comentarios.php?acao=listar&id_chamado=${idChamado}`);
            if (result.success) renderMural(result.data || []);
        }

        document.getElementById('formComentario').onsubmit = async (e) => {
            e.preventDefault();
            const fd = new FormData();
            fd.append('acao', 'salvar');
            fd.append('id_chamado', idChamado);
            fd.append('texto', document.getElementById('txtComentario').value);
            const foto = document.getElementById('fotoComentario');
            if (foto.files.length) fd.append('foto', foto.files[0]);
            const data = await sgmFetch('api/comentarios.php', { method: 'POST', body: fd });
            if (data.success) {
                document.getElementById('formComentario').reset();
                renderMural(data.data || []);
            } else alert(data.message);
        };

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

        document.getElementById('formStatus').onsubmit = async (e) => {
            e.preventDefault();
            const res = await sgmFetch('api/atualizar_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_chamado: idChamado, status: document.getElementById('selectStatus').value })
            });
            if (res.success) {
                alert('Status atualizado!');
                window.location.href = 'tecnico_minhas_tarefas.php';
            } else alert(res.message);
        };

        function abrirModalImagem(src) {
            document.getElementById('imgModalVisualizar').src = src;
            modalVisualizar.show();
        }
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
