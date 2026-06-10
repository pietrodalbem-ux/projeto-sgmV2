<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    echo json_encode(["success" => false, "message" => "Acesso negado."]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

function chamadosVinculadosBloco($conn, $id_bloco) {
    $id = (int)$id_bloco;
    $sql = "SELECT c.id_chamado, c.descricao_problema, c.status, c.prioridade,
                   a.nome AS ambiente_nome, u.nome AS solicitante_nome
            FROM chamados c
            JOIN ambientes a ON c.id_ambiente = a.id_ambiente
            JOIN usuarios u ON c.id_solicitante = u.id_usuario
            WHERE a.id_bloco = $id
            ORDER BY c.id_chamado DESC";
    $result = $conn->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

switch ($method) {
    case 'GET':
        if (isset($_GET['chamados_vinculados'])) {
            $id_bloco = (int)$_GET['chamados_vinculados'];
            echo json_encode([
                "success" => true,
                "chamados" => chamadosVinculadosBloco($conn, $id_bloco)
            ]);
            exit;
        }

        $sql = "SELECT id_bloco, nome, descricao FROM blocos ORDER BY nome ASC";
        $result = $conn->query($sql);
        $blocos = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $blocos[] = $row;
            }
        }
        echo json_encode(["success" => true, "data" => $blocos]);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->nome) || empty(trim($data->nome))) {
            echo json_encode(["success" => false, "message" => "O nome do bloco é obrigatório."]);
            exit;
        }

        $nome = $conn->real_escape_string(trim($data->nome));
        $descricao = isset($data->descricao) && !empty(trim($data->descricao))
            ? "'" . $conn->real_escape_string(trim($data->descricao)) . "'"
            : "NULL";

        if ($conn->query("INSERT INTO blocos (nome, descricao) VALUES ('$nome', $descricao)")) {
            echo json_encode(["success" => true, "message" => "Bloco criado com sucesso!", "id_bloco" => $conn->insert_id]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro ao criar bloco: " . $conn->error]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->id_bloco) || !isset($data->nome) || empty(trim($data->nome))) {
            echo json_encode(["success" => false, "message" => "Dados incompletos. Informe o ID e o nome do bloco."]);
            exit;
        }

        $id_bloco = (int)$data->id_bloco;
        $nome = $conn->real_escape_string(trim($data->nome));
        $descricao = isset($data->descricao) && !empty(trim($data->descricao))
            ? "'" . $conn->real_escape_string(trim($data->descricao)) . "'"
            : "NULL";

        if ($conn->query("UPDATE blocos SET nome = '$nome', descricao = $descricao WHERE id_bloco = $id_bloco")) {
            echo json_encode(["success" => true, "message" => "Bloco atualizado com sucesso!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro ao atualizar bloco: " . $conn->error]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->id_bloco)) {
            echo json_encode(["success" => false, "message" => "Informe o ID do bloco para deletar."]);
            exit;
        }

        $id_bloco = (int)$data->id_bloco;
        $vinculados = chamadosVinculadosBloco($conn, $id_bloco);

        if (count($vinculados) > 0) {
            echo json_encode([
                "success" => false,
                "message" => "Este bloco não pode ser excluído pois possui chamados vinculados aos seus ambientes.",
                "chamados_vinculados" => $vinculados
            ]);
            exit;
        }

        if ($conn->query("DELETE FROM blocos WHERE id_bloco = $id_bloco")) {
            echo json_encode(["success" => true, "message" => "Bloco excluído com sucesso!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro ao deletar bloco: " . $conn->error]);
        }
        break;

    default:
        echo json_encode(["success" => false, "message" => "Método HTTP não suportado."]);
        break;
}
