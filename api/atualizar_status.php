<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

// Proteção: Apenas Técnicos ou Gestores
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_perfil'], ['gestor', 'tecnico'])) {
    echo json_encode(["success" => false, "message" => "Acesso negado."]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$id_chamado = (int)($input['id_chamado'] ?? 0);
$status = $conn->real_escape_string($input['status'] ?? '');
$observacao = $conn->real_escape_string($input['observacao'] ?? '');

if (!$id_chamado || !$status) {
    echo json_encode(["success" => false, "message" => "Dados incompletos."]);
    exit;
}

// Inicia transação para garantir que o comentário e o status sejam atualizados juntos
$conn->begin_transaction();

try {
    // 1. Atualiza o status do chamado
    $sqlStatus = "UPDATE chamados SET status = '$status' WHERE id_chamado = $id_chamado";
    $conn->query($sqlStatus);

    // 2. Se houver observação, insere como comentário e notifica gestores
    if (!empty($observacao)) {
        $id_usuario = $_SESSION['user_id'];
        $nome_usuario = $_SESSION['user_nome'];
        
        $sqlObs = "INSERT INTO chamados_comentarios (id_chamado, id_usuario, texto) VALUES ($id_chamado, $id_usuario, '$observacao')";
        $conn->query($sqlObs);

        // Notifica todos os gestores
        $titulo = "Novo comentário no chamado #$id_chamado";
        $mensagem = "O técnico $nome_usuario adicionou uma observação: " . (strlen($observacao) > 50 ? substr($observacao, 0, 47) . "..." : $observacao);
        $link = "gestor_detalhes.php?id=$id_chamado";

        $sqlGestores = "SELECT id_usuario FROM usuarios WHERE perfil = 'gestor' AND ativo = 1";
        $resGestores = $conn->query($sqlGestores);
        while ($g = $resGestores->fetch_assoc()) {
            $id_gestor = $g['id_usuario'];
            $sqlNotif = "INSERT INTO notificacoes (id_usuario, titulo, mensagem, link) VALUES ($id_gestor, '$titulo', '$mensagem', '$link')";
            $conn->query($sqlNotif);
        }
    }


    $conn->commit();
    echo json_encode(["success" => true, "message" => "Status atualizado com sucesso!"]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["success" => false, "message" => "Erro ao atualizar: " . $e->getMessage()]);
}
