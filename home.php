<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laços de Sangue</title>
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

        .hero {
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
            background: radial-gradient(circle at center, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        }

        .adm-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            z-index: 1000;
            animation: slideInLeft 1s ease-out;
        }

        .adm-btn:hover {
            background: rgba(220, 20, 60, 0.9);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(220, 20, 60, 0.3);
        }

        .mensagem-bemvindo {
            margin-bottom: 50px;
            animation: fadeInUp 1.2s ease-out;
        }

        .mensagem-bemvindo h1 {
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: bold;
            color: white;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.3);
            margin-bottom: 20px;
            letter-spacing: 2px;
        }

        .mensagem-bemvindo p {
            font-size: clamp(1.1rem, 2.5vw, 1.4rem);
            color: rgba(255, 255, 255, 0.9);
            font-style: italic;
            text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            line-height: 1.6;
        }

        .box {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: center;
            animation: fadeInUp 1.5s ease-out;
        }

        .box a {
            text-decoration: none;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.2);
            padding: 18px 35px;
            border-radius: 50px;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            min-width: 160px;
            text-align: center;
        }

        .box a::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .box a:hover::before {
            left: 100%;
        }

        .box a:hover {
             background-color: crimson;
                transform: perspective(1000px) rotateX(-10deg) translateY(-5px) scale(1.02);
                box-shadow: 0 15px 35px rgba(220, 20, 60, 0.4), 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .content-section {
            min-height: 100vh;
            padding: 80px 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transform: translateY(50px);
            transition: all 0.8s ease-out;
        }

        .content-section.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .content-container {
            max-width: 1200px;
            background: rgba(255, 255, 255, 0.95);
            padding: 60px;
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .content-container h2 {
            font-size: clamp(2rem, 4vw, 3rem);
            color: #d63031;
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }

        .content-container h2::after {
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

        .content-container p {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #333;
            margin-bottom: 25px;
            text-align: justify;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin: 40px 0;
        }

        .stat-card {
            background: linear-gradient(135deg, #ff4757, #ff3838);
            padding: 30px;
            border-radius: 20px;
            text-align: center;
            color: white;
            transform: scale(0.9);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: scale(1);
            box-shadow: 0 15px 35px rgba(255, 71, 87, 0.3);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            display: block;
            margin-bottom: 10px;
        }

        .requirements-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }

        .requirement-item {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 15px;
            border-left: 5px solid #d63031;
            transition: all 0.3s ease;
        }

        .requirement-item:hover {
            background: #fff;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transform: translateX(10px);
        }

        .heart-icon {
            color: #d63031;
            font-size: 1.5rem;
            margin-right: 10px;
        }

        .scroll-indicator {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            color: white;
            font-size: 2rem;
            animation: bounce 2s infinite;
            z-index: 100;
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

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0) translateX(-50%);
            }
            40% {
                transform: translateY(-10px) translateX(-50%);
            }
            60% {
                transform: translateY(-5px) translateX(-50%);
            }
        }

        @media (max-width: 768px) {
            .content-container {
                padding: 40px 30px;
                margin: 0 10px;
            }
            
            .box {
                flex-direction: column;
                align-items: center;
            }
            
            .box a {
                width: 80%;
                max-width: 300px;
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

    <section class="hero">
        <a href="loginadm.php" class="adm-btn">ADM</a>
        <div class="mensagem-bemvindo">
            <h1>Seja bem-vindo</h1>
            <p>Uma gota não faz o mar, mas pode mudar o mundo de alguém.</p>
        </div>
        <div class="box">
            <a href="login.php">Login</a>
            <a href="formulario.php">Cadastre-se</a>
        </div>
        <div class="scroll-indicator">↓</div>
    </section>

    <section class="content-section">
        <div class="content-container">
            <h2>❤️ Por que doar sangue?</h2>
            <p>Doar sangue é um dos gestos mais nobres que existem. Em apenas alguns minutos, você pode salvar até quatro vidas e se tornar parte de uma rede de solidariedade que transforma o mundo em um lugar melhor todos os dias.</p>
            <p>Cada doação é uma demonstração de amor ao próximo, um ato de cidadania que não conhece fronteiras sociais, econômicas ou culturais. Quando você doa sangue, está oferecendo esperança para quem mais precisa.</p>
            <div class="stats-grid">
                <div class="stat-card">
                    <span class="stat-number">1</span>
                    <div>doação pode salvar até 4 vidas</div>
                </div>
                <div class="stat-card">
                    <span class="stat-number">450ml</span>
                    <div>é o volume de uma doação</div>
                </div>
                <div class="stat-card">
                    <span class="stat-number">3%</span>
                    <div>da população brasileira doa sangue</div>
                </div>
            </div>
        </div>
    </section>

    <section class="content-section">
        <div class="content-container">
            <h2>🩸 A importância da doação de sangue</h2>
            <p>O sangue é um recurso que não pode ser fabricado artificialmente. Ele só pode ser obtido através da generosidade de doadores voluntários. Por isso, a doação de sangue é fundamental para:</p>
            <div class="requirements-list">
                <div class="requirement-item">
                    <span class="heart-icon">🏥</span>
                    <strong>Emergências médicas:</strong> Acidentes, cirurgias de emergência e traumas necessitam de sangue imediatamente disponível.
                </div>
                <div class="requirement-item">
                    <span class="heart-icon">🩺</span>
                    <strong>Tratamentos oncológicos:</strong> Pacientes com câncer frequentemente precisam de transfusões durante o tratamento.
                </div>
                <div class="requirement-item">
                    <span class="heart-icon">👶</span>
                    <strong>Partos complicados:</strong> Complicações durante o parto podem exigir transfusões para salvar mãe e bebê.
                </div>
                <div class="requirement-item">
                    <span class="heart-icon">😷</span>
                    <strong>Doenças crônicas:</strong> Pessoas com anemia, leucemia e outras condições dependem de doações regulares.
                </div>
            </div>
            <p>Nos bancos de sangue do Brasil, os estoques frequentemente ficam em níveis críticos, especialmente de tipos sanguíneos mais raros. Sua doação pode ser a diferença entre a vida e a morte para alguém.</p>
        </div>
    </section>

    <section class="content-section">
        <div class="content-container">
            <h2>✅ Quem pode doar sangue?</h2>
            <p>Para ser um doador de sangue, você precisa atender alguns critérios básicos de segurança, estabelecidos pelo Ministério da Saúde para proteger tanto o doador quanto o receptor:</p>
            <div class="requirements-list">
                <div class="requirement-item">
                    <span class="heart-icon">📅</span>
                    <strong>Idade:</strong> Entre 16 e 69 anos (menores de 18 precisam de autorização dos responsáveis).
                </div>
                <div class="requirement-item">
                    <span class="heart-icon">⚖️</span>
                    <strong>Peso:</strong> Mínimo de 50 kg.
                </div>
                <div class="requirement-item">
                    <span class="heart-icon">😴</span>
                    <strong>Descanso:</strong> Ter dormido pelo menos 6 horas nas últimas 24 horas.
                </div>
                <div class="requirement-item">
                    <span class="heart-icon">🍽️</span>
                    <strong>Alimentação:</strong> Estar alimentado, evitando alimentos gordurosos 4 horas antes.
                </div>
                <div class="requirement-item">
                    <span class="heart-icon">🆔</span>
                    <strong>Documentos:</strong> Apresentar documento oficial com foto.
                </div>
                <div class="requirement-item">
                    <span class="heart-icon">❤️</span>
                    <strong>Saúde:</strong> Estar em boas condições de saúde.
                </div>
            </div>
        </div>
    </section>

    <section class="content-section">
        <div class="content-container">
            <h2>🔄 Como funciona o processo?</h2>
            <p>O processo de doação de sangue é simples, seguro e supervisionado por profissionais especializados. Todo o procedimento leva cerca de 30 a 45 minutos:</p>
            <div class="requirements-list">
                <div class="requirement-item">
                    <span class="heart-icon">📋</span>
                    <strong>1. Cadastro:</strong> Preenchimento de formulário com informações pessoais e de saúde.
                </div>
                <div class="requirement-item">
                    <span class="heart-icon">👩‍⚕️</span>
                    <strong>2. Triagem clínica:</strong> Entrevista com profissional de saúde para avaliar condições para doação.
                </div>
                <div class="requirement-item">
                    <span class="heart-icon">🩸</span>
                    <strong>3. Teste de anemia:</strong> Verificação dos níveis de hemoglobina através de uma pequena amostra.
                </div>
                <div class="requirement-item">
                    <span class="heart-icon">💉</span>
                    <strong>4. Coleta:</strong> A doação propriamente dita, que dura de 8 a 12 minutos.
                </div>
                <div class="requirement-item">
                    <span class="heart-icon">🥤</span>
                    <strong>5. Lanche:</strong> Período de descanso com lanche para reposição de energia.
                </div>
                <div class="requirement-item">
                    <span class="heart-icon">🔬</span>
                    <strong>6. Testes laboratoriais:</strong> O sangue coletado passa por rigorosos testes de qualidade.
                </div>
            </div>
            <p>Todo material utilizado é descartável e esterilizado, não havendo qualquer risco de contaminação. Após a doação, seu organismo repõe naturalmente o volume doado em até 72 horas.</p>
        </div>
    </section>

    <script>
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.content-section').forEach(section => {
            observer.observe(section);
        });

        const heroSection = document.querySelector('.hero');
        const scrollIndicator = document.querySelector('.scroll-indicator');

        const heroObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) {
                    scrollIndicator.style.opacity = '0';
                } else {
                    scrollIndicator.style.opacity = '1';
                }
            });
        }, { threshold: 0.3 });

        heroObserver.observe(heroSection);

        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
</body>
</html>