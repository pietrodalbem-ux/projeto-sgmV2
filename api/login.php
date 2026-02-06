<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

$dadosBrutos = file_get_contents("php://input");
$data = json_decode($dadosBrutos);

if (!$data || !isset($data->email) || !isset($data->senha)) {
    echo json_encode(["success" => false, "message" => "Dados inválidos."]);
    exit;
}

$email = $conn->real_escape_string(trim($data->email));
$senha = trim($data->senha);

$sql = "SELECT id_usuario, nome, senha_hash, perfil, ativo 
        FROM usuarios 
        WHERE email = '$email' 
        LIMIT 1";

$result = $conn->query($sql);

if ($result && $result->num_rows === 1) {

    $user = $result->fetch_assoc();

    if ((int)$user['ativo'] !== 1) {
        echo json_encode(["success" => false, "message" => "Usuário inativo."]);
        exit;
    }

    $hashDoBanco = trim($user['senha_hash']);

    if (password_verify($senha, $hashDoBanco)) {
        $_SESSION['user_id'] = $user['id_usuario'];
        $_SESSION['user_nome'] = $user['nome'];
        $_SESSION['user_perfil'] = $user['perfil'];

        echo json_encode(["success" => true, "perfil" => $user['perfil']]);
        exit;
    } else {
        echo json_encode(["success" => false, "message" => "Senha incorreta."]);
        exit;
    }

} else {
    echo json_encode(["success" => false, "message" => "Usuário não encontrado."]);
    exit;
}