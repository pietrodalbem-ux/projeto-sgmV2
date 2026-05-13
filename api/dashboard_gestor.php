<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    echo json_encode(["success" => false, "message" => "Acesso negado."]);
    exit;
}
$sql="SELECT
            SUM(CASE WHEN status = 'aberto' THEN 1 ELSE 0 END) as abertos,
            SUM(CASE WHEN status = 'em_execucao' THEN 1 ELSE 0 END) as em_execucao,
            SUM(CASE WHEN prioridade = 'urgente' AND status != 'concluido' THEN 1 ELSE 0 END) as urgentes, 
            COUNT(*) as total
            FROM chamados";
$res = $conn->query($sql);
$dados = $res->fetch_assoc();
echo json_encode($dados);
?>