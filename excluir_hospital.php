<?php
session_start();
include_once('config.php');

// Verificar se é admin
if(!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

// Verificar se o ID foi passado
if(!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: lista_hospital.php?erro=id_nao_informado');
    exit();
}

$id = intval($_GET['id']); // Converter para inteiro para segurança

// Verificar se o hospital existe antes de excluir
$sql_verificar = "SELECT nome FROM hospitais WHERE id = ?";
$stmt_verificar = $conexao->prepare($sql_verificar);

if(!$stmt_verificar) {
    die("Erro na preparação da consulta de verificação: " . $conexao->error);
}

$stmt_verificar->bind_param("i", $id);
$stmt_verificar->execute();
$resultado = $stmt_verificar->get_result();

if($resultado->num_rows == 0) {
    // Hospital não encontrado
    header('Location: lista_hospital.php?erro=hospital_nao_encontrado');
    exit();
}

$hospital = $resultado->fetch_assoc();
$nome_hospital = $hospital['nome'];

// Preparar e executar a query de exclusão
$sql_excluir = "DELETE FROM hospitais WHERE id = ?";
$stmt_excluir = $conexao->prepare($sql_excluir);

if(!$stmt_excluir) {
    die("Erro na preparação da consulta de exclusão: " . $conexao->error);
}

$stmt_excluir->bind_param("i", $id);

if($stmt_excluir->execute()) {
    // Exclusão bem-sucedida
    $stmt_verificar->close();
    $stmt_excluir->close();
    $conexao->close();
    
    // Redirecionar com mensagem de sucesso
    header('Location: lista_hospital.php?sucesso=hospital_excluido&nome=' . urlencode($nome_hospital));
    exit();
} else {
    // Erro na exclusão
    $stmt_verificar->close();
    $stmt_excluir->close();
    $conexao->close();
    
    header('Location: lista_hospital.php?erro=erro_exclusao');
    exit();
}
?>