<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'solicitante') {
    header("Location: login.php");
    exit;
}
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$nome_exibicao = $_SESSION['user_nome'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<?php include 'layout/head.php'; ?>
<body>
    <div class="app-container">
        <aside class="sidebar">
            <div class="sidebar-header"><i class="ph-fill ph-user-circle"></i><h2>SGM | Solicitante</h2></div>
            <ul class="nav-links">
                <li class="nav-item"><a href="solicitante_dashboard.php" class="nav-link"><i class="ph ph-ticket"></i><span>Meus Chamados</span></a></li>
                <li class="nav-item"><a href="solicitante_abrir_chamado.php" class="nav-link"><i class="ph ph-plus-circle"></i><span>Novo Chamado</span></a></li>
            </ul>
            <div class="mt-auto"><a href="api/logout.php" class="nav-link text-danger"><i class="ph ph-sign-out"></i><span>Sair</span></a></div>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <div class="page-title d-flex align-items-center">
                    <button class="menu-toggle" onclick="toggleSidebar()"><i class="ph ph-list"></i></button>
                    <h1>Editar Chamado #<?= $id ?></h1>
                </div>
                <div class="topbar-actions"><span class="text-muted small">Olá, <strong><?= htmlspecialchars($nome_exibicao) ?></strong></span></div>
            </header>

            <div class="p-4 d-flex justify-content-center">
                <div class="card w-100" style="max-width:600px;">
                    <div id="alertaErro" class="alert alert-danger d-none"></div>
                    <form id="formEditar" enctype="multipart/form-data" style="display:none;">
                        <input type="hidden" name="id_chamado" value="<?= $id ?>">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Bloco</label>
                            <select id="bloco" class="form-control" required onchange="carregarAmbientes(this.value)"></select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Ambiente / Sala</label>
                            <select id="sala" name="id_ambiente" class="form-control" required></select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tipo de Serviço</label>
                            <select id="tipo" name="id_tipo" class="form-control" required></select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Descrição do Problema</label>
                            <textarea id="descricao" name="descricao" class="form-control" rows="4" required></textarea>
                        </div>
                        <div class="mb-3" id="boxFotoAtual" style="display:none;">
                            <label class="form-label fw-bold">Foto atual</label><br>
                            <img id="fotoAtual" src="" style="max-height:120px;border-radius:8px;">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Nova foto (opcional)</label>
                            <input type="file" id="foto" name="foto" accept="image/*" class="form-control">
                        </div>
                        <div class="d-flex gap-2">
                            <a href="solicitante_visualizar.php?id=<?= $id ?>" class="btn btn-light w-50 text-center">Cancelar</a>
                            <button class="btn-primary w-50" type="submit" id="btnSalvar">Salvar Alterações</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const idChamado = <?= $id ?>;
        let idBlocoAtual = 0;

        function preencherSelect(id, dados, campoId, campoNome, padrao) {
            const sel = document.getElementById(id);
            let html = `<option value="">${padrao}</option>`;
            dados.forEach(i => { html += `<option value="${i[campoId]}">${i[campoNome]}</option>`; });
            sel.innerHTML = html;
        }

        async function carregarAmbientes(id_bloco, idAmbienteSelecionado = null) {
            const sel = document.getElementById('sala');
            if (!id_bloco) { sel.innerHTML = '<option value="">Selecione o bloco</option>'; return; }
            const ambientes = sgmAsList(await sgmFetch(`api/localizacoes.php?acao=listar_ambientes&id_bloco=${id_bloco}`)).data;
            preencherSelect('sala', ambientes, 'id_ambiente', 'nome', 'Selecione a sala...');
            if (idAmbienteSelecionado) sel.value = idAmbienteSelecionado;
        }

        async function iniciar() {
            const chamado = sgmAsObject(await sgmFetch(`api/chamados.php?id=${idChamado}`));
            const blocos = sgmAsList(await sgmFetch('api/localizacoes.php?acao=listar_blocos')).data;
            const tipos = sgmAsList(await sgmFetch('api/localizacoes.php?acao=listar_tipos')).data;

            if (!chamado || chamado.status !== 'aberto') {
                document.getElementById('alertaErro').classList.remove('d-none');
                document.getElementById('alertaErro').innerText = 'Este chamado não pode ser editado.';
                return;
            }

            preencherSelect('bloco', blocos, 'id_bloco', 'nome', 'Selecione o bloco...');
            preencherSelect('tipo', tipos, 'id_tipo', 'nome', 'Selecione o tipo...');
            idBlocoAtual = chamado.id_bloco;
            document.getElementById('bloco').value = idBlocoAtual;
            await carregarAmbientes(idBlocoAtual, chamado.id_ambiente);
            document.getElementById('tipo').value = chamado.id_tipo_servico;
            document.getElementById('descricao').value = chamado.descricao_problema;
            if (chamado.foto) {
                document.getElementById('boxFotoAtual').style.display = 'block';
                document.getElementById('fotoAtual').src = chamado.foto;
            }
            document.getElementById('formEditar').style.display = 'block';
        }

        document.getElementById('bloco').addEventListener('change', e => carregarAmbientes(e.target.value));

        document.getElementById('formEditar').addEventListener('submit', async e => {
            e.preventDefault();
            const btn = document.getElementById('btnSalvar');
            btn.disabled = true;
            const fd = new FormData(e.target);
            fd.append('id_ambiente', document.getElementById('sala').value);
            fd.append('id_tipo', document.getElementById('tipo').value);
            fd.append('descricao', document.getElementById('descricao').value);
            const foto = document.getElementById('foto');
            if (foto.files.length) fd.append('foto', foto.files[0]);
            const data = await sgmFetch('api/atualizar_chamado.php', { method: 'POST', body: fd });
            if (data.success) {
                alert(data.message);
                window.location.href = `solicitante_visualizar.php?id=${idChamado}`;
            } else {
                alert(data.message);
                btn.disabled = false;
            }
        });

        iniciar();
    </script>
</body>
</html>
