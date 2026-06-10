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
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
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
                <li class="nav-item"><a href="gestor_chamados.php" class="nav-link"><i class="ph ph-list-bullets"></i><span>Chamados</span></a></li>
                <li class="nav-item"><a href="gestor_ambientes.php" class="nav-link active"><i class="ph ph-buildings"></i><span>Ambientes</span></a></li>
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
                    <h1>Configurar Espaços</h1>
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <p class="text-muted mb-0">Crie e edite os ambientes e blocos da instituição.</p>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2" onclick="abrirModalGerenciarBlocos()">
                            <i class="ph ph-list-dashes text-dark"></i> Gerenciar Blocos
                        </button>
                        <button class="btn btn-secondary btn-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modal2">
                            <i class="ph ph-stack"></i> Novo Bloco
                        </button>
                        <button class="btn-primary" style="width: auto; padding: 0.5rem 1rem;" data-bs-toggle="modal" data-bs-target="#modal1">
                            <i class="ph ph-plus-circle"></i> Novo Ambiente
                        </button>
                    </div>
                </div>

                <div class="card p-0 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Ambiente</th>
                                    <th>Bloco</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="tabelaAmbientesBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modais de Ambiente/Bloco (Originais mantidos com estilo card) -->
    <div class="modal fade" id="modal1" tabindex="-1">
        <div class="modal-dialog"><div class="modal-content card p-4">
            <h5 class="card-title mb-4">Criar Ambiente</h5>
            <form id="formCriarAmbiente">
                <div class="mb-3">
                    <label class="form-label fw-bold">Nome do Ambiente</label>
                    <input type="text" class="form-control" id="nomeAmbiente" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Pertence ao Bloco</label>
                    <select class="form-control" id="blocoAmbiente" required><option value="">Carregando...</option></select>
                </div>
                <div class="d-flex gap-2 mt-4">
                    <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn-primary" onclick="criarAmbiente()">Salvar</button>
                </div>
            </form>
        </div></div>
    </div>

    <div class="modal fade" id="modal2" tabindex="-1">
        <div class="modal-dialog"><div class="modal-content card p-4">
            <h5 class="card-title mb-4">Criar Bloco</h5>
            <form id="formCriarBloco">
                <div class="mb-3">
                    <label class="form-label fw-bold">Nome do Bloco</label>
                    <input type="text" class="form-control" id="nomeBloco" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Descrição (Opcional)</label>
                    <textarea class="form-control" id="descBloco" rows="2"></textarea>
                </div>
                <div class="d-flex gap-2 mt-4">
                    <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn-primary" onclick="criarBloco()">Salvar Bloco</button>
                </div>
            </form>
        </div></div>
    </div>

    <div class="modal fade" id="modalEditarAmbiente" tabindex="-1">
        <div class="modal-dialog"><div class="modal-content card p-4">
            <h5 class="card-title mb-4 text-warning">Editar Ambiente</h5>
            <form id="formEditarAmbiente">
                <input type="hidden" id="editIdAmbiente">
                <div class="mb-3">
                    <label class="form-label fw-bold">Nome do Ambiente</label>
                    <input type="text" class="form-control" id="editNomeAmbiente" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Bloco</label>
                    <select class="form-control" id="editBlocoAmbiente" required></select>
                </div>
                <div class="d-flex gap-2 mt-4">
                    <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn-primary bg-warning text-dark border-0" onclick="salvarEdicaoAmbiente()">Atualizar</button>
                </div>
            </form>
        </div></div>
    </div>

    <div class="modal fade" id="modalChamadosVinculados" tabindex="-1">
        <div class="modal-dialog modal-lg"><div class="modal-content card p-4">
            <h5 class="card-title mb-3 text-danger">Exclusão bloqueada</h5>
            <p id="chamadosVinculadosMsg" class="text-muted small"></p>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead><tr><th>ID</th><th>Descrição</th><th>Status</th><th>Info</th><th></th></tr></thead>
                    <tbody id="chamadosVinculadosBody"></tbody>
                </table>
            </div>
            <button class="btn btn-light w-100 mt-3" data-bs-dismiss="modal">Fechar</button>
        </div></div>
    </div>

    <div class="modal fade" id="modalEditarBloco" tabindex="-1">
        <div class="modal-dialog"><div class="modal-content card p-4">
            <h5 class="card-title mb-4">Editar Bloco</h5>
            <input type="hidden" id="editIdBloco">
            <div class="mb-3">
                <label class="form-label fw-bold">Nome</label>
                <input type="text" class="form-control" id="editNomeBloco" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Descrição</label>
                <textarea class="form-control" id="editDescBloco" rows="2"></textarea>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-light w-50" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-warning w-50" onclick="salvarEdicaoBloco()">Salvar</button>
            </div>
        </div></div>
    </div>

    <div class="modal fade" id="modalGerenciarBlocos" tabindex="-1">
        <div class="modal-dialog modal-lg"><div class="modal-content card p-4">
            <h5 class="card-title mb-4">Gerenciar Blocos</h5>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead><tr><th>ID</th><th>Nome do Bloco</th><th class="text-center">Ações</th></tr></thead>
                    <tbody id="listaBlocosBody"></tbody>
                </table>
            </div>
            <div class="mt-4"><button class="btn btn-light w-100" data-bs-dismiss="modal">Fechar</button></div>
        </div></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/notificacoes.js"></script>
    <script src="./assets/js/ambientes.js"></script>
</body>
</html>
