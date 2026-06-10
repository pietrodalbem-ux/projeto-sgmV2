<?php
$sgmScriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$sgmBase = ($sgmScriptDir === '/' || $sgmScriptDir === '.') ? '' : rtrim($sgmScriptDir, '/') . '/';
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGM - Sistema de Gestão de Manutenção</title>
    <script>window.SGM_BASE = <?= json_encode($sgmBase) ?>;</script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= htmlspecialchars($sgmBase) ?>assets/css/style.css">

    <script src="<?= htmlspecialchars($sgmBase) ?>assets/js/theme.js"></script>
    <script src="<?= htmlspecialchars($sgmBase) ?>assets/js/api.js"></script>
    <link rel="icon" href="<?= htmlspecialchars($sgmBase) ?>assets/img/engrenagem.png" type="image/png">
</head>
