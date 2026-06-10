<?php

session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if(!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor'){
    echo json_encode(["success" => false, "message" => "Acesso negado."]);
    exit;
}
$method = $_SERVER['REQUEST_METHOD'];

switch ($method){
    case 'GET':
        $sql = "SELECT a.id_ambiente, a.nome, a.id_bloco, b.nome as nome_bloco from ambientes a left join blocos b on a.id_bloco = b.id_bloco order by a.nome asc";

        $result = $conn->query($sql);
        $ambientes = [];

        if($result){
            while ($row = $result->fetch_assoc())
                $ambientes[] = $row;
        }
        echo json_encode(["success" => true, "data" => $ambientes]);
        break;
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if(!isset($data->nome) || !isset($data->id_bloco)){
            echo json_encode(["success" => false, "message" => "Dados incompletos. Informe nome e id_bloco"]);
            exit;
        }
        $nome = $conn->real_escape_string(trim($data->nome));
        $id_bloco = (int)$data->id_bloco;
        $sql = "INSERT into ambientes (nome,id_bloco) values ('$nome', $id_bloco)";
        if($conn->query($sql) === TRUE){
            echo json_encode(["success" => true, "message" => "Ambiente criado com sucesso!", "id_ambiente" => $conn->insert_id]);
        } else{
            echo json_encode(["success" => false, "message" => "Erro ao criar ambientes: " . $conn->error]);
        }
        break;
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        if(!isset($data->id_ambiente) || !isset($data->nome) || !isset($data->id_bloco)){
            echo json_encode(["success" => false, "message" => "Dados incompletos para atualização."]);
            exit;
        }
        $id_ambiente = (int)$data->id_ambiente;
        $nome = $conn->real_escape_string(trim($data->nome));
        $id_bloco = (int)$data->id_bloco;
        $sql = "UPDATE ambientes set nome = '$nome', id_bloco = $id_bloco where id_ambiente = $id_ambiente";
        if($conn->query($sql)=== TRUE){
            echo json_encode(["success" => true, "message" => "Ambiente atualizado com sucesso!", "id_ambiente" => $conn->insert_id]);
        } else{
            echo json_encode(["success" => false, "message" => "Erro ao atualizar ambientes: " . $conn->error]);
        }
        break;
    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        if (!isset($data->id_ambiente)) {
            echo json_encode(["success" => false, "message" => "Dados incompletos para deletar."]);
            exit;
        }

        $id_ambiente = (int)$data->id_ambiente;
        $vinculados = $conn->query("SELECT id_chamado, descricao_problema, status, prioridade FROM chamados WHERE id_ambiente = $id_ambiente ORDER BY id_chamado DESC");

        if ($vinculados && $vinculados->num_rows > 0) {
            echo json_encode([
                "success" => false,
                "message" => "Este ambiente não pode ser excluído pois possui chamados vinculados.",
                "chamados_vinculados" => $vinculados->fetch_all(MYSQLI_ASSOC)
            ]);
            exit;
        }

        if ($conn->query("DELETE FROM ambientes WHERE id_ambiente = $id_ambiente")) {
            echo json_encode(["success" => true, "message" => "Ambiente excluído com sucesso!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro ao deletar ambiente: " . $conn->error]);
        }
        break;
    default:
        echo json_encode(["success" => false, "message" => "Metodo HTTP não suportado"]);
        break;
}