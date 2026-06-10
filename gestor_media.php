<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    echo json_encode(["success" => false, "message" => "Acesso negado."]);
    exit;
}

$monthlyData = [];
$annualData = [];

// Monthly data for the last 6 months
$sqlMonthly = "SELECT
                    DATE_FORMAT(data_abertura, '%Y-%m') as mes,
                    COUNT(*) as total
                FROM chamados
                WHERE data_abertura >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                GROUP BY mes
                ORDER BY mes ASC";
$resultMonthly = $conn->query($sqlMonthly);
if ($resultMonthly) {
    while ($row = $resultMonthly->fetch_assoc()) {
        $monthlyData[] = $row;
    }
}

// Annual data for the last 3 years
$sqlAnnual = "SELECT
                    YEAR(data_abertura) as ano,
                    COUNT(*) as total
                FROM chamados
                WHERE data_abertura >= DATE_SUB(CURDATE(), INTERVAL 3 YEAR)
                GROUP BY ano
                ORDER BY ano ASC";
$resultAnnual = $conn->query($sqlAnnual);
if ($resultAnnual) {
    while ($row = $resultAnnual->fetch_assoc()) {
        $annualData[] = $row;
    }
}

echo json_encode([
    "success" => true,
    "monthly" => $monthlyData,
    "annual" => $annualData
]);

$conn->close();
?>