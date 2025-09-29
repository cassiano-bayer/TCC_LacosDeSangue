<?php
session_start();
include_once('config.php');

if(!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

if(!empty($_GET['id'])) {
    $id = $_GET['id'];
    
    $sqlUpdate = "UPDATE usuarios SET status_formulario = 'recusado' WHERE id = ?";
    $stmt = $conexao->prepare($sqlUpdate);
    $stmt->bind_param("i", $id);
    
    if($stmt->execute()) {
        $_SESSION['mensagem'] = "Formulário recusado com sucesso!";
        $_SESSION['tipo_mensagem'] = "success";
    } else {
        $_SESSION['mensagem'] = "Erro ao recusar formulário!";
        $_SESSION['tipo_mensagem'] = "error";
    }
    
    $stmt->close();
} else {
    $_SESSION['mensagem'] = "ID não fornecido!";
    $_SESSION['tipo_mensagem'] = "error";
}

header('Location: sistema.php');
exit();
?>