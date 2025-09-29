<?php
session_start();
include_once('config.php'); // Incluir conexão com banco de dados

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$sql_usuario = "SELECT nome_completo, tipo_sanguineo FROM usuarios WHERE id = ?";
$stmt_usuario = $conexao->prepare($sql_usuario);
$stmt_usuario->bind_param("i", $usuario_id);
$stmt_usuario->execute();
$result_usuario = $stmt_usuario->get_result();

if ($result_usuario->num_rows === 1) {
    $dados_usuario = $result_usuario->fetch_assoc();
    $usuario_nome = $dados_usuario['nome_completo'];
    $usuario_tipo = $dados_usuario['tipo_sanguineo'];
} else {
    // Fallback para dados da sessão se não encontrar no banco
    $usuario_nome = $_SESSION['usuario_nome'] ?? 'Usuário';
    $usuario_tipo = $_SESSION['usuario_tipo_sanguineo'] ?? 'N/D';
}

// Buscar hospitais do banco de dados
$sql = "SELECT * FROM hospitais ORDER BY 
        CASE urgencia 
            WHEN 'critica' THEN 1 
            WHEN 'alta' THEN 2 
            WHEN 'media' THEN 3 
            WHEN 'baixa' THEN 4 
        END, nome ASC";

$result = $conexao->query($sql);
$hospitais = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Converter tipos_necessarios de string para array
        $tipos_array = !empty($row['tipos_necessarios']) ? explode(',', $row['tipos_necessarios']) : [];
        
        $hospitais[] = [
            'id' => $row['id'],
            'nome' => $row['nome'],
            'tipos_necessarios' => $tipos_array,
            'urgencia' => $row['urgencia'],
            'localizacao' => $row['localizacao'],
            'telefone' => $row['telefone']
        ];
    }
} else {
    // Dados de fallback caso não existam hospitais no banco
    $hospitais = [
        [
            'id' => 0,
            'nome' => 'Nenhum hospital cadastrado',
            'tipos_necessarios' => [],
            'urgencia' => 'baixa',
            'localizacao' => 'N/A',
            'telefone' => 'N/A'
        ]
    ];
}

// Separar hospitais que precisam do tipo do usuário
$hospitais_prioritarios = [];
$outros_hospitais = [];

foreach ($hospitais as $hospital) {
    if (in_array($usuario_tipo, $hospital['tipos_necessarios'])) {
        $hospitais_prioritarios[] = $hospital;
    } else {
        $outros_hospitais[] = $hospital;
    }
}

// Função para ordenar por urgência
function ordenarPorUrgencia($a, $b) {
    $ordem = ['critica' => 0, 'alta' => 1, 'media' => 2, 'baixa' => 3];
    return $ordem[$a['urgencia']] - $ordem[$b['urgencia']];
}

