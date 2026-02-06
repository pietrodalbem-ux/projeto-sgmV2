<?php
// config/database.php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "sgm_db";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Configura charset para evitar erros de acentuação
$conn->set_charset("utf8mb4");