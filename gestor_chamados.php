<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    header("Location: login.php");
    exit;
}
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
                <li class="nav-item">
                    <a href="gestor_dashboard.php" class="nav-link">
                        <i class="ph ph-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="gestor_chamados.php" class="nav-link active">
                        <i class="ph ph-list-bullets"></i>
                        <span>Chamados</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="gestor_ambientes.php" class="nav-link">
                        <i class="ph ph-buildings"></i>
                        <span>Ambientes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="gestor_usuarios.php" class="nav-link">
                        <i class="ph ph-users"></i>
                        <span>Usuários</span>
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
                    <h1>Gerenciar Chamados</h1>
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
                <div class="card p-3 mb-4 d-flex flex-row gap-2 align-items-center flex-wrap border-0">
                    <span class="text-muted small fw-bold text-uppercase me-2">Filtrar:</span>
                    <button class="btn btn-sm btn-dark filtro-btn active" onclick="filtrarChamados('', this)">Todos</button>
                    <button class="btn btn-sm btn-outline-primary filtro-btn" onclick="filtrarChamados('aberto', this)">Abertos</button>
                    <button class="btn btn-sm btn-outline-warning filtro-btn" onclick="filtrarChamados('em_execucao', this)">Em Execução</button>
                    <button class="btn btn-sm btn-outline-success filtro-btn" onclick="filtrarChamados('concluido', this)">Concluídos</button>

                </div>

                <div class="card p-0 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Foto</th>
                                    <th>Solicitante</th>
                                    <th>Local / Ambiente</th>
                                    <th>Prioridade</th>
                                    <th>Técnico</th>
                                    <th>Status</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="tabelaGeral">
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <div class="spinner-border spinner-border-sm me-2"></div>
                                        Carregando chamados...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div class="modal fade" id="modalFoto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow bg-dark">
                <div class="modal-body p-0 text-center">
                    <img src="" id="imgModal" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/notificacoes.js"></script>
    <script>
        function verFoto(url) {
            document.getElementById('imgModal').src = url;
            new bootstrap.Modal(document.getElementById('modalFoto')).show();
        }

        const configPrioridade = { 
            'urgente': { class: 'status-urgente', icon: 'ph ph-warning-octagon' }, 
            'alta': { class: 'status-urgente', icon: 'ph ph-arrow-circle-up' }, 
            'media': { class: 'status-concluido', icon: 'ph ph-minus-circle' }, 
            'baixa': { class: 'status-aberto', icon: 'ph ph-arrow-circle-down' } 
        };

        const statusStyles = { 
            'aberto': 'status-aberto', 
            'em_execucao': 'status-concluido', 
            'concluido': 'status-aberto', 
            'fechado': 'status-concluido',
            'agendado': 'status-concluido'
        };

        function filtrarChamados(status, btn) {
            document.querySelectorAll('.filtro-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            carregarChamados(status);
        }


        async function carregarChamados(status = '') {
            const body = document.getElementById('tabelaGeral');
            try {
                const res = await fetch(`api/gestor_chamados.php?status=${status}`);
                const chamados = await res.json();
                if (chamados.length === 0) {
                    body.innerHTML = `<tr><td colspan="8" class="text-center py-5 text-muted"><i class="ph ph-folder-open fs-2 d-block mb-2"></i>Nenhum chamado encontrado.</td></tr>`;
                    return;
                }
                body.innerHTML = chamados.map(c => {
                    const prio = configPrioridade[c.prioridade] || configPrioridade['baixa'];
                    const style = statusStyles[c.status] || 'status-aberto';
                    const thumbHtml = c.foto ?
                        `<img src="${c.foto}" style="width: 35px; height: 35px; border-radius: 6px; object-fit: cover; cursor: pointer; border: 1px solid var(--border);" onclick="verFoto('${c.foto}')">` :
                        '<div style="width: 35px; height: 35px; background: var(--bg-main); border-radius: 6px; display: flex; align-items: center; justify-content: center; color: var(--text-muted); border: 1px solid var(--border);"><i class="ph ph-image"></i></div>';
                    
                    return `
                    <tr>
                        <td class="fw-bold text-muted">#${c.id_chamado}</td>
                        <td>${thumbHtml}</td>
                        <td class="fw-medium">${c.solicitante_nome}</td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-main">${c.ambiente_nome}</span>
                                <small class="text-muted" style="font-size: 0.7rem;">${c.bloco_nome}</small>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-1 ${prio.class}" style="background: none; padding: 0;">
                                <i class="${prio.icon}"></i>
                                <span class="text-capitalize small fw-medium">${c.prioridade}</span>
                            </div>
                        </td>
                        <td>
                            ${c.tecnico_nome ? `<span class="small fw-medium"><i class="ph ph-user-check text-success"></i> ${c.tecnico_nome}</span>` : `<span class="text-muted small fst-italic">Não atribuído</span>`}
                        </td>
                        <td><span class="badge ${style}">${c.status.toUpperCase().replace('_', ' ')}</span></td>
                        <td class="text-center">
                            <a href="gestor_detalhes.php?id=${c.id_chamado}" class="btn-primary" style="width: auto; padding: 0.4rem 0.8rem; text-decoration: none; font-size: 0.75rem;">
                                Gerenciar
                            </a>
                        </td>
                    </tr>`;
                }).join('');
            } catch (error) {
                body.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-danger">Erro ao carregar os dados.</td></tr>`;
            }
        }
        carregarChamados();
    </script>
</body>
</html>