usort($hospitais_prioritarios, 'ordenarPorUrgencia');
usort($outros_hospitais, 'ordenarPorUrgencia');
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área do Doador - Laços de Sangue</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: linear-gradient(135deg, #ff4757 0%, #ff3838 50%, #ff6b7a 100%);
            color: #333;
            overflow-x: hidden;
            position: relative;
            min-height: 100vh;
        }

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
            animation: float 8s infinite ease-in-out;
        }

        .particle:nth-child(1) { width: 10px; height: 10px; left: 10%; animation-delay: 0s; }
        .particle:nth-child(2) { width: 15px; height: 15px; left: 20%; animation-delay: 1s; }
        .particle:nth-child(3) { width: 8px; height: 8px; left: 30%; animation-delay: 2s; }
        .particle:nth-child(4) { width: 12px; height: 12px; left: 40%; animation-delay: 3s; }
        .particle:nth-child(5) { width: 6px; height: 6px; left: 50%; animation-delay: 4s; }
        .particle:nth-child(6) { width: 14px; height: 14px; left: 60%; animation-delay: 5s; }
        .particle:nth-child(7) { width: 9px; height: 9px; left: 70%; animation-delay: 6s; }
        .particle:nth-child(8) { width: 11px; height: 11px; left: 80%; animation-delay: 7s; }
        .particle:nth-child(9) { width: 7px; height: 7px; left: 90%; animation-delay: 8s; }

        @keyframes float {
            0%, 100% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            50% { transform: translateY(-100px) rotate(180deg); }
        }

        .header-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .logo {
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .nav-buttons {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .nav-btn {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 10px 20px;
            border-radius: 25px;
            color: white;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .nav-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
        }

        .nav-btn.logout {
            background: rgba(220, 20, 60, 0.8);
        }

        .nav-btn.logout:hover {
            background: rgba(220, 20, 60, 1);
        }

        .main-container {
            padding-top: 100px;
            min-height: 100vh;
        }

        .welcome-section {
            text-align: center;
            padding: 40px 20px;
            color: white;
            animation: fadeInUp 1s ease-out;
        }

        .welcome-section h1 {
            font-size: clamp(2rem, 4vw, 3rem);
            margin-bottom: 10px;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.3);
        }

        .blood-type-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.9);
            color: #d63031;
            padding: 15px 30px;
            border-radius: 50px;
            font-size: 1.5rem;
            font-weight: bold;
            margin: 20px 0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            border: 3px solid rgba(255, 255, 255, 0.3);
        }

        .info-section {
            background: rgba(255, 255, 255, 0.95);
            margin: 40px 20px;
            padding: 40px;
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            animation: fadeInUp 1.2s ease-out;
        }

        .info-section h2 {
            color: #d63031;
            font-size: 2rem;
            margin-bottom: 20px;
            text-align: center;
            position: relative;
        }

        .info-section h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, #d63031, #ff6b7a);
            border-radius: 2px;
        }

        .donation-benefits {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }

        .benefit-card {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 15px;
            border-left: 5px solid #d63031;
            transition: all 0.3s ease;
        }

        .benefit-card:hover {
            background: #fff;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            transform: translateY(-5px);
        }

        .benefit-icon {
            font-size: 2rem;
            margin-bottom: 15px;
            display: block;
        }

        .hospitals-container {
            background: rgba(255, 255, 255, 0.95);
            margin: 20px;
            padding: 40px;
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }

        .section-title {
            font-size: 1.8rem;
            color: #d63031;
            margin-bottom: 30px;
            text-align: center;
            position: relative;
        }

        .priority-alert {
            background: linear-gradient(135deg, #ff4757, #ff3838);
            color: white;
            padding: 15px;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 30px;
            font-weight: bold;
            box-shadow: 0 10px 25px rgba(255, 71, 87, 0.3);
        }

        .hospital-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 2px solid transparent;
            transition: all 0.3s ease;
            position: relative;
        }

        .hospital-card.priority {
            border-color: #d63031;
            background: linear-gradient(135deg, #fff5f5, #ffffff);
        }

        .hospital-card.priority::before {
            content: '🔥 NECESSITA SEU TIPO SANGUÍNEO';
            position: absolute;
            top: -10px;
            left: 20px;
            background: #d63031;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .hospital-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .hospital-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .hospital-name {
            font-size: 1.3rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .urgency-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            text-transform: uppercase;
        }

        .urgency-critica { background: #ff4757; color: white; }
        .urgency-alta { background: #ff6b7a; color: white; }
        .urgency-media { background: #ffa726; color: white; }
        .urgency-baixa { background: #66bb6a; color: white; }

        .hospital-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .info-item {
            display: flex;
            align-items: center;
            font-size: 0.9rem;
            color: #666;
        }

        .info-icon {
            margin-right: 8px;
            font-size: 1.1rem;
        }

        .blood-types {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 15px 0;
        }

        .blood-type {
            background: #f1f3f4;
            padding: 5px 12px;
            border-radius: 15px;
            font-weight: bold;
            font-size: 0.9rem;
        }

        .blood-type.match {
            background: #d63031;
            color: white;
        }

        .submit-btn {
            background: linear-gradient(135deg, #d63031, #ff4757);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            font-size: 1rem;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(214, 48, 49, 0.3);
        }

        .submit-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .msg-status {
            margin-top: 15px;
            padding: 10px;
            border-radius: 10px;
            font-weight: bold;
            text-align: center;
        }

        .compatibility-info {
            background: linear-gradient(135deg, #e3f2fd, #f3e5f5);
            padding: 30px;
            border-radius: 20px;
            margin: 30px 0;
            border-left: 5px solid #2196f3;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .info-section, .hospitals-container {
                margin: 20px 10px;
                padding: 25px 20px;
            }
            
            .hospital-info {
                grid-template-columns: 1fr;
            }
            
            .nav-buttons {
                flex-direction: column;
                gap: 5px;
            }
            
            .nav-btn {
                padding: 8px 15px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <nav class="header-nav">
        <div class="logo">🩸 Laços de Sangue</div>
        <div class="nav-buttons">
            <a href="perfil.php" class="nav-btn">👤 Meu Perfil</a>
            <a href="sair.php" class="nav-btn logout">🚪 Sair</a>
        </div>
    </nav>

    <div class="main-container">
        <section class="welcome-section">
            <h1>Bem-vindo(a), <?=htmlspecialchars($usuario_nome)?>!</h1>
            <div class="blood-type-badge">
                🩸 Tipo Sanguíneo: <?= htmlspecialchars($usuario_tipo) ?>
            </div>
            <p style="font-size: 1.1rem; opacity: 0.9; margin-top: 15px;">
                Você é um herói! Sua doação pode salvar até 4 vidas.
            </p>
        </section>

        <section class="info-section">
            <h2>💝 Benefícios da Doação Regular</h2>
            <div class="donation-benefits">
                <div class="benefit-card">
                    <span class="benefit-icon">❤️</span>
                    <h3>Saúde Cardiovascular</h3>
                    <p>A doação regular estimula a renovação das células sanguíneas e pode reduzir o risco de doenças cardíacas ao diminuir o excesso de ferro no organismo.</p>
                </div>
                <div class="benefit-card">
                    <span class="benefit-icon">🔬</span>
                    <h3>Check-up Gratuito</h3>
                    <p>A cada doação, você recebe exames gratuitos que detectam doenças como hepatite, sífilis, HIV, doença de Chagas e outras infecções.</p>
                </div>
                <div class="benefit-card">
                    <span class="benefit-icon">🏃‍♂️</span>
                    <h3>Renovação Celular</h3>
                    <p>Seu organismo produz novas células sanguíneas para repor as doadas, mantendo seu sistema circulatório sempre renovado e saudável.</p>
                </div>
                <div class="benefit-card">
                    <span class="benefit-icon">😊</span>
                    <h3>Bem-estar Mental</h3>
                    <p>O ato de doar libera endorfinas e proporciona uma sensação de propósito e satisfação pessoal única.</p>
                </div>
            </div>
            
            <div class="compatibility-info">
                <h3>🧬 Compatibilidade do Tipo <?= htmlspecialchars($usuario_tipo) ?></h3>
                <p><strong>Você pode doar para:</strong> 
                <?php
                $compatibilidade = [
                    'N/D' => ['AB+ (Receptor Universal), porém deve-se realizar testes para mais informações.'],
                    'A+' => ['A+', 'AB+'],
                    'A-' => ['A+', 'A-', 'AB+', 'AB-'],
                    'B+' => ['B+', 'AB+'],
                    'B-' => ['B+', 'B-', 'AB+', 'AB-'],
                    'AB+' => ['AB+'],
                    'AB-' => ['AB+', 'AB-'],
                    'O+' => ['A+', 'B+', 'AB+', 'O+'],
                    'O-' => ['Todos os tipos (Doador Universal)']
                ];
                echo is_array($compatibilidade[$usuario_tipo]) ? 
                     implode(', ', $compatibilidade[$usuario_tipo]) : 
                     $compatibilidade[$usuario_tipo];
                ?>
                </p>
            </div>
        </section>

        <div class="hospitals-container">
            <?php if (!empty($hospitais_prioritarios)): ?>
                <div class="priority-alert">
                    🚨 URGENTE: Hospitais precisando do seu tipo sanguíneo (<?= htmlspecialchars($usuario_tipo) ?>)!
                </div>
                
                <h2 class="section-title">🎯 Hospitais Prioritários para Você</h2>
                
                <?php foreach ($hospitais_prioritarios as $hospital): ?>
                    <div class="hospital-card priority">
                        <div class="hospital-header">
                            <div>
                                <div class="hospital-name"><?= htmlspecialchars($hospital['nome']) ?></div>
                            </div>
                            <span class="urgency-badge urgency-<?= $hospital['urgencia'] ?>">
                                <?= ucfirst($hospital['urgencia']) ?>
                            </span>
                        </div>
                        
                        <div class="hospital-info">
                            <div class="info-item">
                                <span class="info-icon">📍</span>
                                <?= htmlspecialchars($hospital['localizacao']) ?>
                            </div>
                            <div class="info-item">
                                <span class="info-icon">📞</span>
                                <?= htmlspecialchars($hospital['telefone']) ?>
                            </div>
                        </div>
                        
                        <p><strong>Tipos sanguíneos necessários:</strong></p>
                        <div class="blood-types">
                            <?php foreach ($hospital['tipos_necessarios'] as $tipo): ?>
                                <span class="blood-type <?= $tipo === $usuario_tipo ? 'match' : '' ?>">
                                    <?= htmlspecialchars($tipo) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                        
                        <form class="ficha-form">
                            <input type="hidden" name="hospital" value="<?= htmlspecialchars($hospital['nome']) ?>">
                            <button class="submit-btn" type="submit">💌 Enviar Ficha Médica</button>
                        </form>
                        <div class="msg-status"></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <h2 class="section-title" style="margin-top: 50px;">🏥 Outros Hospitais</h2>
            
            <?php foreach ($outros_hospitais as $hospital): ?>
                <div class="hospital-card">
                    <div class="hospital-header">
                        <div>
                            <div class="hospital-name"><?= htmlspecialchars($hospital['nome']) ?></div>
                        </div>
                        <span class="urgency-badge urgency-<?= $hospital['urgencia'] ?>">
                            <?= ucfirst($hospital['urgencia']) ?>
                        </span>
                    </div>
                    
                    <div class="hospital-info">
                        <div class="info-item">
                            <span class="info-icon">📍</span>
                            <?= htmlspecialchars($hospital['localizacao']) ?>
                        </div>
                        <div class="info-item">
                            <span class="info-icon">📞</span>
                            <?= htmlspecialchars($hospital['telefone']) ?>
                        </div>
                    </div>
                    
                    <p><strong>Tipos sanguíneos necessários:</strong></p>
                    <div class="blood-types">
                        <?php foreach ($hospital['tipos_necessarios'] as $tipo): ?>
                            <span class="blood-type <?= $tipo === $usuario_tipo ? 'match' : '' ?>">
                                <?= htmlspecialchars($tipo) ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                    
                    <form class="ficha-form">
                        <input type="hidden" name="hospital" value="<?= htmlspecialchars($hospital['nome']) ?>">
                        <button class="submit-btn" type="submit">💌 Enviar Ficha Médica</button>
                    </form>
                    <div class="msg-status"></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        document.querySelectorAll('.ficha-form').forEach(form => {
            form.addEventListener('submit', function(event) {
                event.preventDefault(); 

                const formData = new FormData(this);
                const statusDiv = this.nextElementSibling;
                const button = this.querySelector('.submit-btn');

                //desabilita botão durante o envio
                button.disabled = true;
                button.textContent = '📤 Enviando...';

                fetch('enviar_ficha.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    statusDiv.textContent = '✅ ' + data;
                    statusDiv.style.background = '#d4edda';
                    statusDiv.style.color = '#155724';
                    statusDiv.style.border = '1px solid #c3e6cb';

                    //desabilitar todos os botões após sucesso
                    document.querySelectorAll('.submit-btn').forEach(btn => {
                        btn.disabled = true;
                        btn.style.cursor = 'not-allowed';
                        btn.textContent = "✅ Solicitação Enviada";
                    });

                    setTimeout(() => {
                        statusDiv.textContent = '';
                        statusDiv.style.background = '';
                        statusDiv.style.color = '';
                        statusDiv.style.border = '';
                    }, 5000);
                })
                .catch(err => {
                    statusDiv.textContent = '❌ Erro ao enviar ficha médica.';
                    statusDiv.style.background = '#f8d7da';
                    statusDiv.style.color = '#721c24';
                    statusDiv.style.border = '1px solid #f5c6cb';
                    
                    button.disabled = false;
                    button.textContent = '💌 Enviar Ficha Médica';

                    setTimeout(() => {
                        statusDiv.textContent = '';
                        statusDiv.style.background = '';
                        statusDiv.style.color = '';
                        statusDiv.style.border = '';
                    }, 5000);

                    console.error(err);
                });
            });
        });

        //animação de entrada dos elementos
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.hospital-card').forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = `all 0.6s ease ${index * 0.1}s`;
            observer.observe(card);
        });
    </script>
</body>

</html>
