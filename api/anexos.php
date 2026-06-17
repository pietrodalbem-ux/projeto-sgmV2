<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$id_chamado = isset($_GET['id_chamado']) ? (int)$_GET['id_chamado'] : 0;

if ($id_chamado > 0) {
    $sql = "SELECT * FROM chamados_anexos WHERE id_chamado = $id_chamado";
    $result = $conn->query($sql);
    $anexos = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode(["success" => true, "data" => $anexos]);
} else {
    echo json_encode(["success" => true, "data" => []]);
}
