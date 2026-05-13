<?php

session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

// Verifica se o usuário está logado e se é um gestor
if(!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor'){
    echo json_encode(["success" => false, "message" => "Acesso negado."]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method){
    case 'GET':
        // Lista todos os blocos
        $sql = "SELECT id_bloco, nome, descricao FROM blocos ORDER BY nome ASC";
        $result = $conn->query($sql);
        $blocos = [];

        if($result){
            while ($row = $result->fetch_assoc()) {
                $blocos[] = $row;
            }
        }
        echo json_encode(["success" => true, "data" => $blocos]);
        break;

    case 'POST':
        // Cria um novo bloco
        $data = json_decode(file_get_contents("php://input"));
        
        if(!isset($data->nome) || empty(trim($data->nome))){
            echo json_encode(["success" => false, "message" => "O nome do bloco é obrigatório."]);
            exit;
        }
        
        $nome = $conn->real_escape_string(trim($data->nome));
        // A descrição é opcional no seu banco, então tratamos caso venha vazia
        $descricao = isset($data->descricao) && !empty(trim($data->descricao)) ? "'" . $conn->real_escape_string(trim($data->descricao)) . "'" : "NULL";
        
        $sql = "INSERT INTO blocos (nome, descricao) VALUES ('$nome', $descricao)";
        
        if($conn->query($sql) === TRUE){
            echo json_encode(["success" => true, "message" => "Bloco criado com sucesso!", "id_bloco" => $conn->insert_id]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro ao criar bloco: " . $conn->error]);
        }
        break;

    case 'PUT':
        // Atualiza um bloco existente
        $data = json_decode(file_get_contents("php://input"));
        
        if(!isset($data->id_bloco) || !isset($data->nome) || empty(trim($data->nome))){
            echo json_encode(["success" => false, "message" => "Dados incompletos. Informe o ID e o nome do bloco."]);
            exit;
        }
        
        $id_bloco = (int)$data->id_bloco;
        $nome = $conn->real_escape_string(trim($data->nome));
        $descricao = isset($data->descricao) && !empty(trim($data->descricao)) ? "'" . $conn->real_escape_string(trim($data->descricao)) . "'" : "NULL";
        
        $sql = "UPDATE blocos SET nome = '$nome', descricao = $descricao WHERE id_bloco = $id_bloco";
        
        if($conn->query($sql) === TRUE){
            echo json_encode(["success" => true, "message" => "Bloco atualizado com sucesso!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro ao atualizar bloco: " . $conn->error]);
        }
        break;

    case 'DELETE':
        // Deleta um bloco
        $data = json_decode(file_get_contents("php://input"));
        
        if(!isset($data->id_bloco)){
            echo json_encode(["success" => false, "message" => "Informe o ID do bloco para deletar."]);
            exit;
        }
        
        $id_bloco = (int)$data->id_bloco;
        $sql = "DELETE FROM blocos WHERE id_bloco = $id_bloco";
        
        if($conn->query($sql) === TRUE){
            echo json_encode(["success" => true, "message" => "Bloco deletado com sucesso!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro ao deletar bloco: " . $conn->error]);
        }
        break;

    default:
        echo json_encode(["success" => false, "message" => "Método HTTP não suportado."]);
        break;
}