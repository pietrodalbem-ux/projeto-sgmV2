<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Acesso negado."]);
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$perfil = $_SESSION['user_perfil'];
$id_chamado = (int)($_POST['id_chamado'] ?? 0);

if (!$id_chamado) {
    echo json_encode(["success" => false, "message" => "ID do chamado não informado."]);
    exit;
}

$res = $conn->query("SELECT * FROM chamados WHERE id_chamado = $id_chamado");
$chamado = $res ? $res->fetch_assoc() : null;

if (!$chamado) {
    echo json_encode(["success" => false, "message" => "Chamado não encontrado."]);
    exit;
}

if ($perfil === 'gestor') {
    // gestor pode editar tudo
} elseif ($perfil === 'solicitante' && (int)$chamado['id_solicitante'] === $user_id) {
    if ($chamado['status'] !== 'aberto') {
        echo json_encode(["success" => false, "message" => "Apenas chamados Abertos podem ser editados."]);
        exit;
    }
} else {
    echo json_encode(["success" => false, "message" => "Sem permissão para editar este chamado."]);
    exit;
}

$id_ambiente = (int)($_POST['id_ambiente'] ?? $chamado['id_ambiente']);
$id_tipo = (int)($_POST['id_tipo'] ?? $chamado['id_tipo_servico']);
$descricao = $conn->real_escape_string(trim($_POST['descricao'] ?? $chamado['descricao_problema']));

if (!$id_ambiente || !$id_tipo || $descricao === '') {
    echo json_encode(["success" => false, "message" => "Preencha ambiente, tipo e descrição."]);
    exit;
}

$sets = ["descricao_problema = '$descricao'", "id_ambiente = $id_ambiente", "id_tipo_servico = $id_tipo"];

if ($perfil === 'gestor') {
    if (isset($_POST['prioridade'])) {
        $prioridade = $conn->real_escape_string($_POST['prioridade']);
        $sets[] = "prioridade = '$prioridade'";
    }
    if (isset($_POST['status'])) {
        $status = $conn->real_escape_string($_POST['status']);
        $sets[] = "status = '$status'";
    }
    if (array_key_exists('data_previsao_conclusao', $_POST)) {
        $prev = trim($_POST['data_previsao_conclusao']);
        $sets[] = $prev !== '' ? "data_previsao_conclusao = '" . $conn->real_escape_string($prev) . "'" : "data_previsao_conclusao = NULL";
    }
    if (isset($_POST['id_tecnico'])) {
        $id_tec = (int)$_POST['id_tecnico'];
        $sets[] = $id_tec > 0 ? "id_tecnico = $id_tec" : "id_tecnico = NULL";
    }
}

$sql = "UPDATE chamados SET " . implode(', ', $sets) . " WHERE id_chamado = $id_chamado";

if (!$conn->query($sql)) {
    echo json_encode(["success" => false, "message" => "Erro ao atualizar: " . $conn->error]);
    exit;
}

if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $diretorio = "../assets/uploads/";
    if (!is_dir($diretorio)) {
        mkdir($diretorio, 0777, true);
    }

    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    $permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($ext, $permitidas, true)) {
        echo json_encode(["success" => false, "message" => "Formato de imagem não permitido."]);
        exit;
    }

    $old = $conn->query("SELECT id_anexo, caminho_arquivo FROM chamados_anexos WHERE id_chamado = $id_chamado AND tipo_anexo = 'abertura'");
    if ($old && $o = $old->fetch_assoc()) {
        $path = '../' . $o['caminho_arquivo'];
        if (file_exists($path)) {
            @unlink($path);
        }
        $conn->query("DELETE FROM chamados_anexos WHERE id_anexo = " . (int)$o['id_anexo']);
    }

    $nome_arquivo = "abertura_" . uniqid() . "." . $ext;
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $diretorio . $nome_arquivo)) {
        $caminho_db = "assets/uploads/" . $nome_arquivo;
        $conn->query("INSERT INTO chamados_anexos (id_chamado, caminho_arquivo, tipo_anexo) VALUES ($id_chamado, '$caminho_db', 'abertura')");
    }
}

echo json_encode(["success" => true, "message" => "Chamado atualizado com sucesso!"]);
