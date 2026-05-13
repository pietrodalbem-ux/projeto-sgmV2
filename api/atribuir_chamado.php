<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

// Proteção: Apenas Gestores
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    echo json_encode(["success" => false, "message" => "Acesso negado."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->id_chamado)) {
    echo json_encode(["success" => false, "message" => "ID do chamado não informado."]);
    exit;
}

$id_chamado = (int)$data->id_chamado;
$id_tecnico = isset($data->id_tecnico) ? (int)$data->id_tecnico : 0;
$prioridade = $conn->real_escape_string($data->prioridade ?? 'baixa');
$previsao = !empty($data->data_previsao_conclusao) ? "'" . $conn->real_escape_string($data->data_previsao_conclusao) . "'" : "NULL";
$status = $conn->real_escape_string($data->status ?? 'aberto');

// Se atribuir um técnico e o status for 'aberto', muda para 'em_execucao' automaticamente
if ($id_tecnico > 0 && $status === 'aberto') {
    $status = 'em_execucao';
}

$sql = "UPDATE chamados SET 
        id_tecnico = " . ($id_tecnico > 0 ? $id_tecnico : "NULL") . ", 
        prioridade = '$prioridade', 
        data_previsao_conclusao = $previsao, 
        status = '$status' 
        WHERE id_chamado = $id_chamado";

if ($conn->query($sql)) {
    echo json_encode(["success" => true, "message" => "Chamado atualizado com sucesso!"]);
} else {
    echo json_encode(["success" => false, "message" => "Erro ao atualizar: " . $conn->error]);
}