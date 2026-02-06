<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: ./api/login.php");
    exit;
}

$perfil = $_SESSION['user_perfil'];

switch($perfil){
    case 'gestor':
        header("Location: gestor_dashboard.php");
        break;

    case 'tecnico':
         header("Location: tecnico_minhas_tarefas.php");
        break;

    case 'solicitante':
        header("Location: solicitante_dashboard.php");
        break;
    
    default:
        session_destroy();
        header("Location: login.php?error=perfil_inval");
        break;
}
exit;