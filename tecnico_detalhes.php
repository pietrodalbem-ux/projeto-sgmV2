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
$nome_exibicao = $_SESSION['user_nome'];
$primeira_letra = strtoupper(substr($nome_exibicao, 0, 1));
?>

<body>
    <div class="app-container">
        <!-- Sidebar -->
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

        <!-- Main Content -->
        <main class="main-content">
            <header class="topbar">
                <div class="page-title d-flex align-items-center">
                    <button class="menu-toggle" onclick="toggleSidebar()"><i class="ph ph-list"></i></button>
                    <h1>Detalhes da Tarefa #<?= $id ?></h1>

                </div>
                
                <div class="topbar-actions">
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
                                <h2 class="h5 fw-bold mb-0">Informações do Chamado</h2>
                                <span id="badgeStatus" class="status-badge">CARREGANDO...</span>
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
                                    <label class="text-muted small fw-bold text-uppercase d-block mb-1">Evidência Visual</label>
                                    <div class="p-2 border rounded bg-main d-inline-block">
                                        <img id="imgAnexo" src="" style="max-height: 250px; border-radius: 8px; cursor: pointer;" onclick="window.open(this.src, '_blank')">
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card h-100">
                            <h5 class="fw-bold mb-4 d-flex align-items-center gap-2">
                                <i class="ph ph-check-circle text-success"></i> Atualizar Status
                            </h5>
                            <form id="formStatus">
                                <div class="mb-4">
                                    <label class="text-muted small fw-bold text-uppercase mb-2">Novo Status</label>
                                    <select id="selectStatus" class="form-control" required>
                                        <option value="aberto">Aberto</option>
                                        <option value="em_execucao">Em Execução</option>
                                        <option value="concluido">Concluído</option>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="text-muted small fw-bold text-uppercase mb-2">Observações / Solução</label>
                                    <textarea id="txtObservacao" class="form-control" rows="4" placeholder="Descreva o que foi feito..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Salvar Alterações</button>
                            </form>
                        </div>

                        <!-- Mural de Atualizações -->
                        <div class="card mt-4">
                            <h5 class="fw-bold mb-4 d-flex align-items-center gap-2">
                                <i class="ph ph-chat-circle-dots text-primary"></i> Mural de Atualizações
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/notificacoes.js"></script>
    <script>
        async function carregar() {
            try {
                const res = await fetch(`api/gestor_chamados.php?id=<?= $id ?>`);
                const c = await res.json();
                
                if(c) {
                    document.getElementById('txtLocal').innerText = `${c.bloco_nome} - ${c.ambiente_nome}`;
                    document.getElementById('txtPrioridade').innerText = c.prioridade.toUpperCase();
                    document.getElementById('txtDescricao').innerText = c.descricao_problema;
                    document.getElementById('selectStatus').value = c.status;
                    
                    if(c.data_previsao_conclusao) {
                        const data = new Date(c.data_previsao_conclusao);
                        document.getElementById('txtPrazo').innerText = data.toLocaleDateString('pt-BR');
                    } else {
                        document.getElementById('txtPrazo').innerText = 'Sem prazo definido';
                    }
                    
                    const badge = document.getElementById('badgeStatus');
                    badge.innerText = c.status.toUpperCase().replace('_', ' ');
                    badge.className = `badge ${c.status === 'aberto' ? 'status-aberto' : 'status-concluido'}`;

                    if(c.foto) {
                        document.getElementById('boxFoto').style.display = 'block';
                        document.getElementById('imgAnexo').src = c.foto;
                    }

                    carregarMural();
                }
            } catch (e) { console.error(e); }
        }

        async function carregarMural() {
            const container = document.getElementById('muralComentarios');
            try {
                const res = await fetch(`api/comentarios.php?acao=listar&id_chamado=<?= $id ?>`);
                const result = await res.json();
                
                if(result.success && result.data.length > 0) {
                    container.innerHTML = result.data.map(com => `
                        <div class="timeline-item">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <span class="fw-bold small text-main">${com.usuario_nome} <span class="badge bg-light text-muted fw-normal" style="font-size: 0.6rem;">${com.perfil.toUpperCase()}</span></span>
                                <small class="text-muted" style="font-size: 0.65rem;">${new Date(com.data_envio).toLocaleString('pt-BR')}</small>
                            </div>
                            <p class="mb-0 small text-muted bg-light p-2 rounded">${com.texto}</p>
                        </div>
                    `).join('');
                } else {
                    container.innerHTML = '<p class="text-muted small">Nenhuma atualização registrada.</p>';
                }
            } catch (e) { console.error(e); }
        }


        document.getElementById('formStatus').onsubmit = async (e) => {
            e.preventDefault();
            const payload = {
                id_chamado: <?= $id ?>,
                status: document.getElementById('selectStatus').value,
                observacao: document.getElementById('txtObservacao').value
            };
            // Usando a API de atribuir que também pode atualizar status se configurada, 
            // ou uma API específica de status se existir.
            // Para este projeto, vamos supor que api/atribuir_chamado.php ou uma nova api/atualizar_status.php lida com isso.
            // Vou criar a api/atualizar_status.php para ser mais limpo.
            const res = await fetch('api/atualizar_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const result = await res.json();
            if (result.success) {
                alert("Status atualizado!");
                window.location.href = 'tecnico_minhas_tarefas.php';
            } else {
                alert("Erro: " + result.message);
            }
        };
        carregar();
    </script>
</body>
</html>
