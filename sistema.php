<?php
    session_start();
    include_once('config.php');

    if(!isset($_SESSION['admin'])) {
        header('Location: loginadm.php');
        exit();
    }

    $logado = $_SESSION['admin_nome'];

    //filtro por tipo sanguíneo
    $tipoSanguineo = isset($_GET['tipo_sanguineo']) ? $_GET['tipo_sanguineo'] : '';
    
    if(!empty($_GET['search']))
    {
        $data = $_GET['search'];
        if(!empty($tipoSanguineo)) {
            // Se há busca E filtro de tipo sanguíneo
            $sql = "SELECT * FROM usuarios WHERE (id LIKE '%$data%' OR nome_completo LIKE '%$data%' OR email LIKE '%$data%') AND tipo_sanguineo = '$tipoSanguineo' ORDER BY id DESC";
        } else {
            // Apenas busca
            $sql = "SELECT * FROM usuarios WHERE id LIKE '%$data%' OR nome_completo LIKE '%$data%' OR email LIKE '%$data%' ORDER BY id DESC";
        }
    }
    elseif(!empty($tipoSanguineo))
    {
        // Apenas filtro por tipo sanguíneo - mostra SOMENTE o tipo selecionado
        $sql = "SELECT * FROM usuarios WHERE tipo_sanguineo = '$tipoSanguineo' ORDER BY id DESC";
    }
    else
    {
        $sql = "SELECT * FROM usuarios ORDER BY id DESC";
    }

    $result = $conexao->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>SISTEMA | DOACAO</title>
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

        /*animações principais*/
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

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes float {
            0%, 100% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            50% { transform: translateY(-100px) rotate(180deg); }
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        /*partículas*/
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

        /*navbar*/
        .navbar {
            backdrop-filter: blur(10px);
            background: rgba(220, 53, 69, 0.9) !important;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
            animation: slideInLeft 0.8s ease-out;
            z-index: 10;
            position: relative;
        }

        .navbar-brand {
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        /*sair*/
        .btn-sair {
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            transform: perspective(1000px) rotateX(0deg);
        }

        .btn-sair::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.6s ease;
        }

        .btn-sair:hover::before {
            left: 100%;
        }

        .btn-sair:hover {
            background-color: #c82333 !important;
            transform: perspective(1000px) rotateX(-10deg) translateY(-5px) scale(1.02);
            box-shadow: 0 15px 35px rgba(220, 20, 60, 0.4), 0 5px 15px rgba(0, 0, 0, 0.3);
            
        }

        .btn-sair:active {
            transform: perspective(1000px) rotateX(5deg) translateY(2px) scale(0.98);
            box-shadow: 0 5px 15px rgba(220, 20, 60, 0.3);
        }

        /*boas-vindas*/
        h1 {
            animation: fadeInUp 1s ease-out 0.3s both;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            margin: 30px 0;
        }

        /*pesquisa e filtros*/
        .filters-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            animation: fadeInUp 1s ease-out 0.5s both;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .form-control, .form-select {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            color: white;
            transition: all 0.3s ease;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .form-select {
            color: white;
        }

        .form-select option {
            background: rgba(220, 53, 69, 0.9);
            color: white;
        }

        /*.form-select:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.4);
            box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25);
        }*/

        .btn-search, .btn-filter {
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .btn-search::before, .btn-filter::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transition: all 0.5s ease;
            transform: translate(-50%, -50%);
        }

        .btn-search:hover::before, .btn-filter:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-search:hover, .btn-filter:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 10px 25px rgba(0, 123, 255, 0.3);
        }

        /*tabela*/
        .table-container {
            animation: fadeInUp 1s ease-out 0.7s both;
            position: relative;
            z-index: 5;
            overflow: visible;
            width: 100%;
        }

        .table-bg {
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            overflow: hidden;
            width: 100%;
            table-layout: fixed;
        }

        /*tabela com os usuarios(parte preta)*/
        .user-row {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            animation: fadeInUp 0.6s ease-out both;
        }

        .user-row:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: scale(1.01);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .user-row:nth-child(even) {
            animation-delay: 0.1s;
        }

        .user-row:nth-child(odd) {
            animation-delay: 0.2s;
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

        .btn-action:active {
            transform: translateY(0) scale(1.05);
        }

        .table-sm {
            width: 100%;
            font-size: 0.75em;
        }

        .table-sm th,
        .table-sm td {
            padding: 0.5rem;
            vertical-align: middle;
            text-align: center;
            word-wrap: break-word;
        }

        .table-sm th {
            background: rgba(0, 0, 0, 0.3);
            font-weight: bold;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }

        /*adaptação para monitores grandes*/
        @media (min-width: 1440px) {
            .table-sm {
                font-size: 0.85rem;
                width: 100%;
            }
            .table-sm th, .table-sm td {
                padding: 0.6rem 0.5rem;
                word-wrap: break-word;
            }
        }

        /*adaptação para notebooks padrão*/
        @media (max-width: 1439px) and (min-width: 1024px) {
            .table-sm {
                font-size: 0.75rem;
                width: 100%;
                table-layout: fixed;
            }
            
            .table-sm th, .table-sm td {
                padding: 0.4rem 0.2rem;
                word-wrap: break-word;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 0;
            }
            
            /*ajuste das larguras das colunas para notebook*/
            .table-sm th:nth-child(1), .table-sm td:nth-child(1) { width: 3%; } /*#*/
            .table-sm th:nth-child(2), .table-sm td:nth-child(2) { width: 7%; } /*Nome*/
            .table-sm th:nth-child(3), .table-sm td:nth-child(3) { width: 7%; } /*Email*/
            .table-sm th:nth-child(4), .table-sm td:nth-child(4) { width: 5%; } /*Telefone*/
            .table-sm th:nth-child(5), .table-sm td:nth-child(5) { width: 4%; } /*CPF*/
            .table-sm th:nth-child(6), .table-sm td:nth-child(6) { width: 5%; } /*RG*/
            .table-sm th:nth-child(7), .table-sm td:nth-child(7) { width: 4%; } /*Sexo*/
            .table-sm th:nth-child(8), .table-sm td:nth-child(8) { width: 6%; } /*Data Nasc*/
            .table-sm th:nth-child(9), .table-sm td:nth-child(9) { width: 4%; } /*Peso*/
            .table-sm th:nth-child(10), .table-sm td:nth-child(10) { width: 3%; } /*Tipo Sang*/
            .table-sm th:nth-child(11), .table-sm td:nth-child(11) { width: 3%; } /*Já Doou*/
            .table-sm th:nth-child(12), .table-sm td:nth-child(12) { width: 3%; } /*Doação anterior*/
            .table-sm th:nth-child(13), .table-sm td:nth-child(13) { width: 3%; } /*Possui Doença*/
            .table-sm th:nth-child(14), .table-sm td:nth-child(14) { width: 5%; } /*Doenças*/
            .table-sm th:nth-child(15), .table-sm td:nth-child(15) { width: 4%; } /*Cidade*/
            .table-sm th:nth-child(16), .table-sm td:nth-child(16) { width: 4%; } /*Estado*/
            .table-sm th:nth-child(17), .table-sm td:nth-child(17) { width: 8%; } /*Endereço*/
            .table-sm th:nth-child(18), .table-sm td:nth-child(18) { width: 8%; } /*Hospital*/
            .table-sm th:nth-child(19), .table-sm td:nth-child(19) { width: 5%; } /*Status*/
            .table-sm th:nth-child(20), .table-sm td:nth-child(20) { width: 5%; } /*Ações*/
        }

        /*adaptação para notebook pequeno*/
        @media (max-width: 1023px) and (min-width: 768px) {
            .table-sm {
                font-size: 0.7rem;
                width: 100%;
                table-layout: fixed;
            }
            
            .table-sm th, .table-sm td {
                padding: 0.3rem 0.1rem;
                word-wrap: break-word;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 0;
                line-height: 1.2;
            }
            
            .filters-container {
                gap: 10px;
            }
            
            .form-control {
                width: 250px;
            }
            
            .form-select {
                width: 180px;
            }
        }

        /*tela mínima suportada*/
        @media (max-width: 767px) {
            .table-container {
                overflow-x: auto;
                margin: 0.65rem;
            }
            
            .table-bg {
                min-width: 1000px; /*força largura mínima com scroll horizontal(tela sempre quebra no notebook kk)*/
            }
            
            .table-sm {
                font-size: 0.75rem;
            }
            
            .table-sm th, .table-sm td {
                padding: 0.25rem 0.15rem;
                white-space: nowrap;
            }
            
            .filters-container {
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }
            
            .form-control, .form-select {
                width: 100%;
                max-width: 300px;
            }
            
            h1 {
                font-size: 1.5rem;
            }
        }

        /*ajuste adicional para botões de ação em telas menores*/
        @media (max-width: 1023px) {
            .btn-action {
                padding: 0.2rem 0.3rem;
                margin: 1px;
            }
            
            .btn-action svg {
                width: 12px;
                height: 12px;
            }
        }

        .btn-danger:hover{
            box-shadow: 0 10px 25px rgb(220, 20, 60);
            transform: translateY(-3px) scale(1.05);
        }
        .btn-success:hover{
            box-shadow: 0 10px 25px rgb(57, 143, 31);
            transform: translateY(-3px) scale(1.05);
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
            <a class="navbar-brand">SISTEMA DOAÇÃO</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
        <div class="lista_hosp">
            <a href="lista_hospital.php" class="btn btn-danger border border-white rounded-pill me-5 btn-lista_hospital">Lista de Hospitais</a>
    </div>
        <div class="d-flex">
            <a href="sair.php" class="btn btn-danger border border-white rounded-pill me-5 btn-sair">Sair</a>
        </div>
    </nav>
    
    <?php
        echo "<h1>Bem vindo <u>$logado</u></h1>";
    ?>
    
    <div class="filters-container">
        <input type="search" class="form-control" style="width: 300px;" placeholder="Pesquisar" id="pesquisar" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        <button onclick="searchData()" class="btn btn-primary btn-search">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
            </svg>
        </button>
        
        <select class="form-select <?php echo !empty($tipoSanguineo) ? 'filter-active' : ''; ?>" style="width: 200px;" id="tipoSanguineo" onchange="filterByBloodType()">
            <option value="">Todos os tipos sanguíneos</option>
            <option value="A+" <?php echo ($tipoSanguineo === 'N/D') ? 'selected' : ''; ?>>N/D</option>
            <option value="A+" <?php echo ($tipoSanguineo === 'A+') ? 'selected' : ''; ?>>A+</option>
            <option value="A-" <?php echo ($tipoSanguineo === 'A-') ? 'selected' : ''; ?>>A-</option>
            <option value="B+" <?php echo ($tipoSanguineo === 'B+') ? 'selected' : ''; ?>>B+</option>
            <option value="B-" <?php echo ($tipoSanguineo === 'B-') ? 'selected' : ''; ?>>B-</option>
            <option value="O+" <?php echo ($tipoSanguineo === 'O+') ? 'selected' : ''; ?>>O+</option>
            <option value="O-" <?php echo ($tipoSanguineo === 'O-') ? 'selected' : ''; ?>>O-</option>
            <option value="AB+" <?php echo ($tipoSanguineo === 'AB+') ? 'selected' : ''; ?>>AB+</option>
            <option value="AB-" <?php echo ($tipoSanguineo === 'AB-') ? 'selected' : ''; ?>>AB-</option>
        </select>

        <table class="table-sm text-white table-bg">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Nome Completo</th>
                    <th scope="col">Email</th>
                    <th scope="col">Telefone</th>
                    <th scope="col">CPF</th>
                    <th scope="col">RG</th>
                    <th scope="col">Sexo</th>
                    <th scope="col">Data de Nascimento</th>
                    <th scope="col">Peso</th>
                    <th scope="col">Tipo Sanguíneo</th>
                    <th scope="col">Já Doou</th>
                    <th scope="col">Doação Anterior</th>
                    <th scope="col">Possui Doença</th>
                    <th scope="col">Doenças</th>
                    <th scope="col">Cidade</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Endereço</th>
                    <th scope="col">Hospital Solicitado</th>
                    <th scope="col">Status</th>
                    <th scope="col">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    while($user_data = mysqli_fetch_assoc($result)) {
                        $highlightClass = (!empty($tipoSanguineo) && $user_data['tipo_sanguineo'] == $tipoSanguineo) ? 'highlighted-blood-type' : '';
                        echo "<tr class='user-row $highlightClass'>";
                        echo "<td>" .$user_data['id']."</td>";
                        echo "<td>" .$user_data['nome_completo']."</td>";
                        echo "<td>" .$user_data['email']."</td>";
                        echo "<td>" .$user_data['telefone']."</td>";
                        echo "<td>" .$user_data['cpf']."</td>";
                        echo "<td>" .$user_data['rg']."</td>";
                        echo "<td>" .$user_data['sexo']."</td>";
                        echo "<td>" .$user_data['data_nascimento']."</td>";
                        echo "<td>" .$user_data['peso']."</td>";
                        echo "<td>" .$user_data['tipo_sanguineo']."</td>";
                        echo "<td>" .$user_data['ja_doou']."</td>";
                        echo "<td>" .$user_data['tempo_ultima_doacao']."</td>";
                        echo "<td>" .$user_data['possui_doenca']."</td>";
                        echo "<td>" .$user_data['doencas']."</td>";
                        echo "<td>" .$user_data['cidade']."</td>";
                        echo "<td>" .$user_data['estado']."</td>";
                        echo "<td>" .$user_data['endereco']."</td>";
                        echo "<td>" .$user_data['hospital_solicitado']."</td>";

                        echo "<td>";
                        $status = isset($user_data['status_formulario']) ? $user_data['status_formulario'] : 'em_analise';
                        switch($status) {
                            case 'aprovado':
                                echo "<span class='status-aprovado'>Aprovado</span>";
                                break;
                            case 'recusado':
                                echo "<span class='status-recusado'>Recusado</span>";
                                break;
                            default:
                                echo "<span class='status-em-analise'>Em Análise</span>";
                                break;
                        }
                        echo "</td>";

                        echo "<td>";
                        
                        if($status == 'em_analise' || is_null($status)) {
                           echo "<a class='btn btn-sm btn-danger btn-action' href='delete.php?id=".$user_data['id']."' title='Recusar'>
                                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-x-lg' viewBox='0 0 16 16'>
                                <path d='M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z'/>
                                </svg>
                            </a>";

                            echo "<a class='btn btn-sm btn-success btn-action' href='aceitar.php?id=".$user_data['id']."' title='Aprovar'>
                                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-check2' viewBox='0 0 16 16'>
                                <path d='M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0'/>
                                </svg>
                            </a>";
                        } else {
                            echo "<span class='text-muted'>Processado</span>";
                        }
                        
                        echo "</td>";
                        echo "</tr>";
                    }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        var search = document.getElementById('pesquisar');

        search.addEventListener("keydown", function(event) {
            if (event.key === "Enter") 
            {
                searchData();
            }
        });

        function searchData()
        {
            window.location = 'sistema.php?search=' + search.value;
        }

        function filterByBloodType()
        {
            var tipoSanguineo = document.getElementById('tipoSanguineo').value;
            if(tipoSanguineo === '') {
                window.location = 'sistema.php';
            } else {
                window.location = 'sistema.php?tipo_sanguineo=' + tipoSanguineo;
            }
        }

        //função para filtrar por tipo sanguíneo
        function filterByBloodType() {
            const bloodType = document.getElementById('tipoSanguineo').value;
            if(bloodType === '') {
                window.location = '<?php echo $_SERVER['PHP_SELF']; ?>';
            } else {
                window.location = '<?php echo $_SERVER['PHP_SELF']; ?>?tipo_sanguineo=' + encodeURIComponent(bloodType);
            }
        }
    
    </script>
</body>

</html>
