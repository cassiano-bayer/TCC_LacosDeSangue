<?php
session_start();

if (!empty($_POST['email']) && !empty($_POST['senha'])) {
    include_once('config.php');

    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $sql = "SELECT * FROM usuarios WHERE email = ? AND senha = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ss", $email, $senha);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome_completo'];
        $_SESSION['usuario_tipo_sanguineo'] = $usuario['tipo_sanguineo'];
        $_SESSION['email'] = $usuario['email']; // ✅ Aqui está a correção

        header("Location: area_usuario.php");
        exit();
    } else {
        header("Location: login.php?erro=1");
        exit();
    }
} else {
    header("Location: login.php?erro=2");
    exit();
}
