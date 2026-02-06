<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal HHRR</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Gilroy', 'Nunito', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #072132 0%, #0a2e45 50%, #072132 100%);
            overflow: hidden;
            position: relative;
        }

        /* Particulas decorativas */
        body::before,
        body::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            opacity: 0.05;
        }

        body::before {
            width: 600px;
            height: 600px;
            background: #32C36C;
            top: -200px;
            right: -150px;
            animation: float 8s ease-in-out infinite;
        }

        body::after {
            width: 400px;
            height: 400px;
            background: #32C36C;
            bottom: -100px;
            left: -100px;
            animation: float 6s ease-in-out infinite reverse;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(5deg); }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        @keyframes iconBounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        .error-container {
            text-align: center;
            z-index: 10;
            animation: slideUp 0.8s ease-out;
            padding: 20px;
            max-width: 520px;
            width: 100%;
        }

        .error-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 50px 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .error-icon {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, #32C36C 0%, #28a85a 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: iconBounce 3s ease-in-out infinite;
            box-shadow: 0 8px 30px rgba(50, 195, 108, 0.3);
        }

        .error-icon i {
            font-size: 40px;
            color: #fff;
        }

        .error-code {
            font-size: 72px;
            font-weight: 800;
            color: #fff;
            line-height: 1;
            margin-bottom: 10px;
            letter-spacing: -2px;
        }

        .error-code span {
            color: #32C36C;
        }

        .error-title {
            font-size: 22px;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 15px;
        }

        .error-message {
            font-size: 15px;
            color: rgba(255, 255, 255, 0.5);
            line-height: 1.7;
            margin-bottom: 35px;
        }

        .error-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-primary-custom {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 28px;
            background: linear-gradient(135deg, #32C36C 0%, #28a85a 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(50, 195, 108, 0.3);
            font-family: inherit;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(50, 195, 108, 0.4);
        }

        .btn-secondary-custom {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 28px;
            background: rgba(255, 255, 255, 0.08);
            color: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        .btn-secondary-custom:hover {
            background: rgba(255, 255, 255, 0.12);
            transform: translateY(-2px);
        }

        .logo-container {
            margin-bottom: 30px;
        }

        .logo-container img {
            height: 40px;
            opacity: 0.7;
        }

        @media (max-width: 576px) {
            .error-card {
                padding: 35px 25px;
            }

            .error-code {
                font-size: 56px;
            }

            .error-title {
                font-size: 18px;
            }

            .error-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-card">
            <div class="logo-container">
                <img src="{{ asset('storage/altialogoblanco.png') }}" alt="Altia Logo">
            </div>

            <div class="error-icon">
                <i class="ri-server-line"></i>
            </div>

            <div class="error-code">5<span>0</span>0</div>

            <h1 class="error-title">Error interno del servidor</h1>

            <p class="error-message">
                Lo sentimos, algo salió mal en nuestro servidor.
                Nuestro equipo ha sido notificado y estamos trabajando para solucionarlo.
            </p>

            <div class="error-actions">
                <a href="{{ url('/') }}" class="btn-primary-custom">
                    <i class="ri-home-4-line"></i>
                    Ir al inicio
                </a>
                <button onclick="window.history.back()" class="btn-secondary-custom">
                    <i class="ri-arrow-left-line"></i>
                    Volver
                </button>
            </div>
        </div>
    </div>
</body>
</html>
