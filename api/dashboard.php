<?php
session_start();
require_once '../config/database.php'; // Ajuste o caminho do seu arquivo de conexão
header('Content-Type: application/json');

// Descomente a verificação de sessão quando o login estiver funcionando!
if(!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor'){
    echo json_encode(["success" => false, "message" => "Acesso negado."]);
    exit;
}


// Consulta SQL que conta tudo em uma única viagem ao banco de dados
$sql = "SELECT 
            (SELECT COUNT(*) FROM chamados WHERE status = 'aberto') as novas,
            (SELECT COUNT(*) FROM chamados WHERE status = 'em_execucao') as andamento,
            (SELECT COUNT(*) FROM chamados WHERE prioridade IN ('alta', 'urgente') AND status IN ('aberto', 'agendado', 'em_execucao')) as criticos";

$result = $conn->query($sql);

if ($result) {
    $data = $result->fetch_assoc();
    echo json_encode(["success" => true, "data" => $data]);
} else {
    echo json_encode(["success" => false, "message" => "Erro ao buscar métricas: " . $conn->error]);
}
?>