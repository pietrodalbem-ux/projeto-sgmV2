<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Acesso negado.", "data" => null]);
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$perfil = $_SESSION['user_perfil'];
$method = $_SERVER['REQUEST_METHOD'];

function buscarChamado($conn, $id_chamado) {
    $id = (int)$id_chamado;
    $sql = "SELECT c.*, a.nome AS ambiente_nome, a.id_bloco, b.nome AS bloco_nome,
                   u.nome AS solicitante_nome, ts.nome AS tipo_nome, ts.id_tipo
            FROM chamados c
            LEFT JOIN ambientes a ON c.id_ambiente = a.id_ambiente
            LEFT JOIN blocos b ON a.id_bloco = b.id_bloco
            LEFT JOIN usuarios u ON c.id_solicitante = u.id_usuario
            LEFT JOIN tipos_servico ts ON c.id_tipo_servico = ts.id_tipo
            WHERE c.id_chamado = $id";
    $result = $conn->query($sql);
    return $result ? $result->fetch_assoc() : null;
}

function podeGerenciarChamado($chamado, $user_id, $perfil, $somenteAberto = false) {
    if (!$chamado) {
        return ["ok" => false, "message" => "Chamado não encontrado."];
    }
    if ($perfil === 'gestor') {
        return ["ok" => true];
    }
    if ($perfil === 'solicitante' && (int)$chamado['id_solicitante'] === $user_id) {
        if ($somenteAberto && $chamado['status'] !== 'aberto') {
            return ["ok" => false, "message" => "Apenas chamados com status Aberto podem ser alterados pelo solicitante."];
        }
        return ["ok" => true];
    }
    return ["ok" => false, "message" => "Sem permissão para esta ação."];
}

if ($method === 'GET') {
    $id_chamado = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($id_chamado > 0) {
        $chamado = buscarChamado($conn, $id_chamado);
        $perm = podeGerenciarChamado($chamado, $user_id, $perfil, false);

        if ($perfil === 'tecnico') {
            if (!$chamado || (int)$chamado['id_tecnico'] !== $user_id) {
                echo json_encode(["success" => false, "message" => "Acesso negado.", "data" => null]);
                exit;
            }
        } elseif ($perfil === 'gestor') {
            // gestor sempre pode ver
        } elseif (!$perm['ok']) {
            echo json_encode(["success" => false, "message" => $perm['message'], "data" => null]);
            exit;
        }

        if (!$chamado) {
            echo json_encode(["success" => false, "message" => "Chamado não encontrado.", "data" => null]);
            exit;
        }

        $anexos = $conn->query("SELECT caminho_arquivo FROM chamados_anexos WHERE id_chamado = $id_chamado ORDER BY id_anexo ASC");
        $fotos = [];
        if ($anexos) {
            while ($row = $anexos->fetch_assoc()) {
                $fotos[] = $row['caminho_arquivo'];
            }
        }
        $chamado['fotos'] = $fotos;
        $chamado['foto'] = !empty($fotos) ? $fotos[0] : null; // Keep for fallback/thumbnail
        $chamado['thumbnail'] = $chamado['foto'];

        echo json_encode(["success" => true, "data" => $chamado]);
        exit;
    }

    $where = "1=1";
    if ($perfil === 'solicitante') {
        $where = "c.id_solicitante = $user_id";
    } elseif ($perfil === 'tecnico') {
        $where = "c.id_tecnico = $user_id";
    } elseif ($perfil !== 'gestor') {
        echo json_encode(["success" => false, "message" => "Acesso negado.", "data" => []]);
        exit;
    }

    $sql = "SELECT c.id_chamado, c.descricao_problema, c.status, c.data_abertura, c.prioridade,
                   a.nome AS ambiente_nome, b.nome AS bloco_nome,
                   (SELECT caminho_arquivo FROM chamados_anexos WHERE id_chamado = c.id_chamado ORDER BY id_anexo ASC LIMIT 1) AS thumbnail
            FROM chamados c
            LEFT JOIN ambientes a ON c.id_ambiente = a.id_ambiente
            LEFT JOIN blocos b ON a.id_bloco = b.id_bloco
            WHERE $where
            ORDER BY c.data_abertura DESC";

    $result = $conn->query($sql);
    if (!$result) {
        echo json_encode(["success" => false, "message" => "Erro ao buscar chamados: " . $conn->error, "data" => []]);
        exit;
    }

    echo json_encode(["success" => true, "data" => $result->fetch_all(MYSQLI_ASSOC)]);
    exit;
}

if ($method === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id_chamado = (int)($data['id_chamado'] ?? 0);

    if (!$id_chamado) {
        echo json_encode(["success" => false, "message" => "ID do chamado não informado."]);
        exit;
    }

    $chamado = buscarChamado($conn, $id_chamado);
    $somenteAberto = ($perfil === 'solicitante');
    $perm = podeGerenciarChamado($chamado, $user_id, $perfil, $somenteAberto);

    if (!$perm['ok']) {
        echo json_encode(["success" => false, "message" => $perm['message']]);
        exit;
    }

    $anexos = $conn->query("SELECT caminho_arquivo FROM chamados_anexos WHERE id_chamado = $id_chamado");
    if ($anexos) {
        while ($a = $anexos->fetch_assoc()) {
            $path = '../' . $a['caminho_arquivo'];
            if (file_exists($path)) {
                @unlink($path);
            }
        }
    }

    if ($conn->query("DELETE FROM chamados WHERE id_chamado = $id_chamado")) {
        echo json_encode(["success" => true, "message" => "Chamado #$id_chamado excluído com sucesso."]);
    } else {
        echo json_encode(["success" => false, "message" => "Erro ao excluir: " . $conn->error]);
    }
    exit;
}

echo json_encode(["success" => false, "message" => "Método não suportado."]);
