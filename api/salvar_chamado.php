<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

// 1. Verificação de Sessão Segura
if(!isset($_SESSION['user_id'])){
    echo json_encode(["success" => false, "message" => "Sessão expirada"]);
    exit();
}

$id_solicitante = $_SESSION["user_id"];
$id_ambiente = (int)($_POST['id_ambiente'] ?? 0);
$id_tipo = (int)($_POST['id_tipo'] ?? 0);
$descricao = $conn->real_escape_string($_POST['descricao'] ?? '');

// 2. Validação de campos
if(!$id_ambiente || !$id_tipo || empty($descricao)){
    echo json_encode(['success' => false, 'message' => 'Preencha todos os campos obrigatórios']);
    exit();
}

// 3. SQL Corrigido (Certifique-se que os nomes das colunas estão iguais ao seu banco)
$sql = "INSERT INTO chamados (descricao_problema, id_solicitante, id_ambiente, id_tipo_servico, status) 
        VALUES ('$descricao', $id_solicitante, $id_ambiente, $id_tipo, 'aberto')";

if($conn->query($sql)){
    $id_chamado = $conn->insert_id;
    
    // Processamento da Foto
    if(isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK){
        $diretorio = "../assets/uploads/";
        if(!is_dir($diretorio)) mkdir($diretorio, 0777, true);
        
        $extensao = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $nome_arquivo = "abertura_" . uniqid() . "." . $extensao;
        $caminho_final = $diretorio . $nome_arquivo;
        
        if(move_uploaded_file($_FILES['foto']['tmp_name'], $caminho_final)){
            $caminho_db = "assets/uploads/" . $nome_arquivo;
            $conn->query("INSERT INTO chamados_anexos (id_chamado, caminho_arquivo, tipo_anexo) VALUES ($id_chamado, '$caminho_db', 'abertura')");
        }
    }
    
    echo json_encode(["success" => true, "message" => "Chamado #$id_chamado aberto com sucesso!"]);
} else {
    // Retorno de erro do Banco de Dados sem erros de aspas no JSON
    echo json_encode(["success" => false, "message" => "Erro no banco: " . $conn->error]);
}
?>