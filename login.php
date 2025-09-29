<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>
        <style>
            body{
                margin: 0;
                padding: 0;
                height: 100vh;
                font-family: Arial, Helvetica, sans-serif;
                background: linear-gradient(135deg, #ff4757 0%, #ff3838 50%, #ff6b7a 100%);
                background-repeat: no-repeat;
                background-size: cover;
                overflow-x: hidden;
                position: relative;
            }
            .login-container {
                background: rgba(0, 0, 0, 0.6);
                backdrop-filter: blur(10px);
                border: 2px solid rgba(0, 0, 0, 0.6);
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                padding: 50px;
                border-radius: 25px;
                color: white;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
                animation: fadeInUp 1.2s ease-out;
                z-index: 10;
            }
            input{
                padding: 15px;
                border: none;
                outline: none;
                font-size: 15px;
                border-radius: 15px;
            }
            .inputSubmit{
                background-color: #CD5C5C;
                border-color: black;
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
            }

            .inputSubmit::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
                transition: left 0.6s ease;
            }

            .inputSubmit:hover::before {
                left: 100%;
            }

            .inputSubmit:hover{
                background-color: crimson;
                transform: perspective(1000px) rotateX(-10deg) translateY(-5px) scale(1.02);
                box-shadow: 0 15px 35px rgba(220, 20, 60, 0.4), 0 5px 15px rgba(0, 0, 0, 0.3);
            }

            .inputSubmit:active {
                transform: perspective(1000px) rotateX(5deg) translateY(2px) scale(0.98);
                box-shadow: 0 5px 15px rgba(220, 20, 60, 0.3);
            }

            .titulo-login{
                font-size: 24px;
                margin-bottom: 20px;
                color: #ffcccb;
                font-weight: bold;
                text-shadow: 1px 1px 3px black;
            }
            .voltar {
                position: absolute;  
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
                position: relative;
                overflow: hidden;
                z-index: 20;
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
            @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translate(-50%, -30%) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1);
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

            @media (max-width: 768px) {
            .login-container {
                padding: 30px 25px;
                margin: 0 20px;
                width: calc(100% - 40px);
                left: 50%;
                transform: translate(-50%, -50%);
            }
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
    
    <a href="home.php" class="voltar">Voltar</a>
    
    <div class="login-container">
        <h1 class="titulo-login">Login</h1>
        <form action="testLogin.php" method="POST">
            <input type="text" name="email" placeholder="Email">
            <br><br>
            <input type="password" name="senha" placeholder="Senha">
            <br><br>
            <input class="inputSubmit" type="submit" name="submit" value="Entrar">
        </form>
    </div>
</body>
</html>