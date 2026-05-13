<?php
// api/chamados.php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Acesso negado."]);
    exit;
}

$user_id = $_SESSION['user_id'];
$perfil = $_SESSION['user_perfil'];
$id_chamado = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_chamado > 0) {
    $sql = "SELECT c.*, a.nome as ambiente_nome, b.nome as bloco_nome, u.nome 
    as solicitante_nome, t.nome as tipo_nome
            FROM chamados c
            JOIN ambientes a ON c.id_ambiente = a.id_ambiente
            JOIN blocos b ON a.id_bloco = b.id_bloco
            JOIN usuarios u ON c.id_solicitante = u.id_usuario
            JOIN tipos_servico t ON c.id_tipo_servico = t.id_tipo
            WHERE c.id_chamado = $id_chamado";
    
    $result = $conn->query($sql);
    $chamado = $result->fetch_assoc();
    
    echo json_encode($chamado);
    exit;
}

// Caso contrário, lista todos conforme o perfil
$where = ($perfil === 'solicitante') ? "WHERE c.id_solicitante = $user_id" : "";

$sql = "SELECT c.id_chamado, c.descricao_problema, c.status, c.data_abertura, 
               a.nome as ambiente_nome, b.nome as bloco_nome,
               (SELECT caminho_arquivo FROM chamados_anexos WHERE id_chamado = c.id_chamado LIMIT 1) as thumbnail
        FROM chamados c
        JOIN ambientes a ON c.id_ambiente = a.id_ambiente
        JOIN blocos b ON a.id_bloco = b.id_bloco
        $where
        ORDER BY c.data_abertura DESC";

$result = $conn->query($sql);
$chamados = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($chamados);