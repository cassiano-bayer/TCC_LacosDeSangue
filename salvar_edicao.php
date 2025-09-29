<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

include_once('config.php');

$usuario_id = $_SESSION['usuario_id'];

$sql = "UPDATE usuarios SET nome_completo = ?, email = ?, telefone = ?, cpf = ?, rg = ?, sexo = ?, data_nascimento = ?, peso = ?, tipo_sanguineo = ?, ja_doou = ?, tempo_ultima_doacao = ?, possui_doenca = ?, doencas = ?, cidade = ?, estado = ?, endereco = ? WHERE id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param(
    "ssssssssssssssssi",
    $_POST['nome_completo'],
    $_POST['email'],
    $_POST['telefone'],
    $_POST['cpf'],
    $_POST['rg'],
    $_POST['sexo'],
    $_POST['data_nascimento'],
    $_POST['peso'],
    $_POST['tipo_sanguineo'],
    $_POST['ja_doou'],
    $_POST['tempo_ultima_doacao'],
    $_POST['possui_doenca'],
    $_POST['doencas'],
    $_POST['cidade'],
    $_POST['estado'],
    $_POST['endereco'],
    $usuario_id
);

if ($stmt->execute()) {
    header("Location: perfil.php");
    exit();
} else {
    echo "Erro ao atualizar perfil: " . $stmt->error;
}
?>
