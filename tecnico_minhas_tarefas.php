<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'tecnico') {
    header("Location: login.php"); exit;
}
$id_logado = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<?php include 'layout/head.php'; ?>
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
                        <i class="ph ph-check-square"></i>
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

<?php
$nome_exibicao = $_SESSION['user_nome'];
$primeira_letra = strtoupper(substr($nome_exibicao, 0, 1));
?>
        <!-- Main Content -->
        <main class="main-content">
            <header class="topbar">
                <div class="page-title d-flex align-items-center">
                    <button class="menu-toggle" onclick="toggleSidebar()"><i class="ph ph-list"></i></button>
                    <h1>Minhas Tarefas</h1>
                </div>

                
                <div class="topbar-actions">
                    <div class="d-flex align-items-center gap-2">

                        <span class="text-muted small">Olá, <strong><?= $nome_exibicao ?></strong></span>
                        <div style="width: 32px; height: 32px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.8rem;"><?= $primeira_letra ?></div>
                    </div>
                </div>
            </header>


            <div class="p-4">
                <div id="listaTarefas" class="row g-4">
                    <!-- Preenchido via JS -->
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        async function carregarTarefas() {
            const container = document.getElementById('listaTarefas');
            try {
                const res = await fetch(`api/gestor_chamados.php?id_tecnico=<?= $id_logado ?>`);
                const tarefas = await res.json();

                if (tarefas.length === 0) {
                    container.innerHTML = `
                        <div class="col-12">
                            <div class="card text-center p-5">
                                <i class="ph ph-mask-happy fs-1 text-muted mb-3"></i>
                                <p class="text-muted">Nenhuma tarefa atribuída para você no momento.</p>
                            </div>
                        </div>`;
                    return;
                }

                container.innerHTML = tarefas.map(t => `
                    <div class="col-md-6 col-xl-4">
                        <div class="card h-100">
                            <div class="d-flex justify-content-between mb-3">
                                <span class="small text-muted fw-bold">#${t.id_chamado}</span>
                                <span class="badge ${t.prioridade === 'urgente' ? 'status-urgente' : 'status-concluido'}">${t.status.toUpperCase()}</span>
                            </div>
                            <h5 class="fw-bold mb-2">${t.ambiente_nome}</h5>
                            
                            ${t.foto ? `<img src="${t.foto}" class="w-100 rounded mb-3" style="height: 120px; object-fit: cover;">` : ''}

                            <p class="text-muted small mb-4">${t.descricao.substring(0, 80)}...</p>
                            
                            <div class="mt-auto pt-3 border-top">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="small text-muted"><i class="ph ph-calendar-plus"></i> Abr: ${new Date(t.data_abertura).toLocaleDateString('pt-BR')}</span>
                                    <span class="small ${t.data_previsao_conclusao ? 'text-primary fw-bold' : 'text-muted'}">
                                        <i class="ph ph-clock"></i> Prazo: ${t.data_previsao_conclusao ? new Date(t.data_previsao_conclusao).toLocaleDateString('pt-BR') : 'N/A'}
                                    </span>
                                </div>
                                <a href="tecnico_detalhes.php?id=${t.id_chamado}" class="btn-primary w-100 text-center" style="text-decoration: none; font-size: 0.8rem; display: block;">
                                    Abrir Tarefa
                                </a>
                            </div>
                        </div>
                    </div>

                `).join('');
            } catch (e) {
                container.innerHTML = '<div class="alert alert-danger">Erro ao carregar tarefas.</div>';
            }
        }
        carregarTarefas();
    </script>
</body>
</html>
