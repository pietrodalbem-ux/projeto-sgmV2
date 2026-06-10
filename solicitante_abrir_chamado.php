<?php
session_start();
// Proteção de acesso: verifica se o usuário está logado e se é 'solicitante'
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
                    <a href="solicitante_dashboard.php" class="nav-link">
                        <i class="ph ph-ticket"></i>
                        <span>Meus Chamados</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="solicitante_abrir_chamado.php" class="nav-link active">
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
                    <h1>Abrir Novo Chamado</h1>
                </div>

                
                <div class="topbar-actions">
                    <div class="d-flex align-items-center gap-2">

                        <span class="text-muted small">Olá, <strong><?= $nome_exibicao ?></strong></span>
                        <div style="width: 32px; height: 32px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.8rem;"><?= $primeira_letra ?></div>
                    </div>
                </div>
            </header>


            <div class="p-4 d-flex justify-content-center">
                <div class="card w-100" style="max-width: 600px;">
                    <form id="formChamado" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Bloco</label>
                            <select id="bloco" name="id_bloco" class="form-control" required onchange="carregarAmbientes(this.value)">
                                <option value="">Carregando blocos...</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Ambiente / Sala</label>
                            <select id="sala" name="id_ambiente" class="form-control" required disabled>
                                <option value="">Selecione o Bloco primeiro...</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Tipo de Serviço</label>
                            <select id="tipo" name="id_tipo" class="form-control" required>
                                <option value="">Selecione o tipo...</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Descrição do Problema</label>
                            <textarea id="descricao" name="descricao" class="form-control" rows="4" required placeholder="Descreva o que aconteceu..."></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Foto da Ocorrência</label>
                            <input type="file" id="foto" name="foto" accept="image/*" class="form-control">
                        </div>

                        <button class="btn-primary" type="submit" id="btnEnviar">
                            Registrar Solicitação
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function preencherSelect(idElemento, dados, campoId, campoNome, textoPadrao) {
            const select = document.getElementById(idElemento);
            let html = `<option value="">${textoPadrao}</option>`;
            if (Array.isArray(dados)) {
                dados.forEach(item => {
                    html += `<option value="${item[campoId]}">${item[campoNome]}</option>`;
                });
            }
            select.innerHTML = html;
        }

        async function iniciar() {
            try {
                const blocos = sgmAsList(await sgmFetch('api/localizacoes.php?acao=listar_blocos')).data;
                preencherSelect('bloco', blocos, 'id_bloco', 'nome', 'Selecione o Bloco..');

                const tipos = sgmAsList(await sgmFetch('api/localizacoes.php?acao=listar_tipos')).data;
                preencherSelect('tipo', tipos, 'id_tipo', 'nome', 'Selecione o tipo...');
            } catch (erro) {
                console.error("Erro ao carregar dados iniciais:", erro);
            }
        }

        async function carregarAmbientes(id_bloco) {
            const selA = document.getElementById('sala');
            if (!id_bloco) {
                selA.innerHTML = '<option value="">Selecione o Bloco primeiro...</option>';
                selA.disabled = true;
                return;
            }
            try {
                selA.disabled = true;
                selA.innerHTML = '<option value="">Carregando...</option>';
                const ambientes = sgmAsList(await sgmFetch(`api/localizacoes.php?acao=listar_ambientes&id_bloco=${id_bloco}`)).data;
                preencherSelect('sala', ambientes, 'id_ambiente', 'nome', 'Selecione a Sala...');
                selA.disabled = false;
            } catch (erro) {
                selA.innerHTML = '<option value="">Erro ao carregar</option>';
            }
        }

        document.getElementById('formChamado').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('btnEnviar');
            btn.disabled = true;
            btn.innerText = "Enviando...";

            try {
                const formData = new FormData();
                formData.append('id_ambiente', document.getElementById('sala').value);
                formData.append('id_tipo', document.getElementById('tipo').value);
                formData.append('descricao', document.getElementById('descricao').value);
                const fotoInput = document.getElementById('foto');
                if (fotoInput.files.length > 0) {
                    formData.append('foto', fotoInput.files[0]);
                }

                const result = await sgmFetch('api/salvar_chamado.php', {
                    method: 'POST',
                    body: formData
                });
                if (result.success) {
                    alert(result.message);
                    window.location.href = 'solicitante_dashboard.php';
                } else {
                    alert("Erro: " + result.message);
                    btn.disabled = false;
                    btn.innerText = "Registrar Solicitação";
                }
            } catch (erro) {
                alert("Falha ao registrar chamado.");
                btn.disabled = false;
                btn.innerText = "Registrar Solicitação";
            }
        });

        iniciar();
    </script>
</body>
</html>
