<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Acesso negado."]);
    exit;
}

$acao = $_GET['acao'] ?? 'listar';
$id_chamado = (int)($_GET['id_chamado'] ?? 0);

if ($acao === 'listar') {
    if (!$id_chamado) {
        echo json_encode(["success" => false, "message" => "ID do chamado não informado."]);
        exit;
    }

    $sql = "SELECT c.*, u.nome as usuario_nome, u.perfil 
            FROM chamados_comentarios c 
            JOIN usuarios u ON c.id_usuario = u.id_usuario 
            WHERE c.id_chamado = $id_chamado 
            ORDER BY c.data_envio DESC";
    
    $result = $conn->query($sql);
    $comentarios = $result->fetch_all(MYSQLI_ASSOC);
    
    echo json_encode(["success" => true, "data" => $comentarios]);
}
?>
