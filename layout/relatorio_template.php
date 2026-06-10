<?php
/* layout/relatorio_template.php */
// Variáveis esperadas: $rows (array), $period, $start, $end
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Chamados</title>
    <style>
        body {font-family: Arial, sans-serif; font-size: 12px;}
        table {width: 100%; border-collapse: collapse; margin-top: 20px;}
        th, td {border: 1px solid #444; padding: 4px; text-align: left;}
        th {background-color: #eee;}
        h2 {text-align: center;}
    </style>
</head>
<body>
    <h2>Relatório de Chamados – <?= htmlspecialchars($periodLabel ?? $period) ?></h2>
    <p>Período: <?= htmlspecialchars($start) ?> até <?= htmlspecialchars($end) ?></p>
    <table>
        <thead>
            <tr>
                <th>ID</th><th>Descrição</th><th>Status</th><th>Prioridade</th>
                <th>Abertura</th><th>Previsão</th><th>Solicitante</th>
                <th>Técnico</th><th>Ambiente</th><th>Bloco</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $r): ?>
            <tr>
                <td><?= $r['id_chamado'] ?></td>
                <td><?= htmlspecialchars($r['descricao_problema']) ?></td>
                <td><?= $r['status'] ?></td>
                <td><?= $r['prioridade'] ?></td>
                <td><?= $r['data_abertura'] ?></td>
                <td><?= $r['data_previsao_conclusao'] ?></td>
                <td><?= $r['solicitante'] ?></td>
                <td><?= $r['tecnico'] ?></td>
                <td><?= $r['ambiente'] ?></td>
                <td><?= $r['bloco'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p>Gerado em <?= date('d/m/Y H:i') ?></p>
</body>
</html>
?>