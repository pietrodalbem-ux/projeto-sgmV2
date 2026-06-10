<?php 
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'solicitante') {
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
                <i class="ph-fill ph-user-circle"></i>
                <h2>SGM | Solicitante</h2>
            </div>
            
            <ul class="nav-links">
                <li class="nav-item">
                    <a href="solicitante_dashboard.php" class="nav-link active">
                        <i class="ph ph-ticket"></i>
                        <span>Meus Chamados</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="solicitante_abrir_chamado.php" class="nav-link">
                        <i class="ph ph-plus-circle"></i>
                        <span>Novo Chamado</span>
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
                    <h1>Minhas Solicitações</h1>
                </div>

                
                <div class="topbar-actions">
                    <div class="d-flex align-items-center gap-2">

                        <span class="text-muted small">Olá, <strong><?= $nome_exibicao ?></strong></span>
                        <div style="width: 32px; height: 32px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.8rem;"><?= $primeira_letra ?></div>
                    </div>
                </div>
            </header>


            <div class="p-4">
                <div class="d-flex justify-content-end mb-4">
                    <a href="solicitante_abrir_chamado.php" class="btn-primary" style="width: auto; text-decoration: none; padding: 0.75rem 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="ph ph-plus"></i>
                        Nova solicitação
                    </a>
                </div>

                <div class="card">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Foto</th>
                                    <th>Local</th>
                                    <th>Descrição</th>
                                    <th>Data</th>
                                    <th>Status</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="tabelaChamados">
                                <!-- Preenchido via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal para ver foto -->
    <div class="modal fade" id="modalFoto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-body p-0 text-center">
                    <img id="imgModal" src="" style="max-width: 100%; border-radius: 1rem;">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function verFoto(url) {
            document.getElementById('imgModal').src = url;
            new bootstrap.Modal(document.getElementById('modalFoto')).show();
        }

        async function carregarChamados() {
            const lista = document.getElementById('tabelaChamados');
            try {
                const json = await sgmFetch('api/chamados.php');
                const { success, data: chamados, message } = sgmAsList(json);

                if (!success) {
                    lista.innerHTML = `<tr><td colspan="7" class="text-center py-5 text-danger">${message}</td></tr>`;
                    return;
                }
                
                const statusStyles = {
                    'aberto': 'status-aberto',
                    'agendado': 'status-concluido',
                    'em_execucao': 'status-concluido',
                    'concluido': 'status-concluido',
                    'fechado': 'status-urgente'
                };


                if (chamados.length === 0) {
                    lista.innerHTML = '<tr><td colspan="7" class="text-center py-5 text-muted">Nenhum chamado encontrado.</td></tr>';
                    return;
                }

                lista.innerHTML = chamados.map(c => {
                    const thumbHtml = c.thumbnail ?
                        `<img src="${c.thumbnail}" style="width: 40px; height: 40px; border-radius: 8px; object-fit: cover; cursor: pointer;" onclick="verFoto('${c.thumbnail}')">` :
                        '<div style="width: 40px; height: 40px; background: var(--bg-main); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--text-muted);"><i class="ph ph-image"></i></div>';

                    const acoesAberto = c.status === 'aberto' ? `
                        <a href="solicitante_editar_chamado.php?id=${c.id_chamado}" class="btn btn-sm btn-warning" title="Editar"><i class="ph ph-pencil-simple"></i></a>
                        <button class="btn btn-sm btn-danger" title="Excluir" onclick="excluirChamado(${c.id_chamado})"><i class="ph ph-trash"></i></button>` : '';

                    return `<tr>
                        <td class="fw-bold text-muted">#${c.id_chamado}</td>
                        <td>${thumbHtml}</td>
                        <td class="fw-medium">${c.bloco_nome} - ${c.ambiente_nome}</td>
                        <td class="text-truncate" style="max-width: 200px;">${c.descricao_problema}</td>
                        <td>${new Date(c.data_abertura).toLocaleDateString()}</td>
                        <td><span class="badge ${statusStyles[c.status] || 'status-aberto'}">${c.status.toUpperCase().replace('_', ' ')}</span></td>
                        <td class="text-center">
                            <a href="solicitante_visualizar.php?id=${c.id_chamado}" class="btn btn-sm btn-light" title="Visualizar"><i class="ph ph-eye"></i></a>
                            ${acoesAberto}
                        </td>
                    </tr>`;
                }).join('');
            } catch (error) {
                console.error("Erro ao carregar chamados:", error);
                lista.innerHTML = '<tr><td colspan="6" class="text-center py-5 text-danger">Erro ao carregar chamados. Tente novamente.</td></tr>';
            }
        }

        
        async function excluirChamado(id) {
            if (!confirm('Tem certeza que deseja excluir este chamado? Esta ação não pode ser desfeita.')) return;
            const data = await sgmFetch('api/chamados.php', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_chamado: id })
            });
            if (data.success) {
                carregarChamados();
            } else {
                alert(data.message);
            }
        }

        carregarChamados();
    </script>
</body>
</html>
