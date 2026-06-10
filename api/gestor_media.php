<?php
// api/gestor_media.php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_perfil'], ['gestor', 'tecnico'])) {
    echo json_encode(["success" => false, "message" => "Acesso negado."]);
    exit;
}

// ── Últimos 6 meses: todos os chamados abertos, agrupados por mês ──────────
$monthlyQuery = "
    SELECT
        DATE_FORMAT(data_abertura, '%b/%Y') AS mes,
        DATE_FORMAT(data_abertura, '%Y-%m') AS mes_ordem,
        COUNT(*) AS total
    FROM chamados
    WHERE data_abertura >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY mes_ordem, mes
    ORDER BY mes_ordem ASC
";

// ── Últimos 3 anos: todos os chamados agrupados por ano ────────────────────
$annualQuery = "
    SELECT
        YEAR(data_abertura) AS ano,
        COUNT(*) AS total
    FROM chamados
    WHERE data_abertura >= DATE_SUB(CURDATE(), INTERVAL 3 YEAR)
    GROUP BY ano
    ORDER BY ano ASC
";

$monthly = [];
$annual  = [];

$res = $conn->query($monthlyQuery);
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $monthly[] = ["mes" => $row["mes"], "total" => (int)$row["total"]];
    }
}

$res = $conn->query($annualQuery);
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $annual[] = ["ano" => (string)$row["ano"], "total" => (int)$row["total"]];
    }
}

echo json_encode([
    "success" => true,
    "monthly" => $monthly,
    "annual"  => $annual
]);

$conn->close();
?>