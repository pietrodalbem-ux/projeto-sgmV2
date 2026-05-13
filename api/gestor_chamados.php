<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

// Proteção: Apenas Gestores e Técnicos
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_perfil'], ['gestor', 'tecnico'])) {
    echo json_encode(["success" => false, "message" => "Acesso negado."]);
    exit;
}


// 1. Captura filtros da URL (GET)
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$status = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';
$id_tecnico = isset($_GET['id_tecnico']) ? (int)$_GET['id_tecnico'] : null;

// 2. Monta a cláusula WHERE dinâmica
$conditions = ["1=1"];

if ($id) {
    $conditions[] = "c.id_chamado = $id";
}
if ($status) {
    $conditions[] = "c.status = '$status'";
}
if ($id_tecnico) {
    $conditions[] = "c.id_tecnico = $id_tecnico";
}

$whereClause = "WHERE " . implode(" AND ", $conditions);

// 3. SQL corrigido conforme seu banco sgm_db (3).sql
// Adicionamos 'data_previsao_conclusao' e 'id_tecnico' para a página de detalhes
$sql = "SELECT c.id_chamado, 
               c.descricao_problema, 
               c.descricao_problema as descricao, -- Alias para compatibilidade JS
               c.status, 
               c.prioridade, 
               c.data_abertura, 
               c.data_previsao_conclusao,
               c.id_tecnico,
               a.nome as ambiente_nome, 
               b.nome as bloco_nome,
               u.nome as solicitante_nome, 
               t.nome as tecnico_nome,
               (SELECT caminho_arquivo FROM chamados_anexos WHERE id_chamado = c.id_chamado LIMIT 1) as foto
        FROM chamados c
        JOIN ambientes a ON c.id_ambiente = a.id_ambiente
        JOIN blocos b ON a.id_bloco = b.id_bloco
        JOIN usuarios u ON c.id_solicitante = u.id_usuario
        LEFT JOIN usuarios t ON c.id_tecnico = t.id_usuario
        $whereClause
        ORDER BY CASE 
            WHEN c.prioridade = 'urgente' THEN 1 
            WHEN c.prioridade = 'alta' THEN 2 
            WHEN c.prioridade = 'media' THEN 3
            ELSE 4 END, c.data_abertura DESC";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode(["success" => false, "message" => $conn->error]);
    exit;
}

$dados = $result->fetch_all(MYSQLI_ASSOC);

// 4. Se foi solicitado um ID específico, retorna apenas o objeto (não o array)
if ($id) {
    echo json_encode($dados[0] ?? null);
} else {
    echo json_encode($dados);
}