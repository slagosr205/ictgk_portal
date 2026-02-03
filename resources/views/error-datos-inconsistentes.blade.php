<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - Datos Inconsistentes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .error-container {
            max-width: 700px;
            width: 90%;
        }

        .error-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .error-header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            padding: 2.5rem;
            text-align: center;
            color: white;
        }

        .error-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }

        .error-body {
            padding: 2.5rem;
        }

        .error-title {
            color: #2c3e50;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .error-message {
            color: #34495e;
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 1.5rem;
            border-radius: 8px;
            margin: 1.5rem 0;
        }

        .info-box-title {
            font-weight: 600;
            color: #007bff;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-box-content {
            color: #495057;
            margin-bottom: 0;
        }

        .details-section {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }

        .details-title {
            color: #856404;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .details-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .details-list li {
            padding: 0.5rem 0;
            color: #856404;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-container {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .btn-custom {
            flex: 1;
            min-width: 150px;
            padding: 0.8rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary-custom {
            background: #6c757d;
            color: white;
        }

        .btn-secondary-custom:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(108, 117, 125, 0.4);
        }

        .id-badge {
            background: #e3f2fd;
            color: #1565c0;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-family: 'Courier New', monospace;
            font-weight: 600;
        }

        @media (max-width: 576px) {
            .error-header {
                padding: 2rem 1.5rem;
            }

            .error-body {
                padding: 1.5rem;
            }

            .error-title {
                font-size: 1.5rem;
            }

            .error-message {
                font-size: 1rem;
            }

            .btn-container {
                flex-direction: column;
            }

            .btn-custom {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-card">
            <div class="error-header">
                <i class="ri-error-warning-line error-icon"></i>
                <h1 class="mb-0">Datos Inconsistentes</h1>
            </div>

            <div class="error-body">
                <h2 class="error-title">
                    <i class="ri-alert-line text-warning"></i>
                    Se detectó un problema con los datos
                </h2>

                <p class="error-message">
                    {{ $mensaje ?? 'Se encontraron inconsistencias en los registros de la base de datos.' }}
                </p>

                <div class="info-box">
                    <div class="info-box-title">
                        <i class="ri-information-line"></i>
                        Identidad Consultada
                    </div>
                    <p class="info-box-content">
                        <span class="id-badge">{{ $identidad ?? 'N/A' }}</span>
                    </p>
                </div>

                <div class="details-section">
                    <div class="details-title">
                        <i class="ri-file-list-line"></i>
                        Detalles del problema
                    </div>
                    <ul class="details-list">
                        <li>
                            <i class="ri-checkbox-circle-line"></i>
                            <strong>Registros de ingreso encontrados:</strong> {{ $ingresos->count() ?? 0 }}
                        </li>
                        <li>
                            <i class="ri-close-circle-line"></i>
                            <strong>Información del candidato:</strong> No encontrada
                        </li>
                        <li>
                            <i class="ri-alert-line"></i>
                            <strong>Estado:</strong> Datos inconsistentes en la base de datos
                        </li>
                    </ul>
                </div>

                <div class="alert alert-info" role="alert">
                    <i class="ri-lightbulb-line me-2"></i>
                    <strong>¿Qué significa esto?</strong>
                    <p class="mb-0 mt-2">
                        {{ $detalles ?? 'Existen registros de ingresos laborales sin la información básica del candidato. Esto puede ocurrir cuando se eliminan o corrompen datos. Contacte con el administrador del sistema.' }}
                    </p>
                </div>

                <div class="btn-container">
                    <button onclick="window.history.back()" class="btn-custom btn-secondary-custom">
                        <i class="ri-arrow-left-line"></i>
                        Volver
                    </button>
                    <a href="mailto:portal.reclutamiento@altiabusinesspark.com?subject=Error de datos - ID: {{ $identidad }}&body=Se detectó un error de datos inconsistentes para la identidad: {{ $identidad }}"
                       class="btn-custom btn-primary-custom">
                        <i class="ri-mail-send-line"></i>
                        Reportar a RRHH
                    </a>
                </div>

                @if(isset($ingresos) && $ingresos->count() > 0)
                <div class="mt-4">
                    <details>
                        <summary class="text-muted" style="cursor: pointer;">
                            <i class="ri-code-line"></i> Ver información técnica
                        </summary>
                        <div class="mt-3 p-3 bg-light rounded">
                            <small class="text-muted">
                                <strong>IDs de registros afectados:</strong><br>
                                {{ $ingresos->pluck('id')->implode(', ') }}
                            </small>
                        </div>
                    </details>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
