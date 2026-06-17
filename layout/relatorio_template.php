<?php
/* layout/relatorio_template.php */
// Variáveis esperadas: $rows (array), $periodLabel, $startBr, $endBr, $gearBase64
$meses = ['', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
$gerado_em = "Gerado em " . date('d') . " de " . $meses[(int)date('m')] . " de " . date('Y') . " às " . date('H:i');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Chamados</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; 
            font-size: 11px;
            color: #333;
        }
        .header-container {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
        }
        .header-container img {
            position: absolute;
            left: 0;
            top: 0;
            width: 40px;
            opacity: 0.8;
        }
        h2 {
            margin: 0;
            padding-top: 10px;
            font-size: 20px;
            color: #222;
        }
        .periodo {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        table {
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd; 
            padding: 8px 6px; 
            text-align: left;
            vertical-align: middle;
        }
        th {
            background-color: #f4f6f9;
            color: #333;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }
        tr:nth-child(even) {
            background-color: #fafafa;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #888;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header-container">
        <svg xmlns="http://www.w3.org/2000/svg" style="position: absolute; left: 0; top: 0;" width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="#666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="3"></circle>
            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
        </svg>
        <h2>Relatório de Chamados – <?= htmlspecialchars($periodLabel) ?></h2>
        <div class="periodo">Período: <?= htmlspecialchars($startBr) ?> até <?= htmlspecialchars($endBr) ?></div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th width="5%">ID</th>
                <th width="20%">Descrição</th>
                <th width="8%">Status</th>
                <th width="8%">Prioridade</th>
                <th width="12%">Abertura</th>
                <th width="12%">Previsão</th>
                <th width="10%">Solicitante</th>
                <th width="10%">Técnico</th>
                <th width="15%">Local</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($rows) === 0): ?>
                <tr><td colspan="9" style="text-align: center;">Nenhum chamado encontrado no período.</td></tr>
            <?php else: ?>
                <?php foreach ($rows as $r): ?>
                <tr>
                    <td style="text-align: center;"><b><?= $r['id_chamado'] ?></b></td>
                    <td><?= htmlspecialchars($r['descricao_problema']) ?></td>
                    <td><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $r['status']))) ?></td>
                    <td><?= htmlspecialchars(ucfirst($r['prioridade'])) ?></td>
                    <td><?= $r['data_abertura'] ? date('d/m/Y H:i', strtotime($r['data_abertura'])) : '-' ?></td>
                    <td><?= $r['data_previsao_conclusao'] ? date('d/m/Y H:i', strtotime($r['data_previsao_conclusao'])) : '-' ?></td>
                    <td><?= htmlspecialchars($r['solicitante']) ?></td>
                    <td><?= htmlspecialchars($r['tecnico'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($r['bloco'] . ' - ' . $r['ambiente']) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <div class="footer">
        <?= $gerado_em ?>
    </div>
</body>
</html>