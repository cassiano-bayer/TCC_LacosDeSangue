<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

include_once('config.php');

$usuario_id = $_SESSION['usuario_id'];

$sql = "DELETE FROM usuarios WHERE id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $usuario_id);

if ($stmt->execute()) {
    session_destroy();
    
    header("Location: login.php?msg=conta_deletada");
    exit();
} else {
    header("Location: perfil.php?erro=nao_foi_possivel_deletar");
    exit();
}
?>