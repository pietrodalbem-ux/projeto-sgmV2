<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'solicitante') {
    header("Location: login.php");
    exit;
}
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
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
                <i class="ph-fill ph-user-circle"></i>
                <h2>SGM | Solicitante</h2>
            </div>
            <ul class="nav-links">
                <li class="nav-item"><a href="solicitante_dashboard.php" class="nav-link"><i class="ph ph-ticket"></i><span>Meus Chamados</span></a></li>
                <li class="nav-item"><a href="solicitante_abrir_chamado.php" class="nav-link"><i class="ph ph-plus-circle"></i><span>Novo Chamado</span></a></li>
            </ul>
            <div class="mt-auto">
                <a href="api/logout.php" class="nav-link text-danger"><i class="ph ph-sign-out"></i><span>Sair</span></a>
            </div>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <div class="page-title d-flex align-items-center">
                    <button class="menu-toggle" onclick="toggleSidebar()"><i class="ph ph-list"></i></button>
                    <h1>Chamado #<?= $id ?></h1>
                </div>
                <div class="topbar-actions">
                    <span class="text-muted small">Olá, <strong><?= htmlspecialchars($nome_exibicao) ?></strong></span>
                </div>
            </header>

            <div class="p-4">
                <div id="alertaErro" class="alert alert-danger d-none"></div>
                <div id="conteudoChamado" class="card p-4" style="display:none;">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <h2 class="h5 fw-bold mb-0">Detalhes da Solicitação</h2>
                        <span id="badgeStatus" class="badge status-aberto">—</span>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6"><label class="text-muted small fw-bold text-uppercase">Local</label><p id="txtLocal" class="fw-medium">—</p></div>
                        <div class="col-md-6"><label class="text-muted small fw-bold text-uppercase">Tipo de Serviço</label><p id="txtTipo" class="fw-medium">—</p></div>
                        <div class="col-md-6"><label class="text-muted small fw-bold text-uppercase">Data de Abertura</label><p id="txtData" class="fw-medium">—</p></div>
                        <div class="col-12"><label class="text-muted small fw-bold text-uppercase">Descrição</label><div id="txtDescricao" class="p-3 bg-main rounded border">—</div></div>
                        <div class="col-12" id="boxFoto" style="display:none;">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-1">Evidências Visuais</label>
                            <div id="containerFotos" class="d-flex flex-wrap gap-2"></div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4 mb-4 flex-wrap" id="acoesChamado"></div>


                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const idChamado = <?= $id ?>;
        
        let modalVisualizar;
        document.addEventListener('DOMContentLoaded', () => {
            modalVisualizar = new bootstrap.Modal(document.getElementById('modalVerImagem'));
        });

        function abrirModalImagem(src) {
            document.getElementById('imgModalVisualizar').src = src;
            modalVisualizar.show();
        }

        async function carregar() {
            try {
                const json = await sgmFetch(`api/chamados.php?id=${idChamado}`);
                const c = sgmAsObject(json);
                if (!c) {
                    document.getElementById('alertaErro').classList.remove('d-none');
                    document.getElementById('alertaErro').innerText = json.message || 'Chamado não encontrado.';
                    return;
                }
                document.getElementById('conteudoChamado').style.display = 'block';
                document.getElementById('txtLocal').innerText = `${c.bloco_nome} - ${c.ambiente_nome}`;
                document.getElementById('txtTipo').innerText = c.tipo_nome;
                document.getElementById('txtData').innerText = new Date(c.data_abertura).toLocaleString('pt-BR');
                document.getElementById('txtDescricao').innerText = c.descricao_problema;
                const badge = document.getElementById('badgeStatus');
                badge.innerText = c.status.toUpperCase().replace('_', ' ');
                if (c.fotos && c.fotos.length > 0) {
                    document.getElementById('boxFoto').style.display = 'block';
                    document.getElementById('containerFotos').innerHTML = c.fotos.map(f => `<img src="${f}" style="max-height:250px;max-width:100%;object-fit:contain;border-radius:8px;cursor:pointer;" onclick="abrirModalImagem(this.src)">`).join('');
                } else if (c.foto) {
                    document.getElementById('boxFoto').style.display = 'block';
                    document.getElementById('containerFotos').innerHTML = `<img src="${c.foto}" style="max-height:250px;max-width:100%;object-fit:contain;border-radius:8px;cursor:pointer;" onclick="abrirModalImagem(this.src)">`;
                }
                const acoes = document.getElementById('acoesChamado');
                acoes.innerHTML = `<a href="solicitante_dashboard.php" class="btn btn-light">Voltar</a>`;
                if (c.status === 'aberto') {
                    acoes.innerHTML += `
                        <a href="solicitante_editar_chamado.php?id=${idChamado}" class="btn btn-warning">Editar</a>
                        <button class="btn btn-danger" onclick="excluirChamado()">Excluir</button>`;
                }
            } catch (e) {
                document.getElementById('alertaErro').classList.remove('d-none');
                document.getElementById('alertaErro').innerText = 'Erro ao carregar chamado.';
            }
        }



        async function excluirChamado() {
            if (!confirm('Tem certeza que deseja excluir este chamado? Esta ação não pode ser desfeita.')) return;
            const data = await sgmFetch('api/chamados.php', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_chamado: idChamado })
            });
            if (data.success) {
                alert(data.message);
                window.location.href = 'solicitante_dashboard.php';
            } else {
                alert(data.message);
            }
        }

        carregar();
    </script>

    <!-- Modal para visualização de imagem em tamanho maior -->
    <div class="modal fade" id="modalVerImagem" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-body p-0 text-center position-relative">
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    <img id="imgModalVisualizar" src="" class="img-fluid rounded shadow-lg" style="max-height: 85vh; border: 3px solid rgba(255, 255, 255, 0.25);">
                </div>
            </div>
        </div>
    </div>
</body>
</html>
