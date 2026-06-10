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
        <aside class="sidebar" id="sidebar">
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
                    <a href="gestor_tecnicos.php" class="nav-link">
                        <i class="ph ph-wrench"></i>
                        <span>Técnicos</span>
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

            <!-- Topbar -->
            <header class="topbar">
                <div class="page-title d-flex align-items-center">
                    <button class="menu-toggle" onclick="toggleSidebar()">
                        <i class="ph ph-list"></i>
                    </button>
                    <h1>Visão Geral</h1>
                </div>

                <div class="topbar-actions">
                    <!-- Notificações -->
                    <div class="dropdown me-2">
                        <button class="btn border-0 position-relative p-1" type="button"
                            data-bs-toggle="dropdown" id="btnNotificacoes">
                            <i class="ph-fill ph-bell fs-4 text-muted"></i>
                            <span id="notif-badge"
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                style="display: none; font-size: 0.6rem;">0</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-0 overflow-hidden"
                            style="width: 320px; border-radius: 16px;" id="listaNotificacoes">
                            <li class="p-3 border-bottom bg-light">
                                <h6 class="mb-0 fw-bold">Notificações</h6>
                            </li>
                            <div id="notif-items" style="max-height: 350px; overflow-y: auto;">
                                <!-- Itens via JS -->
                            </div>
                        </ul>
                    </div>

                    <!-- Usuário -->
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small d-none d-sm-inline">
                            Olá, <strong><?= $nome_exibicao ?></strong>
                        </span>
                        <div style="width:36px;height:36px;background:var(--primary);color:#fff;
                             border-radius:50%;display:flex;align-items:center;justify-content:center;
                             font-weight:700;font-size:0.85rem;flex-shrink:0;">
                            <?= $primeira_letra ?>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="page-content p-4">

                <!-- ── Linha 1: KPI Cards ── -->
                <div class="row g-4 mb-4">

                    <!-- Card: Novas Solicitações -->
                    <div class="col-12 col-md-4">
                        <div class="card kpi-card h-100 mb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="kpi-label">Novas Solicitações</p>
                                    <h2 class="kpi-value" id="count-novas">—</h2>
                                </div>
                                <div class="kpi-icon kpi-icon--primary">
                                    <i class="ph-fill ph-ticket fs-2"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card: Em Andamento -->
                    <div class="col-12 col-md-4">
                        <div class="card kpi-card h-100 mb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="kpi-label">Em Andamento</p>
                                    <h2 class="kpi-value" id="count-andamento">—</h2>
                                </div>
                                <div class="kpi-icon kpi-icon--warning">
                                    <i class="ph-fill ph-wrench fs-2"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card: Críticos / Urgentes -->
                    <div class="col-12 col-md-4">
                        <div class="card kpi-card h-100 mb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="kpi-label">Críticos / Urgentes</p>
                                    <h2 class="kpi-value" id="count-criticos">—</h2>
                                </div>
                                <div class="kpi-icon kpi-icon--danger">
                                    <i class="ph-fill ph-warning-octagon fs-2"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /Linha 1 -->

                <!-- ── Linha 2: Gráfico + Exportar PDF ── -->
                <div class="row g-4 mb-4">

                    <!-- Gráfico de Média de Manutenções -->
                    <div class="col-12 col-lg-8">
                        <div class="card h-100 mb-0">
                            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                                <h5 class="card-title mb-0">
                                    <i class="ph ph-chart-bar me-2 text-primary"></i>
                                    Média de Manutenções
                                </h5>
                                <div class="btn-group btn-group-sm" role="group" id="chartToggle">
                                    <button type="button" class="btn btn-primary btn-sm active" id="btnMensal" onclick="mostrarMensal()">Mensal</button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="btnAnual" onclick="mostrarAnual()">Anual</button>
                                </div>
                            </div>
                            <div id="chart-empty" style="display:none; height:110px;" class="d-flex flex-column align-items-center justify-content-center text-muted">
                                <!-- <i class="ph ph-chart-bar fs-1 mb-2 opacity-25"></i> -->
                                
                            </div>
                            <div style="position:relative; height:260px;" id="chart-wrapper">
                                <canvas id="mediaChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Exportar Relatório em PDF -->
                    <div class="col-12 col-lg-4">
                        <div class="card h-100 mb-0">
                            <h5 class="card-title mb-4">
                                <i class="ph ph-file-pdf me-2 text-danger"></i>
                                Exportar Relatório
                            </h5>
                            <form id="pdfForm" target="_blank" method="GET"
                                action="api/gestor_relatorio_pdf.php">
                                <div class="mb-3">
                                    <label for="period" class="form-label fw-semibold small">
                                        Período
                                    </label>
                                    <select name="period" id="period" class="form-select" required>
                                        <option value="day">Hoje</option>
                                        <option value="week">Esta semana</option>
                                        <option value="month" selected>Este mês</option>
                                        <option value="year">Este ano</option>
                                        <option value="custom">Personalizado…</option>
                                    </select>
                                </div>

                                <div id="pdf-custom-range" style="display:none;">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold small">Data Inicial</label>
                                        <input type="date" name="start" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold small">Data Final</label>
                                        <input type="date" name="end" class="form-control">
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-2 mt-2">
                                    <i class="ph ph-file-pdf me-1"></i> Gerar PDF
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
                <!-- /Linha 2 -->

                <!-- ── Linha 3: Ações Rápidas ── -->
                <div class="row g-4">
                    <div class="col-12">
                        <div class="card mb-0">
                            <h5 class="card-title mb-4">
                                <i class="ph ph-lightning me-2 text-warning"></i>
                                Ações Rápidas
                            </h5>
                            <div class="row g-3">

                                <div class="col-12 col-sm-6 col-lg-3">
                                    <a href="gestor_chamados.php" class="quick-action-btn">
                                        <div class="qa-icon qa-icon--primary">
                                            <i class="ph ph-ticket fs-4"></i>
                                        </div>
                                        <div>
                                            <p class="qa-title">Gerenciar Chamados</p>
                                            <p class="qa-sub">Ver fila e atribuir técnicos</p>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-12 col-sm-6 col-lg-3">
                                    <a href="gestor_ambientes.php" class="quick-action-btn">
                                        <div class="qa-icon qa-icon--accent">
                                            <i class="ph ph-buildings fs-4"></i>
                                        </div>
                                        <div>
                                            <p class="qa-title">Configurar Ambientes</p>
                                            <p class="qa-sub">Editar blocos e salas</p>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-12 col-sm-6 col-lg-3">
                                    <a href="gestor_usuarios.php" class="quick-action-btn">
                                        <div class="qa-icon qa-icon--success">
                                            <i class="ph ph-users fs-4"></i>
                                        </div>
                                        <div>
                                            <p class="qa-title">Gerir Usuários</p>
                                            <p class="qa-sub">Cadastros e permissões</p>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-12 col-sm-6 col-lg-3">
                                   
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Linha 3 -->

            </div>
            <!-- /page-content -->

        </main>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="assets/js/notificacoes.js"></script>

    <script>
        // ── Init ──────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', () => {
            carregarMetricas();
            carregarGrafico();

            document.getElementById('period').addEventListener('change', function () {
                document.getElementById('pdf-custom-range').style.display =
                    this.value === 'custom' ? 'block' : 'none';
            });
        });

        // ── KPI Cards ─────────────────────────────────────────
        async function carregarMetricas() {
            try {
                const json = await sgmFetch('api/dashboard.php');
                if (json.success) {
                    document.getElementById('count-novas').innerText      = json.data.novas;
                    document.getElementById('count-andamento').innerText  = json.data.andamento;
                    document.getElementById('count-criticos').innerText   = json.data.criticos;
                }
            } catch (e) {
                console.error('Erro ao carregar métricas:', e);
            }
        }

        // ── Gráfico ───────────────────────────────────────────
        let chartInstance = null;
        let dadosMensais  = [];
        let dadosAnuais   = [];

        async function carregarGrafico() {
            try {
                const data = await sgmFetch('api/gestor_media.php');

                if (!data.success) {
                    mostrarEstadoVazio();
                    return;
                }

                dadosMensais = data.monthly || [];
                dadosAnuais  = data.annual  || [];

                // Exibe mensal por padrão
                mostrarMensal();

            } catch (e) {
                console.error('Erro ao carregar gráfico:', e);
                mostrarEstadoVazio();
            }
        }

        function mostrarMensal() {
            document.getElementById('btnMensal').classList.add('active', 'btn-primary');
            document.getElementById('btnMensal').classList.remove('btn-outline-primary');
            document.getElementById('btnAnual').classList.remove('active', 'btn-primary');
            document.getElementById('btnAnual').classList.add('btn-outline-primary');

            const labels = dadosMensais.map(m => m.mes);
            const values = dadosMensais.map(m => m.total);

            renderizarGrafico(
                labels,
                values,
                'Chamados por mês',
                'rgba(79, 70, 229, 0.8)',
                'rgba(79, 70, 229, 1)'
            );
        }

        function mostrarAnual() {
            document.getElementById('btnAnual').classList.add('active', 'btn-primary');
            document.getElementById('btnAnual').classList.remove('btn-outline-primary');
            document.getElementById('btnMensal').classList.remove('active', 'btn-primary');
            document.getElementById('btnMensal').classList.add('btn-outline-primary');

            const labels = dadosAnuais.map(a => a.ano);
            const values = dadosAnuais.map(a => a.total);

            renderizarGrafico(
                labels,
                values,
                'Chamados por ano',
                'rgba(14, 165, 233, 0.8)',
                'rgba(14, 165, 233, 1)'
            );
        }

        function renderizarGrafico(labels, values, label, bgColor, borderColor) {
            const wrapper = document.getElementById('chart-wrapper');
            const empty   = document.getElementById('chart-empty');

            if (!labels.length) {
                wrapper.style.display = 'none';
                empty.style.display   = 'flex';
                return;
            }

            wrapper.style.display = 'block';
            empty.style.display   = 'none';

            // Destroi instância anterior se existir
            if (chartInstance) {
                chartInstance.destroy();
                chartInstance = null;
            }

            const ctx = document.getElementById('mediaChart').getContext('2d');

            // Gradiente de preenchimento
            const gradient = ctx.createLinearGradient(0, 0, 0, 260);
            gradient.addColorStop(0,   bgColor);
            gradient.addColorStop(1,   'rgba(255,255,255,0)');

            chartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label,
                        data: values,
                        backgroundColor: gradient,
                        borderColor,
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                        hoverBackgroundColor: borderColor
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 600, easing: 'easeOutQuart' },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            titleColor: '#fff',
                            bodyColor: '#94a3b8',
                            padding: 12,
                            cornerRadius: 10,
                            callbacks: {
                                label: ctx => ` ${ctx.parsed.y} chamado${ctx.parsed.y !== 1 ? 's' : ''}`
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0, color: '#64748b' },
                            grid: { color: 'rgba(0,0,0,0.05)', drawBorder: false }
                        },
                        x: {
                            ticks: { color: '#64748b' },
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        function mostrarEstadoVazio() {
            document.getElementById('chart-wrapper').style.display = 'none';
            document.getElementById('chart-empty').style.display   = 'flex';
        }

        // ── Sidebar Toggle ────────────────────────────────────
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }
    </script>

    <!-- Estilos específicos desta página -->
    <style>
        /* KPI Cards */
        .kpi-card { transition: transform .25s, box-shadow .25s; }
        .kpi-card:hover { transform: translateY(-4px); box-shadow: 0 16px 32px -8px rgba(0,0,0,.12); }

        .kpi-label {
            font-size: .75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: var(--text-muted);
            margin-bottom: .5rem;
        }
        .kpi-value {
            font-size: 2.4rem;
            font-weight: 700;
            line-height: 1;
            margin: 0;
        }

        .kpi-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .kpi-icon--primary { background: rgba(79,70,229,.12); color: #4f46e5 !important; }
        .kpi-icon--warning { background: rgba(245,158,11,.12); color: #f59e0b !important; }
        .kpi-icon--danger  { background: rgba(239,68,68,.12);  color: #ef4444 !important; }
        .kpi-icon i { color: inherit !important; }

        /* Ações Rápidas */
        .quick-action-btn {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.25rem;
            border: 1px solid var(--border);
            border-radius: 16px;
            text-decoration: none;
            color: var(--text-main);
            background: var(--bg-surface);
            transition: all .25s;
            width: 100%;
        }
        .quick-action-btn:hover {
            border-color: var(--primary);
            box-shadow: 0 4px 20px rgba(79,70,229,.12);
            transform: translateY(-2px);
            color: var(--text-main);
        }
        .qa-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .qa-icon--primary { background: rgba(79,70,229,.12); color: #4f46e5 !important; }
        .qa-icon--accent  { background: rgba(14,165,233,.12); color: #0ea5e9 !important; }
        .qa-icon--success { background: rgba(16,185,129,.12); color: #10b981 !important; }
        .qa-icon--warning { background: rgba(245,158,11,.12); color: #f59e0b !important; }
        .qa-icon i { color: inherit !important; }

        .qa-title { margin: 0; font-weight: 600; font-size: .875rem; }
        .qa-sub   { margin: 0; font-size: .72rem; color: var(--text-muted); }

        /* Chart card body height */
        .page-content { min-height: calc(100vh - 80px); }
    </style>

</body>
</html>