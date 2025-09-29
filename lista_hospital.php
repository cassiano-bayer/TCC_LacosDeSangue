<?php
session_start();
include_once('config.php');

//erificar se é admin
if(!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

$logado = $_SESSION['admin_nome'];

//buscar hospitais do banco de dados
$sql = "SELECT * FROM hospitais ORDER BY 
    CASE urgencia 
        WHEN 'critica' THEN 1 
        WHEN 'alta' THEN 2 
        WHEN 'media' THEN 3 
        WHEN 'baixa' THEN 4 
    END, nome";
$result = $conexao->query($sql);

//verificar se houve erro na consulta
if (!$result) {
    die("Erro na consulta: " . $conexao->error);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Gerenciamento de Hospitais - Sistema de Doação</title>
    <style>
        body {
            background: linear-gradient(135deg, #ff4757 0%, #ff3838 50%, #ff6b7a 100%);
            color: white;
            text-align: center;
            overflow-x: hidden;
            position: relative;
            min-height: 100vh;
            animation: fadeIn 1s ease-out;
            font-family: Arial, Helvetica, sans-serif;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes float {
            0%, 100% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            50% { transform: translateY(-100px) rotate(180deg); }
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

        .particle:nth-child(1) { width: 10px; height: 10px; left: 10%; animation-delay: 0s; }
        .particle:nth-child(2) { width: 15px; height: 15px; left: 20%; animation-delay: 1s; }
        .particle:nth-child(3) { width: 8px; height: 8px; left: 30%; animation-delay: 2s; }
        .particle:nth-child(4) { width: 12px; height: 12px; left: 40%; animation-delay: 3s; }
        .particle:nth-child(5) { width: 6px; height: 6px; left: 50%; animation-delay: 4s; }
        .particle:nth-child(6) { width: 14px; height: 14px; left: 60%; animation-delay: 5s; }
        .particle:nth-child(7) { width: 9px; height: 9px; left: 70%; animation-delay: 6s; }
        .particle:nth-child(8) { width: 11px; height: 11px; left: 80%; animation-delay: 7s; }
        .particle:nth-child(9) { width: 7px; height: 7px; left: 90%; animation-delay: 8s; }

        .navbar {
            backdrop-filter: blur(10px);
            background: rgba(220, 53, 69, 0.9) !important;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
            animation: fadeInUp 0.8s ease-out;
            z-index: 10;
            position: relative;
        }

        .navbar-brand {
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .btn-nav {
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            transform: perspective(1000px) rotateX(0deg);
        }

        .btn-nav::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.6s ease;
        }

        .btn-nav:hover::before {
            left: 100%;
        }

        .btn-nav:hover {
            background-color: #c82333 !important;
            transform: perspective(1000px) rotateX(-10deg) translateY(-5px) scale(1.02);
            box-shadow: 0 15px 35px rgba(220, 20, 60, 0.4), 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        h1 {
            animation: fadeInUp 1s ease-out 0.3s both;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            margin: 30px 0;
        }

        .container-fluid {
            animation: fadeInUp 1s ease-out 0.7s both;
            position: relative;
            z-index: 5;
        }

        .card {
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(109, 13, 13, 0.1);
            border-radius: 15px;
            margin-bottom: 20px;
            animation: fadeInUp 0.6s ease-out both;
        }

        .card-header {
            background: rgba(255, 255, 255, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            font-weight: bold;
        }

        .table-dark {
            background: rgba(0, 0, 0, 0.3);
        }

        .table-dark th {
            background: rgba(0, 0, 0, 0.5);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .table-dark td {
            border-color: rgba(255, 255, 255, 0.1);
        }

        .btn-action {
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            margin: 2px;
        }

        .btn-action::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transition: all 0.4s ease;
            transform: translate(-50%, -50%);
        }

        .btn-action:hover::before {
            width: 100px;
            height: 100px;
        }

        .btn-action:hover {
            transform: translateY(-2px) scale(1.1);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }

        .btn-primary:hover {
            box-shadow: 0 10px 25px rgba(0, 123, 255, 0.4);
        }

        .btn-warning:hover {
            box-shadow: 0 10px 25px rgba(255, 193, 7, 0.4);
        }

        .btn-danger:hover {
            box-shadow: 0 10px 25px rgba(220, 53, 69, 0.4);
        }

        .btn-success:hover {
            box-shadow: 0 10px 25px rgba(40, 167, 69, 0.4);
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

        .blood-type {
            display: inline-block;
            background: #d63031;
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.8rem;
            font-weight: bold;
            margin: 2px;
        }

        .btn-add {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            color: white;
            padding: 12px 30px;
            font-size: 1.1rem;
            border-radius: 25px;
            font-weight: bold;
            transition: all 0.3s ease;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }

        .btn-add:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 10px 25px rgba(40, 167, 69, 0.4);
            color: white;
        }

        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.8rem;
            }
            
            .btn-action {
                padding: 0.3rem 0.5rem;
                margin: 1px;
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

    <nav class="navbar navbar-expand-lg navbar-dark bg-danger">
        <div class="container-fluid">
            <a class="navbar-brand">🏥 GERENCIAMENTO DE HOSPITAIS</a>
            <div class="d-flex gap-2">
                <a href="sistema.php" class="btn btn-danger border border-white rounded-pill btn-nav">
                    ← Voltar ao Sistema
                </a>
            </div>
        </div>
    </nav>

    <?php echo "<h1>Gerenciamento de Hospitais - <u>$logado</u></h1>"; ?>

    <div class="container-fluid mt-4">
        <div class="row justify-content-center">
            <div class="col-12">
                <a href="adicionar_hospital.php" class="btn btn-add">
                    ➕ Adicionar Novo Hospital
                </a>
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">🏥 Lista de Hospitais Cadastrados</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-dark">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome do Hospital</th>
                                        <th>Tipos Sanguíneos Necessários</th>
                                        <th>Urgência</th>
                                        <th>Localização</th>
                                        <th>Telefone</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result && $result->num_rows > 0) {
                                        while($hospital = mysqli_fetch_assoc($result)) {
                                            echo "<tr>";
                                            echo "<td>" . $hospital['id'] . "</td>";
                                            echo "<td><strong>" . htmlspecialchars($hospital['nome']) . "</strong></td>";
                                            echo "<td>";
                                            
                                            //processar tipos sanguíneos
                                            $tipos = explode(',', $hospital['tipos_necessarios']);
                                            foreach($tipos as $tipo) {
                                                echo "<span class='blood-type'>" . htmlspecialchars(trim($tipo)) . "</span>";
                                            }
                                            
                                            echo "</td>";
                                            echo "<td><span class='urgency-badge urgency-" . $hospital['urgencia'] . "'>" . ucfirst($hospital['urgencia']) . "</span></td>";
                                            echo "<td>" . htmlspecialchars($hospital['localizacao']) . "</td>";
                                            echo "<td>" . htmlspecialchars($hospital['telefone']) . "</td>";
                                            echo "<td>";
                                            echo "<a class='btn btn-sm btn-warning btn-action' href='editar_hospital.php?id=" . $hospital['id'] . "' title='Editar'>
                                                    <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pencil' viewBox='0 0 16 16'>
                                                        <path d='M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708L9.708 9l-3 3a.5.5 0 0 1-.708 0l-3-3a.5.5 0 0 1 0-.708L9.146.146zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm-1.76 1.767L4.5 9.214v2.05h2.05l4.947-4.947-1.05-1.05z'/>
                                                    </svg>
                                                  </a>";
                                            echo "<a class='btn btn-sm btn-danger btn-action' href='excluir_hospital.php?id=" . $hospital['id'] . "' title='Excluir' onclick='return confirm(\"Tem certeza que deseja excluir este hospital?\")'>
                                                    <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-trash' viewBox='0 0 16 16'>
                                                        <path d='M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z'/>
                                                        <path fill-rule='evenodd' d='M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z'/>
                                                    </svg>
                                                  </a>";
                                            echo "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='7' class='text-center'>Nenhum hospital cadastrado</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>