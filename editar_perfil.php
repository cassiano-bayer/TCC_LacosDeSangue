<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

include_once('config.php');

$usuario_id = $_SESSION['usuario_id'];

$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "Usuário não encontrado.";
    exit();
}

$usuario = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Perfil</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            background: linear-gradient(135deg, #ff4757 0%, #ff3838 50%, #ff6b7a 100%);
            opacity: 0;
            animation: fadeIn 0.8s ease-in forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .formulario {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            max-width: 900px;
            margin: 20px auto;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .editarperf {
            font-size: 32px;
            margin-bottom: 30px;
            color: #CD5C5C;
            font-weight: bold;
            text-align: center;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
            margin-top: 0;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-item {
            display: flex;
            flex-direction: column;
        }

        .form-item.full-width {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }

        input, select {
            width: 100%;
            padding: 12px 15px;
            margin: 0;
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            font-size: 15px;
            background-color: #fff;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #CD5C5C;
            box-shadow: 0 0 0 3px rgba(205, 92, 92, 0.1);
            transform: translateY(-2px);
        }

        input:hover, select:hover {
            border-color: #CD5C5C;
        }

        .botao-salvar {
            background-color: #CD5C5C;
            border: none;
            padding: 15px 40px;
            border-radius: 50px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            display: block;
            margin: 30px auto 0;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(205, 92, 92, 0.3);
        }

        .botao-salvar::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s ease;
        }

        .botao-salvar:hover::before {
            left: 100%;
        }

        .botao-salvar:hover {
            background-color: #d62828;
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 15px 35px rgba(205, 92, 92, 0.4);
        }

        .botao-salvar:active {
            transform: translateY(-1px) scale(1.02);
        }

        .voltar {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: #CD5C5C;
            color: white;
            border-radius: 50px;
            padding: 10px 20px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            font-weight: 600;
            box-shadow: 0 5px 15px rgba(205, 92, 92, 0.3);
        }

        .voltar:hover {
            background-color: #d62828;
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 10px 25px rgba(205, 92, 92, 0.4);
        }

        .voltar:active {
            transform: translateY(-1px) scale(1.02);
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .formulario {
                margin: 10px;
                padding: 20px;
            }
            
            .editarperf {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <a href="perfil.php" class="voltar">← Voltar</a>
    
    <div class="formulario">
        <h2 class="editarperf">Editar Perfil</h2>
        <form action="salvar_edicao.php" method="POST">
            <div class="form-grid">
                <div class="form-item">
                    <label>Nome Completo:</label>
                    <input type="text" name="nome_completo" value="<?= htmlspecialchars($usuario['nome_completo']) ?>" required>
                </div>

                <div class="form-item">
                    <label>Email:</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>
                </div>

                <div class="form-item">
                    <label>Telefone:</label>
                    <input type="text" name="telefone" value="<?= htmlspecialchars($usuario['telefone']) ?>" required>
                </div>

                <div class="form-item">
                    <label>CPF:</label>
                    <input type="text" name="cpf" value="<?= htmlspecialchars($usuario['cpf']) ?>" required>
                </div>

                <div class="form-item">
                    <label>RG:</label>
                    <input type="text" name="rg" value="<?= htmlspecialchars($usuario['rg']) ?>" required>
                </div>

                <div class="form-item">
                    <label>Sexo:</label>
                    <select name="sexo" required>
                        <option value="Masculino" <?= $usuario['sexo'] == 'Masculino' ? 'selected' : '' ?>>Masculino</option>
                        <option value="Feminino" <?= $usuario['sexo'] == 'Feminino' ? 'selected' : '' ?>>Feminino</option>
                        <option value="Outro" <?= $usuario['sexo'] == 'Outro' ? 'selected' : '' ?>>Outro</option>
                    </select>
                </div>

                <div class="form-item">
                    <label>Data de Nascimento:</label>
                    <input type="date" name="data_nascimento" value="<?= $usuario['data_nascimento'] ?>" required>
                </div>

                <div class="form-item">
                    <label>Peso (kg):</label>
                    <input type="number" name="peso" value="<?= $usuario['peso'] ?>" required>
                </div>

                <div class="form-item">
                    <label>Tipo Sanguíneo:</label>
                    <select name="tipo_sanguineo" required>
                    <option value="N/D" <?= $usuario['tipo_sanguineo'] == 'N/D' ? 'selected' : '' ?>>N/D</option>
                    <option value="A+" <?= $usuario['tipo_sanguineo'] == 'A+' ? 'selected' : '' ?>>A+</option>
                    <option value="A-" <?= $usuario['tipo_sanguineo'] == 'A-' ? 'selected' : '' ?>>A-</option>
                    <option value="B+" <?= $usuario['tipo_sanguineo'] == 'B+' ? 'selected' : '' ?>>B+</option>
                    <option value="B-" <?= $usuario['tipo_sanguineo'] == 'B-' ? 'selected' : '' ?>>B-</option>
                    <option value="O+" <?= $usuario['tipo_sanguineo'] == 'O+' ? 'selected' : '' ?>>O+</option>
                    <option value="O-" <?= $usuario['tipo_sanguineo'] == 'O-' ? 'selected' : '' ?>>O-</option>
                    <option value="AB+" <?= $usuario['tipo_sanguineo'] == 'AB+' ? 'selected' : '' ?>>AB+</option>
                    <option value="AB-" <?= $usuario['tipo_sanguineo'] == 'AB-' ? 'selected' : '' ?>>AB-</option>
                    </select>
                </div>

                <div class="form-item">
                    <label>Já doou sangue antes?</label>
                    <select name="ja_doou" required>
                        <option value="Sim" <?= $usuario['ja_doou'] == 'Sim' ? 'selected' : '' ?>>Sim</option>
                        <option value="Não" <?= $usuario['ja_doou'] == 'Não' ? 'selected' : '' ?>>Não</option>
                    </select>
                </div>

                <div class="form-item">
                    <label>Doação anterior:</label>
                    <select name="tempo_ultima_doacao" required>
                    <option value="Menos de 30 dias" <?= $usuario['tempo_ultima_doacao'] == 'Menos de 30 dias' ? 'selected' : '' ?>>Menos de 30 dias</option>
                    <option value="Menos de 60 dias" <?= $usuario['tempo_ultima_doacao'] == 'Menos de 60 dias' ? 'selected' : '' ?>>Menos de 60 dias</option>
                    <option value="60 dias ou mais" <?= $usuario['tempo_ultima_doacao'] == '60 dias ou mais' ? 'selected' : '' ?>>60 dias ou mais</option>
                    <option value="90 dias ou mais" <?= $usuario['tempo_ultima_doacao'] == '90 dias ou mais' ? 'selected' : '' ?>>90 dias ou mais</option>
                    </select>
                </div>

                <div class="form-item">
                    <label>Possui alguma doença?</label>
                    <select name="possui_doenca" required>
                        <option value="Sim" <?= $usuario['possui_doenca'] == 'Sim' ? 'selected' : '' ?>>Sim</option>
                        <option value="Não" <?= $usuario['possui_doenca'] == 'Não' ? 'selected' : '' ?>>Não</option>
                    </select>
                </div>

                <div class="form-item full-width">
                    <label>Doenças informadas:</label>
                    <input type="text" name="doencas" value="<?= htmlspecialchars($usuario['doencas']) ?>">
                </div>

                <div class="form-item">
                    <label>Cidade:</label>
                    <input type="text" name="cidade" value="<?= htmlspecialchars($usuario['cidade']) ?>" required>
                </div>

                <div class="form-item">
                    <label>Estado:</label>
                    <input type="text" name="estado" value="<?= htmlspecialchars($usuario['estado']) ?>" required>
                </div>

                <div class="form-item full-width">
                    <label>Endereço:</label>
                    <input type="text" name="endereco" value="<?= htmlspecialchars($usuario['endereco']) ?>" required>
                </div>
            </div>
            
            <button type="submit" class="botao-salvar">Salvar Alterações</button>
        </form>
    </div>
</body>
</html>