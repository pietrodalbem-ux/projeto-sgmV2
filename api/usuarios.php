<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    echo json_encode(["success" => false, "message" => "Acesso negado."]);
    exit;
}

$acao = $_GET['acao'] ?? $_POST['acao'] ?? 'listar';
$gestor_id = (int)$_SESSION['user_id'];

if ($acao === 'listar') {
    $perfil = isset($_GET['perfil']) ? $conn->real_escape_string($_GET['perfil']) : '';
    $incluirInativos = isset($_GET['incluir_inativos']);
    $where = $perfil ? "WHERE perfil = '$perfil'" : "";
    if ($perfil && !$incluirInativos) {
        $where .= " AND ativo = 1";
    }

    $sql = "SELECT id_usuario, nome, email, perfil, ativo, data_criacao FROM usuarios $where ORDER BY nome ASC";
    $result = $conn->query($sql);
    echo json_encode($result->fetch_all(MYSQLI_ASSOC));
    exit;
}

if ($acao === 'salvar') {
    $id = (int)($_POST['id_usuario'] ?? 0);
    $nome = $conn->real_escape_string(trim($_POST['nome'] ?? ''));
    $email = $conn->real_escape_string(trim($_POST['email'] ?? ''));
    $perfil = $conn->real_escape_string($_POST['perfil'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($nome === '' || $email === '' || $perfil === '') {
        echo json_encode(["success" => false, "message" => "Preencha nome, e-mail e perfil."]);
        exit;
    }

    if ($id > 0) {
        $sql = "UPDATE usuarios SET nome='$nome', email='$email', perfil='$perfil' WHERE id_usuario=$id";
        if (!empty($senha)) {
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios SET nome='$nome', email='$email', perfil='$perfil', senha_hash='$hash' WHERE id_usuario=$id";
        }
    } else {
        if (empty($senha)) {
            echo json_encode(["success" => false, "message" => "Informe uma senha para o novo usuário."]);
            exit;
        }
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (nome, email, perfil, senha_hash) VALUES ('$nome', '$email', '$perfil', '$hash')";
    }

    if ($conn->query($sql)) {
        echo json_encode(["success" => true, "message" => "Usuário salvo com sucesso!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Erro: " . $conn->error]);
    }
    exit;
}

if ($acao === 'toggle_status') {
    $id = (int)$_POST['id_usuario'];
    $status = (int)$_POST['ativo'];
    $novo_status = $status === 1 ? 0 : 1;

    if ($id === $gestor_id) {
        echo json_encode(["success" => false, "message" => "Você não pode desativar sua própria conta."]);
        exit;
    }

    if ($conn->query("UPDATE usuarios SET ativo=$novo_status WHERE id_usuario=$id")) {
        echo json_encode(["success" => true, "message" => "Status alterado!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Erro: " . $conn->error]);
    }
    exit;
}

if ($acao === 'excluir') {
    $id = (int)($_POST['id_usuario'] ?? 0);

    if (!$id) {
        echo json_encode(["success" => false, "message" => "ID não informado."]);
        exit;
    }

    if ($id === $gestor_id) {
        echo json_encode(["success" => false, "message" => "Você não pode excluir sua própria conta."]);
        exit;
    }

    $chamados = $conn->query("SELECT c.id_chamado, c.descricao_problema, c.status,
                                     CASE WHEN c.id_solicitante = $id THEN 'solicitante' ELSE 'técnico' END AS vinculo
                              FROM chamados c
                              WHERE c.id_solicitante = $id OR c.id_tecnico = $id
                              ORDER BY c.id_chamado DESC");

    if ($chamados && $chamados->num_rows > 0) {
        $lista = $chamados->fetch_all(MYSQLI_ASSOC);
        echo json_encode([
            "success" => false,
            "message" => "Não é possível excluir: existem chamados vinculados a este usuário.",
            "chamados_vinculados" => $lista
        ]);
        exit;
    }

    $comentarios = $conn->query("SELECT COUNT(*) AS total FROM chamados_comentarios WHERE id_usuario = $id");
    $totalCom = $comentarios ? (int)$comentarios->fetch_assoc()['total'] : 0;

    if ($conn->query("DELETE FROM usuarios WHERE id_usuario = $id")) {
        echo json_encode(["success" => true, "message" => "Usuário excluído com sucesso!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Erro ao excluir: " . $conn->error]);
    }
    exit;
}

echo json_encode(["success" => false, "message" => "Ação inválida."]);
