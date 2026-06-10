<?php
// api/gestor_relatorio_pdf.php
session_start();
require_once '../config/database.php';
require_once '../vendor/autoload.php'; // dompdf autoloader

// Verifica permissões (gestor ou técnico)
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_perfil'], ['gestor', 'tecnico'])) {
    echo json_encode(["success" => false, "message" => "Acesso negado."]);
    exit;
}

// ---------- Parâmetros de período ----------
$period = $_GET['period'] ?? 'month'; // day, week, month, year ou custom
$start  = $_GET['start'] ?? null;
$end    = $_GET['end'] ?? null;

$now = new DateTime();
switch ($period) {
    case 'day':
        $start = $now->format('Y-m-d');
        $end   = $now->format('Y-m-d');
        break;
    case 'week':
        $start = $now->modify('monday this week')->format('Y-m-d');
        $end   = $now->modify('sunday this week')->format('Y-m-d');
        break;
    case 'month':
        $start = $now->format('Y-m-01');
        $end   = $now->format('Y-m-t');
        break;
    case 'year':
        $start = $now->format('Y-01-01');
        $end   = $now->format('Y-12-31');
        break;
    case 'custom':
        if (empty($_GET['start']) || empty($_GET['end'])) {
            echo json_encode(["success" => false, "message" => "Período custom requer start e end."]);
            exit;
        }
        $start = $_GET['start'];
        $end = $_GET['end'];
        break;
    default:
        echo json_encode(["success" => false, "message" => "Período inválido."]);
        exit;
}

// Ajuste para garantir que o BETWEEN pegue o dia final inteiro (até 23:59:59)
$dbStart = $conn->real_escape_string($start) . " 00:00:00";
$dbEnd   = $conn->real_escape_string($end) . " 23:59:59";

// ---------- Consulta ao banco ----------
$query = "SELECT c.id_chamado, c.descricao_problema, c.status, c.prioridade,
                 c.data_abertura, c.data_previsao_conclusao,
                 u.nome AS solicitante, t.nome AS tecnico,
                 a.nome AS ambiente, b.nome AS bloco
          FROM chamados c
          JOIN ambientes a ON c.id_ambiente = a.id_ambiente
          JOIN blocos b   ON a.id_bloco = b.id_bloco
          JOIN usuarios u ON c.id_solicitante = u.id_usuario
          LEFT JOIN usuarios t ON c.id_tecnico = t.id_usuario
          WHERE c.data_abertura BETWEEN '$dbStart' AND '$dbEnd'
          ORDER BY c.data_abertura DESC";

$result = $conn->query($query);
if (!$result) {
    echo json_encode(["success" => false, "message" => "Erro na consulta."]);
    exit;
}
$rows = $result->fetch_all(MYSQLI_ASSOC);

// ---------- Monta HTML usando template ----------
ob_start();
$periodLabel = ucfirst($period);
include '../layout/relatorio_template.php';
$html = ob_get_clean();

// ---------- Gera PDF com Dompdf ----------
use Dompdf\Dompdf;
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$filename = "relatorio_chamados_{$period}_" . date('Ymd') . ".pdf";
header('Content-Type: application/pdf');
header("Content-Disposition: attachment; filename=\"{$filename}\"");
echo $dompdf->output();
?>