<?php
session_start();
date_default_timezone_set('America/Sao_Paulo');
require_once '../config/database.php';

// Include FPDF library
require('../lib/fpdf/fpdf.php'); // Adjust path as necessary

if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    die("Acesso negado.");
}

$period = $_GET['period'] ?? 'month';
$startDate = $_GET['start'] ?? null;
$endDate = $_GET['end'] ?? null;

$title = "Relatório de Chamados";
$whereClause = "";

switch ($period) {
    case 'day':
        $title .= " - Hoje";
        $whereClause = "WHERE DATE(c.data_abertura) = CURDATE()";
        break;
    case 'week':
        $title .= " - Últimos 7 Dias";
        $whereClause = "WHERE c.data_abertura >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        break;
    case 'month':
        $title .= " - Mês Atual";
        $whereClause = "WHERE MONTH(c.data_abertura) = MONTH(CURDATE()) AND YEAR(c.data_abertura) = YEAR(CURDATE())";
        break;
    case 'year':
        $title .= " - Ano Atual";
        $whereClause = "WHERE YEAR(c.data_abertura) = YEAR(CURDATE())";
        break;
    case 'custom':
        if ($startDate && $endDate) {
            $title .= " - De " . date('d/m/Y', strtotime($startDate)) . " a " . date('d/m/Y', strtotime($endDate));
            $whereClause = "WHERE c.data_abertura BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'";
        } else {
            die("Datas customizadas são obrigatórias.");
        }
        break;
}

$sql = "SELECT
            c.id_chamado,
            c.descricao_problema,
            c.status,
            c.prioridade,
            DATE_FORMAT(c.data_abertura, '%d/%m/%Y %H:%i') as data_abertura_formatada,
            u.nome as solicitante_nome,
            a.nome as ambiente_nome,
            b.nome as bloco_nome,
            t.nome as tecnico_nome
        FROM chamados c
        LEFT JOIN usuarios u ON c.id_solicitante = u.id_usuario
        LEFT JOIN ambientes a ON c.id_ambiente = a.id_ambiente
        LEFT JOIN blocos b ON a.id_bloco = b.id_bloco
        LEFT JOIN usuarios t ON c.id_tecnico = t.id_usuario
        $whereClause
        ORDER BY c.data_abertura DESC";

$result = $conn->query($sql);

$pdf = new FPDF();
$pdf->AddPage();

// Adicionar a engrenagem
$pdf->Image('../assets/img/engrenagem.png', 10, 8, 15);

$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, utf8_decode($title), 0, 1, 'C');

$meses = ['', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
$gerado_em = "Gerado em " . date('d') . " de " . $meses[(int)date('m')] . " de " . date('Y') . " às " . date('H:i');

$pdf->SetFont('Arial', 'I', 10);
$pdf->SetTextColor(100, 100, 100);
$pdf->Cell(0, 5, utf8_decode($gerado_em), 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(200, 220, 255);
$pdf->Cell(20, 7, utf8_decode('ID'), 1, 0, 'C', true);
$pdf->Cell(50, 7, utf8_decode('Solicitante'), 1, 0, 'C', true);
$pdf->Cell(40, 7, utf8_decode('Local'), 1, 0, 'C', true);
$pdf->Cell(30, 7, utf8_decode('Status'), 1, 0, 'C', true);
$pdf->Cell(20, 7, utf8_decode('Prioridade'), 1, 0, 'C', true);
$pdf->Cell(30, 7, utf8_decode('Data Abertura'), 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 8);
$pdf->SetFillColor(240, 240, 240);
$isEvenRow = false;

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $fill = $isEvenRow;
        $pdf->Cell(20, 6, $row['id_chamado'], 1, 0, 'C', $fill);
        $pdf->Cell(50, 6, utf8_decode($row['solicitante_nome']), 1, 0, 'L', $fill);
        $pdf->Cell(40, 6, utf8_decode($row['bloco_nome'] . ' - ' . $row['ambiente_nome']), 1, 0, 'L', $fill);
        $pdf->Cell(30, 6, utf8_decode(ucfirst(str_replace('_', ' ', $row['status']))), 1, 0, 'C', $fill);
        $pdf->Cell(20, 6, utf8_decode(ucfirst($row['prioridade'])), 1, 0, 'C', $fill);
        $pdf->Cell(30, 6, utf8_decode($row['data_abertura_formatada']), 1, 1, 'C', $fill);
        $isEvenRow = !$isEvenRow;
    }
} else {
    $pdf->Cell(0, 10, utf8_decode('Nenhum chamado encontrado para o período selecionado.'), 1, 1, 'C');
}

$pdf->Output('I', 'relatorio_chamados.pdf');
$conn->close();