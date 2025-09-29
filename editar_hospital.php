<?php
session_start();
include_once('config.php');

//verificar se é admin
if(!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

//verificar se foi passado um ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: lista_hospital.php');
    exit();
}

$hospital_id = (int)$_GET['id'];
$mensagem = '';
$tipo_mensagem = '';

//buscar dados do hospital
$sql_select = "SELECT * FROM hospitais WHERE id = ?";
$stmt_select = $conexao->prepare($sql_select);
$stmt_select->bind_param("i", $hospital_id);
$stmt_select->execute();
$result = $stmt_select->get_result();

if ($result->num_rows == 0) {
    header('Location: lista_hospital.php');
    exit();
}

$hospital = $result->fetch_assoc();
$stmt_select->close();

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome']);
    $tipos_necessarios = isset($_POST['tipos_necessarios']) ? implode(',', $_POST['tipos_necessarios']) : '';
    $urgencia = $_POST['urgencia'];
    $localizacao = trim($_POST['localizacao']);
    $telefone = trim($_POST['telefone']);
    
    // Validações
    if (empty($nome) || empty($tipos_necessarios) || empty($urgencia) || empty($localizacao) || empty($telefone)) {
        $mensagem = 'Todos os campos são obrigatórios!';
        $tipo_mensagem = 'danger';
    } else {
        $sql_update = "UPDATE hospitais SET nome = ?, tipos_necessarios = ?, urgencia = ?, localizacao = ?, telefone = ?, updated_at = NOW() WHERE id = ?";
        $stmt_update = $conexao->prepare($sql_update);
        
        if ($stmt_update) {
            $stmt_update->bind_param("sssssi", $nome, $tipos_necessarios, $urgencia, $localizacao, $telefone, $hospital_id);
            
            if ($stmt_update->execute()) {
                $mensagem = 'Hospital atualizado com sucesso!';
                $tipo_mensagem = 'success';
                
                //atualizar dados do hospital para exibir as mudanças
                $hospital['nome'] = $nome;
                $hospital['tipos_necessarios'] = $tipos_necessarios;
                $hospital['urgencia'] = $urgencia;
                $hospital['localizacao'] = $localizacao;
                $hospital['telefone'] = $telefone;
            } else {
                $mensagem = 'Erro ao atualizar hospital: ' . $stmt_update->error;
                $tipo_mensagem = 'danger';
            }
            
            $stmt_update->close();
        } else {
            $mensagem = 'Erro na preparação da consulta: ' . $conexao->error;
            $tipo_mensagem = 'danger';
        }
    }
}

