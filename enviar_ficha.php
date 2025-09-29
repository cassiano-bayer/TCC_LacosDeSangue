<?php
session_start();
include_once("config.php");

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$hospital_solicitado = $_POST['hospital'] ?? '';

$sql_check = "SELECT ficha_enviada FROM usuarios WHERE id = ?";
$stmt_check = mysqli_prepare($conexao, $sql_check);
mysqli_stmt_bind_param($stmt_check, "i", $usuario_id);
mysqli_stmt_execute($stmt_check);
mysqli_stmt_bind_result($stmt_check, $ficha_enviada);
mysqli_stmt_fetch($stmt_check);
mysqli_stmt_close($stmt_check);

if ($ficha_enviada) {
    echo "Você já enviou uma ficha médica.";
    exit();
}

if ($hospital_solicitado != '') {
    $sql = "UPDATE usuarios SET hospital_solicitado = ?, ficha_enviada = 1 WHERE id = ?";
    $stmt = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($stmt, "si", $hospital_solicitado, $usuario_id);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        echo "Solicitação enviada para o hospital $hospital_solicitado.";
    } else {
        echo "Erro ao registrar a ficha.";
    }
} else {
    echo "Nenhum hospital selecionado.";
}
?>
