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
                    <a href="gestor_dashboard.php" class="nav-link active">
                        <i class="ph ph-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="gestor_chamados.php" class="nav-link">
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
                    <h1>Visão Geral</h1>
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
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="card glass mb-0 border-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted small fw-bold text-uppercase mb-2" style="letter-spacing: 0.5px;">Novas Solicitações</p>
                                    <h2 class="mb-0 fw-bold display-6" id="count-novas">0</h2>
                                </div>
                                <div class="p-3 rounded-4 bg-primary bg-opacity-75 text-primary">
                                    <i class="ph-fill ph-envelope-open fs-2"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card glass mb-0 border-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted small fw-bold text-uppercase mb-2" style="letter-spacing: 0.5px;">Em Andamento</p>
                                    <h2 class="mb-0 fw-bold display-6" id="count-andamento">0</h2>
                                </div>
                                <div class="p-3 rounded-4 bg-warning bg-opacity-75 text-warning">
                                    <i class="ph-fill ph-wrench fs-2"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card glass mb-0 border-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted small fw-bold text-uppercase mb-2" style="letter-spacing: 0.5px;">Críticos / Urgentes</p>
                                    <h2 class="mb-0 fw-bold display-6" id="count-criticos">0</h2>
                                </div>
                                <div class="p-3 rounded-4 bg-danger bg-opacity-75 text-danger">
                                    <i class="ph-fill ph-warning-octagon fs-2"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row g-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header border-0 bg-transparent p-0 mb-4">
                                <h5 class="card-title mb-0">Ações Rápidas</h5>
                            </div>
                            <div class="row g-3">
                                <div class="col-6">
                                    <a href="gestor_chamados.php" class="btn w-100 p-3 border rounded-3 text-start d-flex align-items-center gap-3 nav-link text-main">
                                        <div class="p-2 rounded ">
                                            <i class="ph ph-ticket fs-5 text-dark"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0 fw-semibold small">Gerenciar Chamados</p>
                                            <p class="text-muted x-small mb-0" style="font-size: 0.7rem;">Ver fila e atribuir técnicos</p>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="gestor_ambientes.php" class="btn w-100 p-3 border rounded-3 text-start d-flex align-items-center gap-3 nav-link text-main">
                                        <div class="p-2 rounded bg-accent bg-opacity-75 text-accent">
                                            <i class="bi bi-buildings fs-5 text-black"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0 fw-semibold small">Configurar Ambientes</p>
                                            <p class="text-muted x-small mb-0" style="font-size: 0.7rem;">Editar blocos e salas</p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <h5 class="card-title mb-3">Dica do Dia</h5>
                            <p class="text-muted small">Mantenha os chamados atualizados para que os solicitantes possam acompanhar o progresso em tempo real.</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/notificacoes.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            carregarMetricas();
        });

        async function carregarMetricas() {
            try {
                const response = await fetch('api/dashboard.php');
                const result = await response.json();
                if (result.success) {
                    document.getElementById('count-novas').innerText = result.data.novas;
                    document.getElementById('count-andamento').innerText = result.data.andamento;
                    document.getElementById('count-criticos').innerText = result.data.criticos;
                }
            } catch (error) {
                console.error("Erro ao carregar métricas:", error);
            }
        }
    </script>
</body>
</html>