$logado = $_SESSION['admin_nome'];
$tipos_sanguineos = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
$tipos_hospital = explode(',', $hospital['tipos_necessarios']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Editar Hospital - Sistema de Doação</title>
    <style>
        body {
            background: linear-gradient(135deg, #ff4757 0%, #ff3838 50%, #ff6b7a 100%);
            color: white;
            overflow-x: hidden;
            position: relative;
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
        }

        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            animation: float 8s infinite ease-in-out;
        }

        @keyframes float {
            0%, 100% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            50% { transform: translateY(-100px) rotate(180deg); }
        }

        .particle:nth-child(1) { width: 10px; height: 10px; left: 10%; animation-delay: 0s; }
        .particle:nth-child(2) { width: 15px; height: 15px; left: 20%; animation-delay: 1s; }
        .particle:nth-child(3) { width: 8px; height: 8px; left: 30%; animation-delay: 2s; }
        .particle:nth-child(4) { width: 12px; height: 12px; left: 40%; animation-delay: 3s; }
        .particle:nth-child(5) { width: 6px; height: 6px; left: 50%; animation-delay: 4s; }

        .navbar {
            backdrop-filter: blur(10px);
            background: rgb(220, 53, 70) !important;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
            z-index: 10;
            position: relative;
        }

        .card {
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(15px);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            margin: 20px auto;
            max-width: 800px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .form-control, .form-select {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            color: white;
            border-radius: 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: #fff;
            color: white;
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.3);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .form-select option {
            background: #333;
            color: white;
        }

        .form-check {
            background: rgba(255, 255, 255, 0.1);
            padding: 12px 15px;
            border-radius: 12px;
            margin-bottom: 10px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .form-check:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }

        .form-check-input:checked {
            background-color: #d63031;
            border-color: #d63031;
        }

        .form-check-label {
            color: white;
            font-weight: bold;
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            border-radius: 20px;
            padding: 12px 30px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(40, 167, 69, 0.4);
        }

        .btn-secondary {
            background: rgba(108, 117, 125, 0.8);
            border: none;
            border-radius: 20px;
            padding: 12px 30px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: rgba(108, 117, 125, 1);
            transform: translateY(-2px);
        }

        .alert {
            border-radius: 15px;
            border: none;
            font-weight: bold;
        }

        .blood-types-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }

        h1 {
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            margin: 30px 0;
            text-align: center;
        }

        .card-header {
            background: rgba(255, 255, 255, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            font-weight: bold;
            text-align: center;
        }

        .hospital-info {
            background: rgba(68, 68, 68, 0.51);
            border: 2px solid rgba(0, 0, 0, 0.5);
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .info-item {
            margin-bottom: 8px;
        }

        .info-label {
            font-weight: bold;
            color:rgba(250, 142, 142, 0.94);
        }

        .fade-in {
            animation: fadeIn 0.8s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <!--partículas animadas-->
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <!--navbar-->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold">🩸 Sistema de Doação</a>
        </div>
    </nav>

    <div class="container fade-in">
        <h1>✏️ Editar Hospital</h1>

        <?php if (!empty($mensagem)): ?>
            <div class="alert alert-<?php echo $tipo_mensagem; ?> alert-dismissible fade show" role="alert">
                <?php echo $mensagem; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="hospital-info">
            <h5>📍 Informações Atuais do Hospital</h5>
            <div class="info-item">
                <span class="info-label">Nome:</span> <?php echo htmlspecialchars($hospital['nome']); ?>
            </div>
            <div class="info-item">
                <span class="info-label">Tipos Necessários:</span> <?php echo htmlspecialchars($hospital['tipos_necessarios']); ?>
            </div>
            <div class="info-item">
                <span class="info-label">Urgência:</span> 
                <span class="badge bg-<?php echo $hospital['urgencia'] == 'alta' ? 'danger' : ($hospital['urgencia'] == 'media' ? 'warning' : 'success'); ?>">
                    <?php echo ucfirst($hospital['urgencia']); ?>
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Localização:</span> <?php echo htmlspecialchars($hospital['localizacao']); ?>
            </div>
            <div class="info-item">
                <span class="info-label">Telefone:</span> <?php echo htmlspecialchars($hospital['telefone']); ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">✏️ Editar Dados do Hospital</h4>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="nome" class="form-label">🏥 Nome do Hospital</label>
                        <input type="text" class="form-control" id="nome" name="nome" 
                               value="<?php echo htmlspecialchars($hospital['nome']); ?>" 
                               placeholder="Digite o nome do hospital" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">🩸 Tipos Sanguíneos Necessários</label>
                        <div class="blood-types-grid">
                            <?php foreach ($tipos_sanguineos as $tipo): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           id="tipo_<?php echo $tipo; ?>" name="tipos_necessarios[]" 
                                           value="<?php echo $tipo; ?>"
                                           <?php echo in_array($tipo, $tipos_hospital) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="tipo_<?php echo $tipo; ?>">
                                        <?php echo $tipo; ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="urgencia" class="form-label">⚡ Nível de Urgência</label>
                        <select class="form-select" id="urgencia" name="urgencia" required>
                            <option value="">Selecione o nível de urgência</option>
                            <option value="baixa" <?php echo $hospital['urgencia'] == 'baixa' ? 'selected' : ''; ?>>🟢 Baixa</option>
                            <option value="media" <?php echo $hospital['urgencia'] == 'media' ? 'selected' : ''; ?>>🟡 Média</option>
                            <option value="alta" <?php echo $hospital['urgencia'] == 'alta' ? 'selected' : ''; ?>>🟠 Alta</option>
                            <option value="critica" <?php echo $hospital['urgencia'] == 'critica' ? 'selected' : ''; ?>>🔴 Crítica</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="localizacao" class="form-label">📍 Localização</label>
                        <input type="text" class="form-control" id="localizacao" name="localizacao" 
                               value="<?php echo htmlspecialchars($hospital['localizacao']); ?>" 
                               placeholder="Digite a localização do hospital" required>
                    </div>

                    <div class="mb-3">
                        <label for="telefone" class="form-label">📞 Telefone</label>
                        <input type="text" class="form-control" id="telefone" name="telefone" 
                               value="<?php echo htmlspecialchars($hospital['telefone']); ?>" 
                               placeholder="Digite o telefone do hospital" required>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-success me-3">
                            💾 Salvar Alterações
                        </button>
                        <a href="lista_hospital.php" class="btn btn-secondary">
                            ↩️ Voltar para Lista
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        //auto-hide alerts após 5 segundos
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        //validação do formulário
        document.querySelector('form').addEventListener('submit', function(e) {
            const checkboxes = document.querySelectorAll('input[name="tipos_necessarios[]"]:checked');
            if (checkboxes.length === 0) {
                e.preventDefault();
                alert('Por favor, selecione pelo menos um tipo sanguíneo!');
            }
        });
    </script>
</body>
</html>