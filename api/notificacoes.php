<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Acesso negado."]);
    exit;
}

$id_usuario = $_SESSION['user_id'];
$acao = $_GET['acao'] ?? 'listar';

if ($acao === 'listar') {
    $sql = "SELECT * FROM notificacoes WHERE id_usuario = $id_usuario ORDER BY data_criacao DESC LIMIT 10";
    $result = $conn->query($sql);
    $notif = $result->fetch_all(MYSQLI_ASSOC);
    
    // Conta não lidas
    $sqlUnread = "SELECT COUNT(*) as total FROM notificacoes WHERE id_usuario = $id_usuario AND lida = 0";
    $resUnread = $conn->query($sqlUnread);
    $unreadCount = $resUnread->fetch_assoc()['total'];
    
    echo json_encode([
        "success" => true,
        "data" => $notif,
        "notificacoes" => $notif, // Mantido para compatibilidade com scripts antigos
        "unreadCount" => $unreadCount
    ]);
} 

elseif ($acao === 'marcar_lida') {
    $id_notif = (int)($_GET['id'] ?? 0);
    $sql = "UPDATE notificacoes SET lida = 1 WHERE id_notificacao = $id_notif AND id_usuario = $id_usuario";
    if ($conn->query($sql)) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => $conn->error]);
    }
}

elseif ($acao === 'excluir') {
    $id_notif = (int)($_GET['id'] ?? 0);
    $sql = "DELETE FROM notificacoes WHERE id_notificacao = $id_notif AND id_usuario = $id_usuario";
    if ($conn->query($sql)) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => $conn->error]);
    }
}
?>
