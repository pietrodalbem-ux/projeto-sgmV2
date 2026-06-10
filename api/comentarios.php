<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Acesso negado."]);
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$perfil = $_SESSION['user_perfil'];
$acao = $_GET['acao'] ?? $_POST['acao'] ?? 'listar';
$id_chamado = (int)($_GET['id_chamado'] ?? $_POST['id_chamado'] ?? 0);

function salvarAnexoComentario($file) {
    $diretorio = "../assets/uploads/";
    if (!is_dir($diretorio)) {
        mkdir($diretorio, 0777, true);
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($ext, $permitidas, true)) {
        return ["ok" => false, "message" => "Formato de imagem não permitido."];
    }
    $nome = "comentario_" . uniqid() . "." . $ext;
    if (move_uploaded_file($file['tmp_name'], $diretorio . $nome)) {
        return ["ok" => true, "path" => "assets/uploads/" . $nome];
    }
    return ["ok" => false, "message" => "Falha no upload da imagem."];
}

function removerAnexo($caminho) {
    if ($caminho) {
        $path = '../' . $caminho;
        if (file_exists($path)) {
            @unlink($path);
        }
    }
}

function colunaExiste($conn, $tabela, $coluna) {
    $t = $conn->real_escape_string($tabela);
    $c = $conn->real_escape_string($coluna);
    $r = $conn->query("SHOW COLUMNS FROM `$t` LIKE '$c'");
    return $r && $r->num_rows > 0;
}

function listarComentarios($conn, $id_chamado) {
    $sql = "SELECT c.*, u.nome AS usuario_nome, u.perfil
            FROM chamados_comentarios c
            JOIN usuarios u ON c.id_usuario = u.id_usuario
            WHERE c.id_chamado = $id_chamado
            ORDER BY c.data_envio DESC";
    $result = $conn->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

if ($acao === 'listar') {
    if (!$id_chamado) {
        echo json_encode(["success" => false, "message" => "ID do chamado não informado."]);
        exit;
    }
    echo json_encode(["success" => true, "data" => listarComentarios($conn, $id_chamado)]);
    exit;
}

if ($acao === 'salvar') {
    if (!in_array($perfil, ['tecnico', 'gestor'], true)) {
        echo json_encode(["success" => false, "message" => "Apenas técnicos podem registrar comentários."]);
        exit;
    }
    if ($perfil === 'tecnico') {
        $chk = $conn->query("SELECT id_chamado FROM chamados WHERE id_chamado = $id_chamado AND id_tecnico = $user_id");
        if (!$chk || $chk->num_rows === 0) {
            echo json_encode(["success" => false, "message" => "Chamado não atribuído a você."]);
            exit;
        }
    }

    $texto = trim($_POST['texto'] ?? '');
    if ($texto === '') {
        echo json_encode(["success" => false, "message" => "Informe o texto do comentário."]);
        exit;
    }

    $textoEsc = $conn->real_escape_string($texto);
    $caminhoVal = null;

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $up = salvarAnexoComentario($_FILES['foto']);
        if (!$up['ok']) {
            echo json_encode(["success" => false, "message" => $up['message']]);
            exit;
        }
        $caminhoVal = $up['path'];
    }

    $temAnexo = colunaExiste($conn, 'chamados_comentarios', 'caminho_arquivo');
    if ($temAnexo) {
        $caminho = $caminhoVal ? "'" . $conn->real_escape_string($caminhoVal) . "'" : "NULL";
        $sql = "INSERT INTO chamados_comentarios (id_chamado, id_usuario, texto, caminho_arquivo) VALUES ($id_chamado, $user_id, '$textoEsc', $caminho)";
    } else {
        $sql = "INSERT INTO chamados_comentarios (id_chamado, id_usuario, texto) VALUES ($id_chamado, $user_id, '$textoEsc')";
    }

    if ($conn->query($sql)) {
        if ($perfil === 'tecnico') {
            $nome_usuario = $conn->real_escape_string($_SESSION['user_nome']);
            $titulo = "Novo comentário no chamado #$id_chamado";
            $mensagem = $conn->real_escape_string("O técnico $nome_usuario registrou uma atualização.");
            $link = "gestor_detalhes.php?id=$id_chamado";
            $gestores = $conn->query("SELECT id_usuario FROM usuarios WHERE perfil = 'gestor' AND ativo = 1");
            while ($g = $gestores->fetch_assoc()) {
                $conn->query("INSERT INTO notificacoes (id_usuario, titulo, mensagem, link) VALUES ({$g['id_usuario']}, '$titulo', '$mensagem', '$link')");
            }
        }
        echo json_encode(["success" => true, "message" => "Comentário registrado!", "data" => listarComentarios($conn, $id_chamado)]);
    } else {
        echo json_encode(["success" => false, "message" => "Erro: " . $conn->error]);
    }
    exit;
}

