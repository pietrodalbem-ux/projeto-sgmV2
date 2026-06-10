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
        <aside class="sidebar">
            <div class="sidebar-header">
                <i class="ph-fill ph-shield-check"></i>
                <h2>SGM | Gestor</h2>
            </div>
            <ul class="nav-links">
                <li class="nav-item"><a href="gestor_dashboard.php" class="nav-link"><i class="ph ph-chart-line"></i><span>Dashboard</span></a></li>
                <li class="nav-item"><a href="gestor_chamados.php" class="nav-link"><i class="ph ph-list-bullets"></i><span>Chamados</span></a></li>
                <li class="nav-item"><a href="gestor_ambientes.php" class="nav-link"><i class="ph ph-buildings"></i><span>Ambientes</span></a></li>
                <li class="nav-item"><a href="gestor_tecnicos.php" class="nav-link active"><i class="ph ph-wrench"></i><span>Técnicos</span></a></li>
                <li class="nav-item"><a href="gestor_usuarios.php" class="nav-link"><i class="ph ph-users"></i><span>Usuários</span></a></li>
            </ul>
            <div class="mt-auto">
                <a href="api/logout.php" class="nav-link text-danger"><i class="ph ph-sign-out"></i><span>Sair</span></a>
            </div>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <div class="page-title d-flex align-items-center">
                    <button class="menu-toggle" onclick="toggleSidebar()"><i class="ph ph-list"></i></button>
                    <h1>Gestão de Técnicos</h1>
                </div>
                <div class="topbar-actions">
                    <span class="text-muted small">Olá, <strong><?= htmlspecialchars($nome_exibicao) ?></strong></span>
                </div>
            </header>

            <div class="p-4">
                <div class="d-flex justify-content-end mb-4">
                    <button class="btn-primary" style="width:auto;padding:0.75rem 1.5rem;" onclick="abrirModal()">
                        <i class="ph ph-user-plus"></i> Novo Técnico
                    </button>
                </div>
                <div class="card p-0 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>E-mail</th>
                                    <th>Status</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="listaTecnicos"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div class="modal fade" id="modalTecnico" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content card p-4">
                <h5 class="card-title mb-4" id="modalTitulo">Novo Técnico</h5>
                <form id="formTecnico">
                    <input type="hidden" id="userId" name="id_usuario">
                    <input type="hidden" name="perfil" value="tecnico">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nome Completo</label>
                        <input type="text" id="userName" name="nome" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">E-mail</label>
                        <input type="email" id="userEmail" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Senha <small class="text-muted">(Deixe em branco para manter na edição)</small></label>
                        <input type="password" id="userSenha" name="senha" class="form-control">
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let modal;
        document.addEventListener('DOMContentLoaded', () => {
            modal = new bootstrap.Modal(document.getElementById('modalTecnico'));
            carregarTecnicos();
        });

        async function carregarTecnicos() {
            const { success, data: users, message } = sgmAsList(await sgmFetch('api/usuarios.php?acao=listar&perfil=tecnico&incluir_inativos=1'));
            if (!success) { alert(message); return; }
            const body = document.getElementById('listaTecnicos');
            if (!users.length) {
                body.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-muted">Nenhum técnico cadastrado.</td></tr>';
                return;
            }
            body.innerHTML = users.map(u => `
                <tr>
                    <td class="fw-bold">${u.nome}</td>
                    <td>${u.email}</td>
                    <td><span class="badge ${u.ativo == 1 ? 'status-concluido' : 'status-urgente'}">${u.ativo == 1 ? 'ATIVO' : 'INATIVO'}</span></td>
                    <td class="text-center d-flex gap-2 justify-content-center">
                        <button class="btn btn-sm btn-light" onclick='editarTecnico(${JSON.stringify(u)})'><i class="ph ph-pencil-simple"></i></button>
                        <button class="btn btn-sm ${u.ativo == 1 ? 'btn-outline-danger' : 'btn-outline-success'}" onclick="toggleStatus(${u.id_usuario}, ${u.ativo})">
                            <i class="ph ${u.ativo == 1 ? 'ph-user-minus' : 'ph-user-plus'}"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="excluirTecnico(${u.id_usuario}, '${u.nome.replace(/'/g, "\\'")}')"><i class="ph ph-trash"></i></button>
                    </td>
                </tr>`).join('');
        }

        function abrirModal() {
            document.getElementById('formTecnico').reset();
            document.getElementById('userId').value = '';
            document.getElementById('modalTitulo').innerText = 'Novo Técnico';
            modal.show();
        }

        function editarTecnico(u) {
            document.getElementById('userId').value = u.id_usuario;
            document.getElementById('userName').value = u.nome;
            document.getElementById('userEmail').value = u.email;
            document.getElementById('modalTitulo').innerText = 'Editar Técnico';
            modal.show();
        }

        document.getElementById('formTecnico').addEventListener('submit', async (e) => {
            e.preventDefault();
            const fd = new FormData(e.target);
            fd.append('acao', 'salvar');
            fd.append('perfil', 'tecnico');
            const data = await sgmFetch('api/usuarios.php', { method: 'POST', body: fd });
            if (data.success) { modal.hide(); carregarTecnicos(); }
            else alert(data.message);
        });

        async function toggleStatus(id, current) {
            const fd = new FormData();
            fd.append('acao', 'toggle_status');
            fd.append('id_usuario', id);
            fd.append('ativo', current);
            await sgmFetch('api/usuarios.php', { method: 'POST', body: fd });
            carregarTecnicos();
        }

        async function excluirTecnico(id, nome) {
            if (!confirm(`Excluir o técnico "${nome}"? Esta ação não pode ser desfeita.`)) return;
            const fd = new FormData();
            fd.append('acao', 'excluir');
            fd.append('id_usuario', id);
            const data = await sgmFetch('api/usuarios.php', { method: 'POST', body: fd });
            if (data.success) carregarTecnicos();
            else if (data.chamados_vinculados?.length) {
                const lista = data.chamados_vinculados.map(c => `#${c.id_chamado} - ${c.descricao_problema.substring(0, 40)}...`).join('\n');
                alert(`${data.message}\n\nChamados vinculados:\n${lista}`);
            } else alert(data.message);
        }
    </script>
</body>
</html>
