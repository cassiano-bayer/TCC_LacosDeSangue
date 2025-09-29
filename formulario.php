<?php
if(isset($_POST['submit']))
{
    include_once('config.php');

    //validação de senha
    $senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';
    
    if ($senha !== $confirmar_senha) {
        $erro = "As senhas não são iguais, verifique-as e tente novamente.";
    } else {
        //validação de email
        $email = $_POST['email'] ?? '';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erro = "Por favor, insira um endereço de email válido.";
        } else {
            //validação de idade para doação de sangue
            $data_nascimento = $_POST['data_nascimento'] ?? '';
            
            if (!empty($data_nascimento)) {
                $hoje = new DateTime();
                $nascimento = new DateTime($data_nascimento);
                $idade = $hoje->diff($nascimento)->y;
                
                if ($idade < 16 || $idade > 69) {
                    if ($idade < 16) {
                        $erro = "Para doar sangue é necessário ter entre 16 e 69 anos. Você precisa ter pelo menos 16 anos para poder doar.";
                    } else {
                        $erro = "Para doar sangue é necessário ter entre 16 e 69 anos. Infelizmente, a idade máxima para doação é 69 anos.";
                    }
                }
            }
            
            //se não tem erro de idade, continua com as outras validações
            if (!isset($erro)) {
                //lista de doenças que impedem doação
                $doencas_impedem_doacao = [
                    'HIV/AIDS',
                    'Hepatite B ou C', 
                    'Doença de Chagas',
                    'Sífilis',
                    'HTLV',
                    'Malária',
                    'Câncer',
                    'Tuberculose',
                    'Diabetes tipo 1',
                    'Doença autoimune'
                ];
                
                //verifica se possui doenças que impedem doação
                $possui_doenca_impeditiva = false;
                if (isset($_POST['doenca_nome']) && is_array($_POST['doenca_nome'])) {
                    foreach ($_POST['doenca_nome'] as $doenca) {
                        if (in_array($doenca, $doencas_impedem_doacao)) {
                            $possui_doenca_impeditiva = true;
                            break;
                        }
                    }
                }
                
                if ($possui_doenca_impeditiva) {
                    $erro = "Infelizmente, devido às condições de saúde informadas, você não está apto para doar sangue no momento. Consulte um médico para mais informações.";
                } else {
                    //validação adicional de peso (mínimo 50kg para doação)
                    $peso = $_POST['peso'] ?? '';
                    if (!empty($peso) && $peso < 50) {
                        $erro = "Para doar sangue é necessário ter pelo menos 50kg. Seu peso atual não atende aos critérios mínimos para doação.";
                    }
                    
                    //se todas as validações passaram, processa dados normalmente <a href="https://clude.com.br/blog/quais-exames-de-sangue-mostram-o-tipo-sanguineo/" class="info">
                    if (!isset($erro)) {
                        $nome_completo   = $_POST['nome'] ?? '';
                        $telefone        = $_POST['telefone'] ?? '';
                        $cpf             = $_POST['cpf'] ?? '';
                        $rg              = $_POST['rg'] ?? '';
                        $sexo            = $_POST['genero'] ?? '';
                        $tipo_sanguineo  = $_POST['tipo_sang'] ?? '';
                        $ja_doou         = isset($_POST['doacao_anterior']) && $_POST['doacao_anterior'] === 'sim' ? 'Sim' : 'Não';  
                         $tempo_doacao    = $_POST['tempo_doacao'] ?? '';
                        $possui_doenca   = isset($_POST['doenca']) && $_POST['doenca'] === 'sim' ? 'Sim' : 'Não';

                        $doencas = '';
                        if (isset($_POST['doenca_nome']) && is_array($_POST['doenca_nome'])) {
                            $doencas = implode(', ', $_POST['doenca_nome']);
                        }

                        $cidade   = $_POST['cidade'] ?? '';
                        $estado   = $_POST['estado'] ?? '';
                        $endereco = $_POST['endereco'] ?? '';

                        $result = mysqli_query($conexao,
                            "INSERT INTO usuarios(nome_completo, senha, email, telefone, cpf, rg, sexo, data_nascimento, peso, tipo_sanguineo, ja_doou, tempo_ultima_doacao, possui_doenca, doencas, cidade, estado, endereco)
                             VALUES ('$nome_completo', '$senha', '$email', '$telefone', '$cpf', '$rg', '$sexo', '$data_nascimento', '$peso', '$tipo_sanguineo', '$ja_doou', '$tempo_doacao', '$possui_doenca', '$doencas', '$cidade', '$estado', '$endereco')");
                        
                        header('Location: login.php');
                    }
                }
            }
        }
    }
}
?>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Formulário para Doação</title>
    <style>
         body {
            margin: 0;
            padding: 20px 0;
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

        .box {
            margin: 0 auto;
            background-color: rgba(0, 0, 0, 0.6);
            padding: 15px;
            border-radius: 15px;
            width: 40%;
            color: white;
            position: relative;
            margin-top: 80px;
        }
        
        fieldset {
            border: 3px solid #CD5C5C;
        }

        legend {
            border: 1px solid #CD5C5C;
            padding: 10px;
            text-align: center;
            background-color: #CD5C5C;
            border-radius: 8px;
        }

        .inputBox {
            position: relative;
            margin-bottom: 10px;
            width: 100%;
        }

        .inputUser {
            background: none;
            border: none;
            border-bottom: 1px solid white;
            outline: none;
            color: white;
            font-size: 15px;
            width: 100%;
            letter-spacing: 2px;
        }

        .inputUser.invalid {
            border-bottom: 2px solid #ff4444;
        }

        .labelInput {
            position: absolute;
            top: 0px;
            left: 0px;
            pointer-events: none;
            transition: .5s;
        }

        .inputUser:focus ~ .labelInput,
        .inputUser:valid ~ .labelInput {
            top: -20px;
            font-size: 12px;
            color: #CD5C5C;
        }

        .inputUser.invalid:focus ~ .labelInput,
        .inputUser.invalid ~ .labelInput {
            color: #ff4444;
        }

        #data_nascimento {
            border: none;
            padding: 8px;
            border-radius: 10px;
            outline: none;
            font-size: 15px;
        }

        #submit {
            background-color: #CD5C5C;
            padding: 15px;
            width: 100%;
            border-radius: 1000px;
            color: white;
            font-size: 15px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            transform: perspective(1000px) rotateX(0deg);
            border: none;
            outline: none;
        }

        #submit::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s ease;
        }

        #submit:hover::before {
            left: 100%;
        }

        #submit:hover {
            background-color: #d62828;
            transform: perspective(1000px) rotateX(-8deg) translateY(-3px) scale(1.02);
            box-shadow: 0 15px 35px rgba(231, 72, 104, 0.4), 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        #submit:active {
            transform: perspective(1000px) rotateX(3deg) translateY(1px) scale(0.98);
            box-shadow: 0 5px 15px rgba(231, 72, 104, 0.3);
        }

        .row-container {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .row-container .inputBox {
            flex: 1;
            min-width: 48%;
        }
        .voltar {
            position: fixed;  
            top: 13px;           
            left: 20px;           
            background-color: #CD5C5C;
            color: white;
            border-radius: 1000px;
            padding: 10px 20px;   
            text-decoration: none;
            width: auto;         
            display: inline-block;
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            position: fixed;
            overflow: hidden;
            z-index: 1000;
        }

        .voltar::before {
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

        .voltar:hover::before {
            width: 300px;
            height: 300px;
        }

        .voltar:hover {
            background-color: rgb(220, 20, 60);
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 10px 25px rgba(220, 20, 60, 0.3);
        }

        .voltar:active {
            transform: translateY(-1px) scale(1.02);
        }

        .erro-message {
            background-color: #ff4444;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .doenca-checkbox {
            opacity: 0.5;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .doenca-checkbox.enabled {
            opacity: 1;
            pointer-events: auto;
        }

        .email-error {
            color: #ff4444;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }

       .linha-doacao {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-top: 10px;
    }

    .pergunta-doacao {
        min-width: 10px;
    }

    .tempo-doacao-box {
        display: none;
    }

    .tempo-doacao-box select {
        padding: 5px;
        width: 190px;
        max-width: 100%;
    }
    </style>
</head>
<body>
    <a href="home.php" class="voltar">Voltar</a>
    <div class="box">
        <form action="formulario.php" method="POST">
            <fieldset>
                <legend><b>Formulário de Doador</b></legend>
                <br>

                <?php if (isset($erro)): ?>
                    <div class="erro-message">
                        <?php echo $erro; ?>
                    </div>
                <?php endif; ?>

               <div class="row-container">
                <div class="inputBox">
                    <input type="text" name="nome" id="nome" class="inputUser" required>
                    <label for="nome" class="labelInput">Nome completo</label>
                    </div>

                    <div class="inputBox">
                    <input type="password" name="senha" id="senha" class="inputUser" required>
                    <label for="senha" class="labelInput">Senha</label>
                        </div>
                    </div>
                    <br>
                <div class="row-container">
                    <div class="inputBox">
                        <input type="email" name="email" id="email" class="inputUser" required>
                        <label for="email" class="labelInput">Email</label>
                        <div class="email-error" id="email-error">Por favor, insira um email válido (exemplo@dominio.com)</div>
                    </div>
                    <div class="inputBox">
                        <input type="password" name="confirmar_senha" id="confirmar_senha" class="inputUser" required>
                        <label for="confirmar_senha" class="labelInput">Confirmar Senha</label>
                    </div>
                </div>
                <br>
                <div class="row-container">
                    <div class="inputBox">
                    <input type="tel" name="telefone" id="telefone" class="inputUser" placeholder=" " required>
                    <label for="telefone" class="labelInput">Telefone</label>
                    </div>
                    <div class="inputBox">
                        <input type="text" name="cpf" id="cpf" class="inputUser" required>
                        <label for="cpf" class="labelInput">CPF</label>
                    </div>
                </div>
                <br>
                <div class="row-container">
                    <div class="inputBox">
                        <input type="text" name="rg" id="rg" class="inputUser" required>
                        <label for="rg" class="labelInput">RG</label>
                    </div>
                </div>
                <br>
                <p>Sexo:</p>
                <input type="radio" id="masculino" name="genero" value="masculino" required>
                <label for="masculino">Masculino</label><br>
                <input type="radio" id="feminino" name="genero" value="feminino" required>
                <label for="feminino">Feminino</label><br>
                <input type="radio" id="outro" name="genero" value="outro" required>
                <label for="outro">Outro</label><br><br>

                <label for="data_nascimento"><b>Data de Nascimento:</b></label>
                <input type="date" name="data_nascimento" id="data_nascimento" required>
                <br><br>

                <div class="inputBox">
                    <input type="number" name="peso" id="peso" class="inputUser" min="30" max="200" required>
                    <label for="peso" class="labelInput">Peso em Kg (mínimo 50kg)</label>
                </div>

                <br>
            
                <label for="tipo_sang"><b>Tipo Sanguíneo</b></label>
                <select name="tipo_sang" required>
                    <option value="">Selecione</option>
                    <option value="N/D">N/D</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                </select>
                <br><br>

                

                <div class="linha-doacao">
                <div class="pergunta-doacao">
                    <label><b>Já doou sangue antes?</b></label><br>
                    <label><input type="radio" name="doacao_anterior" value="sim" required onclick="mostrarSelect(true)"> Sim</label>
                    <label><input type="radio" name="doacao_anterior" value="nao" required onclick="mostrarSelect(false)"> Não</label>
                </div>

                <div id="tempo-doacao" class="tempo-doacao-box">
                    <label for="tempo_doacao"><b>Quanto tempo desde a última doação?</b></label><br>
                    <select name="tempo_doacao" id="tempo_doacao">
                    <option value="">Selecione uma opção</option>
                    <option value="menos 30">Menos de 30 dias</option>
                    <option value="menos 60">Menos de 60 dias</option>
                    <option value="60 dias">60 dias ou mais</option>
                    <option value="90 dias">90 dias ou mais</option>
                    </select>
                </div>
                </div>



                <label><b>Possui alguma doença?</b></label><br>
                <label><input type="radio" name="doenca" value="sim" id="doenca_sim" required> Sim</label>
                <label><input type="radio" name="doenca" value="nao" id="doenca_nao" required> Não</label>
                <br><br>

                <div id="doencas_container">
                    <label><b>Você possui alguma das condições abaixo?</b></label>
                    <br>
                    <label class="doenca-checkbox"><input type="checkbox" name="doenca_nome[]" value="HIV/AIDS"> HIV/AIDS</label><br>
                    <label class="doenca-checkbox"><input type="checkbox" name="doenca_nome[]" value="Hepatite B ou C"> Hepatite B ou C</label><br>
                    <label class="doenca-checkbox"><input type="checkbox" name="doenca_nome[]" value="Doença de Chagas"> Doença de Chagas</label><br>
                    <label class="doenca-checkbox"><input type="checkbox" name="doenca_nome[]" value="Sífilis"> Sífilis</label><br>
                    <label class="doenca-checkbox"><input type="checkbox" name="doenca_nome[]" value="HTLV"> HTLV</label><br>
                    <label class="doenca-checkbox"><input type="checkbox" name="doenca_nome[]" value="Malária"> Malária</label><br>
                    <label class="doenca-checkbox"><input type="checkbox" name="doenca_nome[]" value="Câncer"> Câncer</label><br>
                    <label class="doenca-checkbox"><input type="checkbox" name="doenca_nome[]" value="Tuberculose"> Tuberculose</label><br>
                    <label class="doenca-checkbox"><input type="checkbox" name="doenca_nome[]" value="Diabetes tipo 1"> Diabetes tipo 1 (uso de insulina)</label><br>
                    <label class="doenca-checkbox"><input type="checkbox" name="doenca_nome[]" value="Doença autoimune"> Doenças autoimunes graves</label><br><br>
                </div>

                <div class="row-container">
                    <div class="inputBox">
                        <input type="text" name="cidade" id="cidade" class="inputUser" required>
                        <label for="cidade" class="labelInput">Cidade</label>
                    </div>
                    <div class="inputBox">
                        <input type="text" name="estado" id="estado" class="inputUser" maxlength="2" required>
                        <label for="estado" class="labelInput">Estado</label>
                    </div>
                </div>
                <br>
                <div class="inputBox">
                    <input type="text" name="endereco" id="endereco" class="inputUser" required>
                    <label for="endereco" class="labelInput">Endereço</label>
                </div>

                <br>
                <input type="submit" name="submit" id="submit">
            </fieldset>
        </form>
    </div>

<script>
        function validarEmail(email) {
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(email);
        }

        function mostrarErroEmail(mostrar) {
            const emailError = document.getElementById('email-error');
            const emailInput = document.getElementById('email');
            
            if (mostrar) {
                emailError.style.display = 'block';
                emailInput.classList.add('invalid');
            } else {
                emailError.style.display = 'none';
                emailInput.classList.remove('invalid');
            }
        }

        function toggleDoencasCheckboxes() {
            const doencaSim = document.getElementById('doenca_sim');
            const doencaNao = document.getElementById('doenca_nao');
            const checkboxes = document.querySelectorAll('.doenca-checkbox');
            
            if (doencaNao.checked) {
                checkboxes.forEach(label => {
                    label.classList.remove('enabled');
                    const checkbox = label.querySelector('input[type="checkbox"]');
                    checkbox.checked = false;
                });
            } else if (doencaSim.checked) {
                checkboxes.forEach(label => {
                    label.classList.add('enabled');
                });
            }
        }

        function validarIdade() {
            const dataNascimento = document.getElementById('data_nascimento').value;
            if (dataNascimento) {
                const hoje = new Date();
                const nascimento = new Date(dataNascimento);
                const idade = hoje.getFullYear() - nascimento.getFullYear();
                const mes = hoje.getMonth() - nascimento.getMonth();
                
                const idadeReal = (mes < 0 || (mes === 0 && hoje.getDate() < nascimento.getDate())) ? idade - 1 : idade;
                
                if (idadeReal < 16 || idadeReal > 69) {
                    let mensagem = '';
                    if (idadeReal < 16) {
                        mensagem = `Você tem ${idadeReal} anos. Para doar sangue é necessário ter entre 16 e 69 anos.`;
                    } else {
                        mensagem = `Você tem ${idadeReal} anos. A idade máxima para doação de sangue é 69 anos.`;
                    }
                    alert(mensagem);
                    return false;
                }
            }
            return true;
        }

        function validarPeso() {
            const peso = document.getElementById('peso').value;
            if (peso && peso < 50) {
                alert('Para doar sangue é necessário ter pelo menos 50kg.');
                return false;
            }
            return true;
        }

        function configurarLimitesIdade() {
            const hoje = new Date();
            
            const dataMinima = new Date();
            dataMinima.setFullYear(hoje.getFullYear() - 69);
            
            const dataMaxima = new Date();
            dataMaxima.setFullYear(hoje.getFullYear() - 16);
            
            const dataMin = dataMinima.toISOString().split('T')[0];
            const dataMax = dataMaxima.toISOString().split('T')[0];
            
            const inputData = document.getElementById('data_nascimento');
            inputData.setAttribute('min', dataMin);
            inputData.setAttribute('max', dataMax);
        }

        document.getElementById('doenca_sim').addEventListener('change', toggleDoencasCheckboxes);
        document.getElementById('doenca_nao').addEventListener('change', toggleDoencasCheckboxes);
        document.getElementById('data_nascimento').addEventListener('change', validarIdade);
        document.getElementById('peso').addEventListener('blur', validarPeso);

        document.getElementById('email').addEventListener('input', function() {
            const email = this.value;
            if (email.length > 0) {
                if (!validarEmail(email)) {
                    mostrarErroEmail(true);
                } else {
                    mostrarErroEmail(false);
                }
            } else {
                mostrarErroEmail(false);
            }
        });

        document.getElementById('email').addEventListener('blur', function() {
            const email = this.value;
            if (email.length > 0 && !validarEmail(email)) {
                mostrarErroEmail(true);
            }
        });

        document.querySelector('form').addEventListener('submit', function(e) {
            const senha = document.getElementById('senha').value;
            const confirmarSenha = document.getElementById('confirmar_senha').value;
            const email = document.getElementById('email').value;
            
            if (!validarEmail(email)) {
                e.preventDefault();
                mostrarErroEmail(true);
                alert('Por favor, insira um endereço de email válido.');
                return false;
            }
            
            if (senha !== confirmarSenha) {
                e.preventDefault();
                alert('As senhas não coincidem. Por favor, verifique e tente novamente.');
                return false;
            }

            if (!validarIdade()) {
                e.preventDefault();
                return false;
            }

            if (!validarPeso()) {
                e.preventDefault();
                return false;
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            toggleDoencasCheckboxes();
            configurarLimitesIdade();
        });

        function phoneMask(value) {
            value = value.replace(/\D/g, '');
            
            if (value.length <= 2) {
                return `(${value}`;
            } else if (value.length <= 7) {
                return `(${value.slice(0, 2)}) ${value.slice(2)}`;
            } else if (value.length <= 11) {
                return `(${value.slice(0, 2)}) ${value.slice(2, 7)}-${value.slice(7)}`;
            } else {
                value = value.slice(0, 11);
                return `(${value.slice(0, 2)}) ${value.slice(2, 7)}-${value.slice(7)}`;
            }
        }

        const phoneInput = document.getElementById('telefone');

        phoneInput.addEventListener('input', function(e) {
            const cursorPosition = e.target.selectionStart;
            const oldValue = e.target.value;
            const newValue = phoneMask(e.target.value);
            
            e.target.value = newValue;
            
            if (newValue.length > oldValue.length) {
                e.target.setSelectionRange(cursorPosition + 1, cursorPosition + 1);
            } else if (newValue.length < oldValue.length) {
                e.target.setSelectionRange(cursorPosition - 1, cursorPosition - 1);
            } else {
                e.target.setSelectionRange(cursorPosition, cursorPosition);
            }
        });

        phoneInput.addEventListener('keypress', function(e) {
            const allowedChars = /[0-9()\s-]/;
            if (!allowedChars.test(e.key) && !['Backspace', 'Delete', 'Tab', 'ArrowLeft', 'ArrowRight'].includes(e.key)) {
                e.preventDefault();
            }
        });

        function cpfMask(value) {
            value = value.replace(/\D/g, '');
            
            if (value.length <= 3) {
                return value;
            } else if (value.length <= 6) {
                return `${value.slice(0, 3)}.${value.slice(3)}`;
            } else if (value.length <= 9) {
                return `${value.slice(0, 3)}.${value.slice(3, 6)}.${value.slice(6)}`;
            } else {
                value = value.slice(0, 11);
                return `${value.slice(0, 3)}.${value.slice(3, 6)}.${value.slice(6, 9)}-${value.slice(9)}`;
            }
        }

        const cpfInput = document.getElementById('cpf');

        cpfInput.addEventListener('input', function(e) {
            const cursorPosition = e.target.selectionStart;
            const oldValue = e.target.value;
            const newValue = cpfMask(e.target.value);
            
            e.target.value = newValue;
            
            if (newValue.length > oldValue.length) {
                e.target.setSelectionRange(cursorPosition + 1, cursorPosition + 1);
            } else if (newValue.length < oldValue.length) {
                e.target.setSelectionRange(cursorPosition - 1, cursorPosition - 1);
            } else {
                e.target.setSelectionRange(cursorPosition, cursorPosition);
            }
        });

        cpfInput.addEventListener('keypress', function(e) {
            const allowedChars = /[0-9.\-]/;
            if (!allowedChars.test(e.key) && !['Backspace', 'Delete', 'Tab', 'ArrowLeft', 'ArrowRight'].includes(e.key)) {
                e.preventDefault();
            }
        });

        function mostrarSelect(mostrar) {
            const div = document.getElementById("tempo-doacao");
            div.style.display = mostrar ? "block" : "none";
        }
    </script>
</body>
</html>