if ($acao === 'atualizar') {
    if ($perfil !== 'tecnico') {
        echo json_encode(["success" => false, "message" => "Apenas técnicos podem editar comentários."]);
        exit;
    }

    $id_comentario = (int)($_POST['id_comentario'] ?? 0);
    $texto = trim($_POST['texto'] ?? '');

    if (!$id_comentario || $texto === '') {
        echo json_encode(["success" => false, "message" => "Dados incompletos."]);
        exit;
    }

    $com = $conn->query("SELECT * FROM chamados_comentarios WHERE id_comentario = $id_comentario AND id_usuario = $user_id");
    if (!$com || $com->num_rows === 0) {
        echo json_encode(["success" => false, "message" => "Comentário não encontrado ou sem permissão."]);
        exit;
    }
    $row = $com->fetch_assoc();
    $id_chamado = (int)$row['id_chamado'];
    $textoEsc = $conn->real_escape_string($texto);
    $setCaminho = '';
    $temAnexo = colunaExiste($conn, 'chamados_comentarios', 'caminho_arquivo');

    if ($temAnexo && isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $up = salvarAnexoComentario($_FILES['foto']);
        if (!$up['ok']) {
            echo json_encode(["success" => false, "message" => $up['message']]);
            exit;
        }
        removerAnexo($row['caminho_arquivo'] ?? null);
        $setCaminho = ", caminho_arquivo = '" . $conn->real_escape_string($up['path']) . "'";
    }

    if ($conn->query("UPDATE chamados_comentarios SET texto = '$textoEsc' $setCaminho WHERE id_comentario = $id_comentario")) {
        echo json_encode(["success" => true, "message" => "Comentário atualizado!", "data" => listarComentarios($conn, $id_chamado)]);
    } else {
        echo json_encode(["success" => false, "message" => "Erro: " . $conn->error]);
    }
    exit;
}

if ($acao === 'excluir') {
    if ($perfil !== 'tecnico') {
        echo json_encode(["success" => false, "message" => "Apenas técnicos podem excluir comentários."]);
        exit;
    }

    $id_comentario = (int)($_POST['id_comentario'] ?? 0);
    $com = $conn->query("SELECT * FROM chamados_comentarios WHERE id_comentario = $id_comentario AND id_usuario = $user_id");

    if (!$com || $com->num_rows === 0) {
        echo json_encode(["success" => false, "message" => "Comentário não encontrado ou sem permissão."]);
        exit;
    }

    $row = $com->fetch_assoc();
    $id_chamado = (int)$row['id_chamado'];
    removerAnexo($row['caminho_arquivo'] ?? null);

    if ($conn->query("DELETE FROM chamados_comentarios WHERE id_comentario = $id_comentario")) {
        echo json_encode(["success" => true, "message" => "Comentário excluído.", "data" => listarComentarios($conn, $id_chamado)]);
    } else {
        echo json_encode(["success" => false, "message" => "Erro: " . $conn->error]);
    }
    exit;
}

echo json_encode(["success" => false, "message" => "Ação inválida."]);
