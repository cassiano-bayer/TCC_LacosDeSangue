<?php
session_start();
include_once('config.php');

// Verificar se é admin
if(!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

$mensagem = '';
$tipo_mensagem = '';

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
        $sql = "INSERT INTO hospitais (nome, tipos_necessarios, urgencia, localizacao, telefone) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conexao->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("sssss", $nome, $tipos_necessarios, $urgencia, $localizacao, $telefone);
            
            if ($stmt->execute()) {
                $mensagem = 'Hospital adicionado com sucesso!';
                $tipo_mensagem = 'success';
                // Limpar campos após sucesso
                $_POST = array();
            } else {
                $mensagem = 'Erro ao adicionar hospital: ' . $stmt->error;
                $tipo_mensagem = 'danger';
            }
            
            $stmt->close();
        } else {
            $mensagem = 'Erro na preparação da consulta: ' . $conexao->error;
            $tipo_mensagem = 'danger';
        }
    }
}

$logado = $_SESSION['admin_nome'];
$tipos_sanguineos = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Adicionar Hospital - Sistema de Doação</title>
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
            background: rgba(220, 53, 69, 0.9) !important;
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

        .btn-primary {
            background: linear-gradient(135deg, #007bff, #0056b3);
            border: none;
            border-radius: 20px;
            padding: 12px 30px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 123, 255, 0.4);
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
    </style>
</head>
<body>
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <nav class="navbar navbar-expand-lg navbar-dark bg-danger">
        <div class="container-fluid">
            <a class="navbar-brand">🏥 ADICIONAR HOSPITAL</a>
        </div>
    </nav>

    <div class="container-fluid">
        <h1>Adicionar Novo Hospital - <?php echo htmlspecialchars($logado); ?></h1>

        <?php if (!empty($mensagem)): ?>
            <div class="alert alert-<?php echo $tipo_mensagem; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($mensagem); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">➕ Dados do Hospital</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nome" class="form-label">Nome do Hospital</label>
                            <input type="text" class="form-control" id="nome" name="nome" 
                                   placeholder="Ex: Hospital São João"
                                   value="<?php echo isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : ''; ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input type="text" class="form-control" id="telefone" name="telefone" 
                                   placeholder="(51) 3333-0000"
                                   value="<?php echo isset($_POST['telefone']) ? htmlspecialchars($_POST['telefone']) : ''; ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="localizacao" class="form-label">Localização</label>
                            <input type="text" class="form-control" id="localizacao" name="localizacao" 
                                   placeholder="Ex: Zona Norte"
                                   value="<?php echo isset($_POST['localizacao']) ? htmlspecialchars($_POST['localizacao']) : ''; ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="urgencia" class="form-label">Nível de Urgência</label>
                            <select class="form-select" id="urgencia" name="urgencia" required>
                                <option value="">Selecione a urgência</option>
                                <option value="critica" <?php echo (isset($_POST['urgencia']) && $_POST['urgencia'] == 'critica') ? 'selected' : ''; ?>>Crítica</option>
                                <option value="alta" <?php echo (isset($_POST['urgencia']) && $_POST['urgencia'] == 'alta') ? 'selected' : ''; ?>>Alta</option>
                                <option value="media" <?php echo (isset($_POST['urgencia']) && $_POST['urgencia'] == 'media') ? 'selected' : ''; ?>>Média</option>
                                <option value="baixa" <?php echo (isset($_POST['urgencia']) && $_POST['urgencia'] == 'baixa') ? 'selected' : ''; ?>>Baixa</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Tipos Sanguíneos Necessários</label>
                        <div class="blood-types-grid">
                            <?php foreach ($tipos_sanguineos as $tipo): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           id="tipo_<?php echo $tipo; ?>" name="tipos_necessarios[]" 
                                           value="<?php echo $tipo; ?>"
                                           <?php echo (isset($_POST['tipos_necessarios']) && in_array($tipo, $_POST['tipos_necessarios'])) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="tipo_<?php echo $tipo; ?>">
                                        <?php echo $tipo; ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="d-flex gap-3 justify-content-center">
                        <button type="submit" class="btn btn-primary">
                            💾 Salvar Hospital
                        </button>
                        <a href="lista_hospital.php" class="btn btn-secondary">
                            ❌ Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>