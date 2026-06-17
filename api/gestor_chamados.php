<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_perfil'], ['gestor', 'tecnico'])) {
    echo json_encode(["success" => false, "message" => "Acesso negado.", "data" => null]);
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$status = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';
$id_tecnico = isset($_GET['id_tecnico']) ? (int)$_GET['id_tecnico'] : 0;

$conditions = ["1=1"];

if ($id > 0) {
    $conditions[] = "c.id_chamado = $id";
}
if ($status !== '') {
    $conditions[] = "c.status = '$status'";
}
if ($id_tecnico > 0) {
    $conditions[] = "c.id_tecnico = $id_tecnico";
}

$whereClause = "WHERE " . implode(" AND ", $conditions);

$sql = "SELECT c.id_chamado,
               c.descricao_problema,
               c.descricao_problema AS descricao,
               c.status,
               c.prioridade,
               c.data_abertura,
               c.data_previsao_conclusao,
               c.id_tecnico,
               c.id_solicitante,
               a.nome AS ambiente_nome,
               b.nome AS bloco_nome,
               u.nome AS solicitante_nome,
               t.nome AS tecnico_nome,
               (SELECT caminho_arquivo FROM chamados_anexos WHERE id_chamado = c.id_chamado ORDER BY id_anexo ASC LIMIT 1) AS foto
        FROM chamados c
        LEFT JOIN ambientes a ON c.id_ambiente = a.id_ambiente
        LEFT JOIN blocos b ON a.id_bloco = b.id_bloco
        LEFT JOIN usuarios u ON c.id_solicitante = u.id_usuario
        LEFT JOIN usuarios t ON c.id_tecnico = t.id_usuario
        $whereClause
        ORDER BY CASE
            WHEN c.prioridade = 'urgente' THEN 1
            WHEN c.prioridade = 'alta' THEN 2
            WHEN c.prioridade = 'media' THEN 3
            ELSE 4 END, c.data_abertura DESC";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode(["success" => false, "message" => $conn->error, "data" => $id > 0 ? null : []]);
    exit;
}

$dados = $result->fetch_all(MYSQLI_ASSOC);

if ($id > 0) {
    $row = $dados[0] ?? null;
    if ($row) {
        $anexos = $conn->query("SELECT caminho_arquivo FROM chamados_anexos WHERE id_chamado = $id ORDER BY id_anexo ASC");
        $fotos = [];
        if ($anexos) {
            while ($a = $anexos->fetch_assoc()) {
                $fotos[] = $a['caminho_arquivo'];
            }
        }
        $row['fotos'] = $fotos;
    }
    echo json_encode(["success" => true, "data" => $row]);
} else {
    echo json_encode(["success" => true, "data" => $dados]);
}
