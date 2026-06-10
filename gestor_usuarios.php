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
                <li class="nav-item"><a href="gestor_dashboard.php" class="nav-link"><i class="ph ph-chart-line"></i><span>Dashboard</span></a></li>
                <li class="nav-item"><a href="gestor_chamados.php" class="nav-link"><i class="ph ph-list-bullets"></i><span>Chamados</span></a></li>
                <li class="nav-item"><a href="gestor_ambientes.php" class="nav-link"><i class="ph ph-buildings"></i><span>Ambientes</span></a></li>
                <li class="nav-item"><a href="gestor_tecnicos.php" class="nav-link"><i class="ph ph-wrench"></i><span>Técnicos</span></a></li>
                <li class="nav-item"><a href="gestor_usuarios.php" class="nav-link active"><i class="ph ph-users"></i><span>Usuários</span></a></li>
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
                    <h1>Gestão de Usuários</h1>
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
                <div class="d-flex justify-content-end mb-4">
                    <button class="btn-primary" style="width: auto; padding: 0.75rem 1.5rem;" onclick="abrirModal()">
                        <i class="ph ph-user-plus"></i> Novo Usuário
                    </button>
                </div>

                <div class="card p-0 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>E-mail</th>
                                    <th>Perfil</th>
                                    <th>Status</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="listaUsuarios"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Usuário -->
    <div class="modal fade" id="modalUsuario" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content card p-4">
                <h5 class="card-title mb-4" id="modalTitulo">Novo Usuário</h5>
                <form id="formUsuario">
                    <input type="hidden" id="userId" name="id_usuario">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nome Completo</label>
                        <input type="text" id="userName" name="nome" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">E-mail</label>
                        <input type="email" id="userEmail" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Perfil</label>
                        <select id="userPerfil" name="perfil" class="form-control" required>
                            <option value="solicitante">Solicitante</option>
                            <option value="tecnico">Técnico</option>
                            <option value="gestor">Gestor</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Senha <small class="text-muted">(Deixe em branco para manter a atual)</small></label>
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
    <script src="assets/js/notificacoes.js"></script>
    <script>
        let modal;
        document.addEventListener('DOMContentLoaded', () => {
            modal = new bootstrap.Modal(document.getElementById('modalUsuario'));
            carregarUsuarios();
        });

        async function carregarUsuarios() {
            const { success, data: users, message } = sgmAsList(await sgmFetch('api/usuarios.php?acao=listar'));
            if (!success) { alert(message); return; }
            const body = document.getElementById('listaUsuarios');
            body.innerHTML = users.map(u => `
                <tr>
    <td class="fw-bold">${u.nome}</td>
    <td>${u.email}</td>
    <td class="text-capitalize small">${u.perfil}</td>
    <td>
        <span class="badge ${u.ativo == 1 ? 'status-concluido' : 'status-urgente'}">
            ${u.ativo == 1 ? 'ATIVO' : 'INATIVO'}
        </span>
    </td>
    <td class="text-center d-flex gap-2 justify-content-center">
        <button class="btn btn-sm btn-light" onclick='editarUsuario(${JSON.stringify(u)})'>
            <i class="ph ph-pencil-simple text-black"></i>
        </button>
        <button class="btn btn-sm ${u.ativo == 1 ? 'btn-outline-danger' : 'btn-outline-success'}" onclick="toggleStatus(${u.id_usuario}, ${u.ativo})">
            <i class="ph ${u.ativo == 1 ? 'ph-user-minus' : 'ph-user-plus'} text-black"></i>
        </button>
        <button class="btn btn-sm btn-outline-danger" onclick="excluirUsuario(${u.id_usuario}, '${u.nome.replace(/'/g, "\\'")}')" title="Excluir">
            <i class="ph ph-trash"></i>
        </button>
    </td>
</tr>
            `).join('');
        }

        function abrirModal() {
            document.getElementById('formUsuario').reset();
            document.getElementById('userId').value = '';
            document.getElementById('modalTitulo').innerText = 'Novo Usuário';
            modal.show();
        }

        function editarUsuario(u) {
            document.getElementById('userId').value = u.id_usuario;
            document.getElementById('userName').value = u.nome;
            document.getElementById('userEmail').value = u.email;
            document.getElementById('userPerfil').value = u.perfil;
            document.getElementById('modalTitulo').innerText = 'Editar Usuário';
            modal.show();
        }

        document.getElementById('formUsuario').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            formData.append('acao', 'salvar');
            const data = await sgmFetch('api/usuarios.php', { method: 'POST', body: formData });
            if(data.success) {
                modal.hide();
                carregarUsuarios();
            } else {
                alert(data.message);
            }
        });

        async function toggleStatus(id, current) {
            const formData = new FormData();
            formData.append('acao', 'toggle_status');
            formData.append('id_usuario', id);
            formData.append('ativo', current);
            await sgmFetch('api/usuarios.php', { method: 'POST', body: formData });
            carregarUsuarios();
        }

        async function excluirUsuario(id, nome) {
            if (!confirm(`Tem certeza que deseja excluir o usuário "${nome}"? Esta ação não pode ser desfeita.`)) return;
            const fd = new FormData();
            fd.append('acao', 'excluir');
            fd.append('id_usuario', id);
            const data = await sgmFetch('api/usuarios.php', { method: 'POST', body: fd });
            if (data.success) {
                carregarUsuarios();
            } else if (data.chamados_vinculados && data.chamados_vinculados.length) {
                const lista = data.chamados_vinculados.map(c =>
                    `#${c.id_chamado} - ${c.descricao_problema.substring(0, 40)}... (${c.status}, vínculo: ${c.vinculo})`
                ).join('\n');
                alert(`${data.message}\n\nChamados vinculados:\n${lista}`);
            } else {
                alert(data.message);
            }
        }
    </script>
</body>
</html>
