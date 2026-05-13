<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

// Proteção: Apenas Gestores (Técnicos podem precisar listar usuários para delegar/comentar, mas por enquanto mantemos Gestor)
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    echo json_encode(["success" => false, "message" => "Acesso negado."]);
    exit;
}

$acao = $_GET['acao'] ?? $_POST['acao'] ?? 'listar';

if ($acao === 'listar') {
    $perfil = isset($_GET['perfil']) ? $conn->real_escape_string($_GET['perfil']) : '';
    $where = $perfil ? "WHERE perfil = '$perfil' AND ativo = 1" : "";
    
    $sql = "SELECT id_usuario, nome, email, perfil, ativo, data_criacao FROM usuarios $where ORDER BY nome ASC";
    $result = $conn->query($sql);
    echo json_encode($result->fetch_all(MYSQLI_ASSOC));
} 

elseif ($acao === 'salvar') {
    $id = (int)($_POST['id_usuario'] ?? 0);
    $nome = $conn->real_escape_string($_POST['nome']);
    $email = $conn->real_escape_string($_POST['email']);
    $perfil = $conn->real_escape_string($_POST['perfil']);
    $senha = $_POST['senha'] ?? '';

    if ($id > 0) {
        $sql = "UPDATE usuarios SET nome='$nome', email='$email', perfil='$perfil' WHERE id_usuario=$id";
        if (!empty($senha)) {
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios SET nome='$nome', email='$email', perfil='$perfil', senha_hash='$hash' WHERE id_usuario=$id";
        }
    } else {
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (nome, email, perfil, senha_hash) VALUES ('$nome', '$email', '$perfil', '$hash')";
    }

    if ($conn->query($sql)) {
        echo json_encode(["success" => true, "message" => "Usuário salvo com sucesso!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Erro: " . $conn->error]);
    }
}

elseif ($acao === 'toggle_status') {
    $id = (int)$_POST['id_usuario'];
    $status = (int)$_POST['ativo'];
    $novo_status = $status === 1 ? 0 : 1;
    
    $sql = "UPDATE usuarios SET ativo=$novo_status WHERE id_usuario=$id";
    if ($conn->query($sql)) {
        echo json_encode(["success" => true, "message" => "Status alterado!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Erro: " . $conn->error]);
    }
}