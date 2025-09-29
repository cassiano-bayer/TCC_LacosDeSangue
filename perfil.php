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

if ($result->num_rows === 1) {
    $usuario = $result->fetch_assoc();
} else {
    echo "Usuário não encontrado.";
    exit();
}

function getStatusTexto($status) {
    switch($status) {
        case 'em_analise':
            return 'Em análise';
        case 'recusado':
            return 'Formulário recusado';
        case 'aprovado':
            return 'Formulário válido para doação, vá ao hospital selecionado para realizar o restante dos testes';
        default:
            return 'Status não definido';
    }
}

function getStatusCor($status) {
    switch($status) {
        case 'em_analise':
            return '#FFA500'; 
        case 'recusado':
            return '#DC3545'; 
        case 'aprovado':
            return '#28A745'; 
        default:
            return '#6C757D'; 
    }
}

$fichaMedicaEnviada = !empty($usuario['hospital_solicitado']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil do Usuário - Laços de Sangue</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #ff4757 0%, #ff3838 50%, #ff6b7a 100%);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* Partículas de fundo */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 12s infinite ease-in-out;
        }

        .particle:nth-child(1) { width: 8px; height: 8px; left: 10%; animation-delay: 0s; }
        .particle:nth-child(2) { width: 12px; height: 12px; left: 20%; animation-delay: 2s; }
        .particle:nth-child(3) { width: 6px; height: 6px; left: 30%; animation-delay: 4s; }
        .particle:nth-child(4) { width: 10px; height: 10px; left: 40%; animation-delay: 1s; }
        .particle:nth-child(5) { width: 14px; height: 14px; left: 60%; animation-delay: 3s; }
        .particle:nth-child(6) { width: 7px; height: 7px; left: 80%; animation-delay: 5s; }

        @keyframes float {
            0%, 100% { 
                transform: translateY(100vh) rotate(0deg); 
                opacity: 0; 
            }
            10% { opacity: 1; }
            90% { opacity: 0.8; }
            50% { 
                transform: translateY(-50px) rotate(180deg); 
            }
        }

        .container {
            padding: 40px 20px;
            max-width: 1000px;
            margin: 0 auto;
            position: relative;
        }

        .perfil {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            padding: 40px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            animation: slideInUp 0.8s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #f1f3f4;
            padding-bottom: 20px;
        }

        .header h2 {
            font-size: 2.2rem;
            color: #d63031;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-icon {
            background: linear-gradient(135deg, #ff4757, #ff3838);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .btn-editar {
            background: linear-gradient(135deg, #74b9ff, #0984e3);
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 8px 25px rgba(116, 185, 255, 0.3);
        }

        .btn-editar:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(116, 185, 255, 0.4);
        }

        .status-container {
            background: linear-gradient(135deg, #f8f9fa, #ffffff);
            border-radius: 20px;
            padding: 25px;
            margin: 30px 0;
            border-left: 6px solid <?= $fichaMedicaEnviada ? getStatusCor($usuario['status_formulario']) : '#6C757D' ?>;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            position: relative;
            overflow: hidden;
        }

        .status-container::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(to bottom, <?= $fichaMedicaEnviada ? getStatusCor($usuario['status_formulario']) : '#6C757D' ?>, transparent);
        }

        .status-titulo {
            font-size: 1.3rem;
            font-weight: 700;
            color: #2d3436;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .status-icone {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background-color: <?= $fichaMedicaEnviada ? getStatusCor($usuario['status_formulario']) : '#6C757D' ?>;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .status-icone::after {
            content: '✓';
            color: white;
            font-weight: bold;
            font-size: 14px;
        }

        .status-texto {
            color: <?= $fichaMedicaEnviada ? getStatusCor($usuario['status_formulario']) : '#6C757D' ?>;
            font-weight: 600;
            font-size: 1.1rem;
            line-height: 1.5;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin: 30px 0;
        }

        .info-card {
            background: linear-gradient(135deg, #ffffff, #f8f9fa);
            padding: 20px;
            border-radius: 15px;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
            position: relative;
        }

        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            border-color: #ff4757;
        }

        .info-label {
            font-weight: 700;
            color: #d63031;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            display: block;
        }

        .info-value {
            color: #2d3436;
            font-size: 1.1rem;
            font-weight: 500;
            line-height: 1.4;
        }

        .info-highlight {
            background: linear-gradient(135deg, #ff4757, #ff3838);
            color: white !important;
            border-radius: 12px;
            transform: scale(1.02);
        }

        .info-highlight .info-label {
            color: rgba(255, 255, 255, 0.9);
        }

        .info-highlight .info-value {
            color: white;
            font-weight: 600;
        }

        .botao-voltar {
            background: linear-gradient(135deg, #ff4757, #ff3838);
            color: white;
            padding: 15px 35px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-top: 30px;
            margin-right: 15px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(255, 71, 87, 0.3);
        }

        .botao-voltar:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(255, 71, 87, 0.4);
        }

        .botao-apagar {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            padding: 15px 35px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-top: 30px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(231, 76, 60, 0.3);
        }

        .botao-apagar:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(231, 76, 60, 0.4);
            background: linear-gradient(135deg, #c0392b, #a93226);
        }

        .botoes-container {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
        }

        .section-divider {
            height: 2px;
            background: linear-gradient(90deg, transparent, #ff4757, transparent);
            margin: 40px 0;
            border-radius: 2px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px 15px;
            }
            
            .perfil {
                padding: 25px 20px;
            }
            
            .header {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .header h2 {
                font-size: 1.8rem;
            }

            .botoes-container {
                flex-direction: column;
                width: 100%;
            }

            .botao-voltar,
            .botao-apagar {
                width: 100%;
                justify-content: center;
                margin-right: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Partículas de fundo -->
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div class="container">
        <div class="perfil">
            <div class="header">
                <h2>
                    Perfil do Usuário
                </h2>
                <a href="editar_perfil.php" class="btn-editar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001m-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708z"/>
                    </svg>
                    Editar Informações
                </a>
            </div>

            <div class="status-container">
                <div class="status-titulo">
                    <span class="status-icone"></span>
                    Status do Formulário
                </div>
                <div class="status-texto">
                    <?php if ($fichaMedicaEnviada): ?>
                        <?= getStatusTexto($usuario['status_formulario']) ?>
                    <?php else: ?>
                        Nenhuma ficha médica enviada
                    <?php endif; ?>
                </div>
            </div>

            <div class="section-divider"></div>

            <div class="info-grid">
                <div class="info-card">
                    <span class="info-label">Nome Completo</span>
                    <div class="info-value"><?= $usuario['nome_completo'] ?></div>
                </div>

                <div class="info-card">
                    <span class="info-label">Email</span>
                    <div class="info-value"><?= $usuario['email'] ?></div>
                </div>

                <div class="info-card">
                    <span class="info-label">Telefone</span>
                    <div class="info-value"><?= $usuario['telefone'] ?></div>
                </div>

                <div class="info-card">
                    <span class="info-label">CPF</span>
                    <div class="info-value"><?= $usuario['cpf'] ?></div>
                </div>

                <div class="info-card">
                    <span class="info-label">RG</span>
                    <div class="info-value"><?= $usuario['rg'] ?></div>
                </div>

                <div class="info-card">
                    <span class="info-label">Sexo</span>
                    <div class="info-value"><?= $usuario['sexo'] ?></div>
                </div>

                <div class="info-card">
                    <span class="info-label">Data de Nascimento</span>
                    <div class="info-value"><?= $usuario['data_nascimento'] ?></div>
                </div>

                <div class="info-card">
                    <span class="info-label">Peso</span>
                    <div class="info-value"><?= $usuario['peso'] ?> kg</div>
                </div>

                <div class="info-card info-highlight">
                    <span class="info-label">Tipo Sanguíneo</span>
                    <div class="info-value"><?= $usuario['tipo_sanguineo'] ?></div>
                </div>

                <div class="info-card">
                    <span class="info-label">Já doou sangue</span>
                    <div class="info-value"><?= $usuario['ja_doou'] ?></div>
                </div>

                <div class="info-card">
                    <span class="info-label">Última doação</span>
                    <div class="info-value"><?= $usuario['tempo_ultima_doacao'] ?></div>
                </div>

                <div class="info-card">
                    <span class="info-label">Possui alguma doença</span>
                    <div class="info-value"><?= $usuario['possui_doenca'] ?></div>
                </div>

                <div class="info-card">
                    <span class="info-label">Doenças informadas</span>
                    <div class="info-value"><?= $usuario['doencas'] ?: 'Nenhuma' ?></div>
                </div>

                <div class="info-card">
                    <span class="info-label">Cidade</span>
                    <div class="info-value"><?= $usuario['cidade'] ?></div>
                </div>

                <div class="info-card">
                    <span class="info-label">Estado</span>
                    <div class="info-value"><?= $usuario['estado'] ?></div>
                </div>

                <div class="info-card">
                    <span class="info-label">Endereço</span>
                    <div class="info-value"><?= $usuario['endereco'] ?></div>
                </div>

                <?php if(!empty($usuario['hospital_solicitado'])): ?>
                <div class="info-card info-highlight">
                    <span class="info-label">Hospital Solicitado</span>
                    <div class="info-value"><?= $usuario['hospital_solicitado'] ?></div>
                </div>
                <?php endif; ?>
            </div>

            <div class="section-divider"></div>

            <div class="botoes-container">
                <a href="area_usuario.php" class="botao-voltar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                    </svg>
                    Voltar para Área do Usuário
                </a>

                <a href="apagar_conta.php" class="botao-apagar" onclick="return confirm('Tem certeza que deseja apagar sua conta?')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z"/>
                    </svg>
                    Apagar Conta
                </a>
            </div>
        </div>
    </div>
</body>
</html>