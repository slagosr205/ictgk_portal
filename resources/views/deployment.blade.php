@extends('layouts.app')

@section('deployment')
@php
    $isAdmin = auth()->check() && auth()->user()->perfil_id === 1;
@endphp

<div class="deployment-page" id="dtdeployment" data-is-admin="{{ $isAdmin }}">
    <div class="page-header">
        <div class="header-info">
            <div class="header-icon">
                <i class="ri-upload-cloud-line"></i>
            </div>
            <div>
                <h2>Deployment & Version Control</h2>
                <p>Gestiona versiones locales y despliega cambios al servidor FTP.</p>
            </div>
        </div>
        <div class="header-actions">
            <button class="btn-modern btn-primary" data-bs-toggle="modal" data-bs-target="#modalNewVersion">
                <i class="ri-add-line"></i>
                <span>Nueva versión</span>
            </button>
            <button class="btn-modern btn-secondary" id="refreshChanges">
                <i class="ri-refresh-line"></i>
                <span>Actualizar</span>
            </button>
        </div>
    </div>

    <div class="deployment-grid">
        <div class="deploy-card">
            <div class="card-header">
                <h3><i class="ri-settings-3-line"></i> Configuración FTP</h3>
                <div class="connection-status" id="connectionStatus">
                    @if($isConnected)
                    <span class="status-badge connected"><i class="ri-check-line"></i> Conectado</span>
                    @else
                    <span class="status-badge disconnected"><i class="ri-close-line"></i> No configurado</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <form id="ftpConfigForm" class="deploy-form">
                    <div class="form-row">
                        <div class="form-field">
                            <label for="ftp_host">Servidor FTP</label>
                            <input type="text" id="ftp_host" name="host" class="form-control" placeholder="ftp.ejemplo.com" value="{{ $ftpConfig['host'] ?? '' }}">
                        </div>
                        <div class="form-field small">
                            <label for="ftp_port">Puerto</label>
                            <input type="number" id="ftp_port" name="port" class="form-control" placeholder="21" value="{{ $ftpConfig['port'] ?? 21 }}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-field">
                            <label for="ftp_username">Usuario</label>
                            <input type="text" id="ftp_username" name="username" class="form-control" placeholder="usuario" value="{{ $ftpConfig['username'] ?? '' }}">
                        </div>
                        <div class="form-field">
                            <label for="ftp_password">Contraseña</label>
                            <input type="password" id="ftp_password" name="password" class="form-control" placeholder="••••••••" value="{{ $ftpConfig['password'] ?? '' }}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-field">
                            <label for="ftp_root">Directorio raíz</label>
                            <input type="text" id="ftp_root" name="root" class="form-control" placeholder="/public_html" value="{{ $ftpConfig['root'] ?? '/' }}">
                        </div>
                        <div class="form-field checkbox">
                            <label class="checkbox-label">
                                <input type="checkbox" name="pasv" {{ !empty($ftpConfig['pasv'] ?? true) ? 'checked' : '' }}>
                                <span>Modo pasivo</span>
                            </label>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-modern btn-secondary" id="testConnection">
                            <i class="ri-wireless-charging-line"></i>
                            <span>Probar conexión</span>
                        </button>
                        <button type="submit" class="btn-modern btn-primary">
                            <i class="ri-save-3-line"></i>
                            <span>Guardar</span>
                        </button>
                    </div>
                </form>
                <div id="ftpTestResult" class="test-result hidden"></div>
            </div>
        </div>

        <div class="deploy-card">
            <div class="card-header">
                <h3><i class="ri-github-line"></i> GitHub</h3>
                <div class="connection-status" id="githubStatus">
                    <span class="status-badge disconnected"><i class="ri-close-line"></i> No conectado</span>
                </div>
            </div>
            <div class="card-body">
                <form id="githubConfigForm" class="deploy-form">
                    <div class="form-row">
                        <div class="form-field">
                            <label for="github_token">Token de GitHub</label>
                            <input type="password" id="github_token" name="token" class="form-control" placeholder="ghp_xxxx...">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-field">
                            <label for="github_owner">Owner/Organización</label>
                            <input type="text" id="github_owner" name="owner" class="form-control" placeholder="mi-organizacion">
                        </div>
                        <div class="form-field">
                            <label for="github_repo">Repositorio</label>
                            <input type="text" id="github_repo" name="repo" class="form-control" placeholder="mi-proyecto">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-field">
                            <label for="github_branch">Rama</label>
                            <input type="text" id="github_branch" name="branch" class="form-control" placeholder="main" value="main">
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-modern btn-secondary" id="testGitHub">
                            <i class="ri-github-line"></i>
                            <span>Probar conexión</span>
                        </button>
                        <button type="submit" class="btn-modern btn-primary">
                            <i class="ri-save-3-line"></i>
                            <span>Guardar</span>
                        </button>
                    </div>
                </form>
                <div id="githubTestResult" class="test-result hidden"></div>
            </div>
        </div>

        <div class="deploy-card">
            <div class="card-header">
                <h3><i class="ri-file-list-3-line"></i> Cambios locales</h3>
                <span class="badge-count" id="changesCount">{{ count($localChanges) }}</span>
            </div>
            <div class="card-body">
                <div class="changes-toolbar">
                    <div class="search-box">
                        <i class="ri-search-line"></i>
                        <input type="text" id="searchChanges" placeholder="Buscar archivos...">
                    </div>
                    <div class="toolbar-actions">
                        <button class="btn-modern btn-secondary btn-sm" id="selectAllChanges">
                            <i class="ri-checkbox-multiple-line"></i>
                            <span>Seleccionar todo</span>
                        </button>
                        <button class="btn-modern btn-warning btn-sm" id="deploySelected" disabled>
                            <i class="ri-upload-cloud-line"></i>
                            <span>FTP Deploy</span>
                        </button>
                        <button class="btn-modern btn-primary btn-sm" id="pushToGitHub" disabled>
                            <i class="ri-github-line"></i>
                            <span>Push GitHub</span>
                        </button>
                        <button class="btn-modern btn-info btn-sm" id="pullFromGitHub" disabled>
                            <i class="ri-github-line"></i>
                            <span>Pull GitHub</span>
                        </button>
                    </div>
                </div>
                <div class="changes-list" id="changesList">
                    @foreach($localChanges as $change)
                    <div class="change-item" data-path="{{ $change['path'] }}" data-modified="{{ $change['modified'] }}">
                        <label class="checkbox-item">
                            <input type="checkbox" name="files[]" value="{{ $change['path'] }}">
                            <span class="file-info">
                                <i class="ri-file-code-line"></i>
                                <span class="filename">{{ $change['filename'] }}</span>
                                <span class="filepath">{{ $change['path'] }}</span>
                            </span>
                            <span class="file-meta">
                                <span class="filesize">{{ number_format($change['size'] / 1024, 1) }} KB</span>
                                <span class="filetime">{{ $change['modified'] }}</span>
                            </span>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="versions-section">
        <div class="section-header">
            <h3><i class="ri-history-line"></i> Historial de versiones</h3>
        </div>
        <div class="versions-grid" id="versionsGrid">
            @foreach($versions as $version)
            <div class="version-card" data-id="{{ $version['id'] }}">
                <div class="version-header">
                    <span class="version-name">{{ $version['name'] }}</span>
                    <span class="version-status {{ $version['status'] ?? 'local' }}">{{ $version['status'] ?? 'local' }}</span>
                </div>
                <div class="version-info">
                    <p class="version-desc">{{ $version['description'] ?? 'Sin descripción' }}</p>
                    <div class="version-meta">
                        <span><i class="ri-user-line"></i> {{ $version['creator'] }}</span>
                        <span><i class="ri-calendar-line"></i> {{ \Carbon\Carbon::parse($version['created_at'])->format('d/m/Y H:i') }}</span>
                        <span><i class="ri-file-count-line"></i> {{ count($version['files'] ?? []) }} archivos</span>
                    </div>
                </div>
                <div class="version-actions">
                    @if(($version['status'] ?? 'local') === 'success')
                    <button class="btn-modern btn-secondary btn-sm" disabled>
                        <i class="ri-check-line"></i>
                    </button>
                    @elseif(($version['status'] ?? 'local') === 'partial')
                    <button class="btn-modern btn-warning btn-sm" disabled>
                        <i class="ri-alert-line"></i>
                    </button>
                    @else
                    <button class="btn-modern btn-primary btn-sm btn-redeploy" data-id="{{ $version['id'] }}">
                        <i class="ri-upload-cloud-line"></i>
                    </button>
                    @endif
                    <button class="btn-modern btn-info btn-sm btn-view" data-id="{{ $version['id'] }}">
                        <i class="ri-eye-line"></i>
                    </button>
                    <button class="btn-modern btn-danger btn-sm btn-delete" data-id="{{ $version['id'] }}">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>
            </div>
            @endforeach

            @if(count($versions) === 0)
            <div class="empty-state">
                <i class="ri-history-line"></i>
                <p>No hay versiones guardadas</p>
                <span>Crea una versión para comenzar a rastrear tus cambios</span>
            </div>
            @endif
        </div>
    </div>

    <div class="deploy-progress hidden" id="deployProgress">
        <div class="progress-header">
            <h4>Desplegando archivos...</h4>
            <button class="btn-close" id="closeProgress">&times;</button>
        </div>
        <div class="progress-body">
            <div class="progress-info">
                <span id="progressText">0 / 0 archivos</span>
                <span id="progressPercent">0%</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill"></div>
            </div>
            <div class="progress-log" id="progressLog"></div>
        </div>
    </div>
</div>

    <div class="modal fade" id="modalNewVersion" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-modern">
                <div class="modal-header modal-header-modern">
                    <div class="modal-header-info">
                        <div class="modal-icon">
                            <i class="ri-add-circle-line"></i>
                        </div>
                        <div>
                            <h1 class="modal-title fs-5">Crear versión</h1>
                            <p>Guarda los archivos seleccionados como una nueva versión.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body modal-body-modern">
                    <form id="createVersionForm">
                        <div class="form-field">
                            <label for="versionName">Nombre de versión</label>
                            <input type="text" id="versionName" name="name" class="form-control" placeholder="v1.0.0">
                        </div>
                        <div class="form-field">
                            <label for="versionDesc">Descripción</label>
                            <textarea id="versionDesc" name="description" class="form-control" rows="3" placeholder="Describe los cambios de esta versión..."></textarea>
                        </div>
                        <div class="selected-files-info">
                            <span id="selectedFilesCount">0 archivos seleccionados</span>
                        </div>
                    </form>
                </div>
                <div class="modal-footer modal-footer-modern">
                    <button type="button" class="btn-modern btn-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line"></i>
                        <span>Cancelar</span>
                    </button>
                    <button type="submit" class="btn-modern btn-primary" form="createVersionForm" id="createVersionBtn">
                        <i class="ri-save-3-line"></i>
                        <span>Crear versión</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalViewVersion" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content modal-modern">
                <div class="modal-header modal-header-modern">
                    <div class="modal-header-info">
                        <div class="modal-icon">
                            <i class="ri-folder-info-line"></i>
                        </div>
                        <div>
                            <h1 class="modal-title fs-5" id="viewVersionTitle">Versión</h1>
                            <p id="viewVersionDesc">Detalles de la versión</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body modal-body-modern">
                    <div class="version-files-list" id="versionFilesList"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('deploymentjs')
<script>
window.DeploymentRoutes = {
    // GitHub
    githubConfig: '{{ route("deployment.githubConfig") }}',
    saveGitHubConfig: '{{ route("deployment.saveGitHubConfig") }}',
    testGitHub: '{{ route("deployment.testGitHub") }}',
    pushToGitHub: '{{ route("deployment.pushToGitHub") }}',
    pullFromGitHub: '{{ route("deployment.pullFromGitHub") }}',
    githubChanges: '{{ route("deployment.githubChanges") }}',
    
    // FTP & Versions
    saveConfig: '{{ route("deployment.saveConfig") }}',
    testConnection: '{{ route("deployment.testConnection") }}',
    scanChanges: '{{ route("deployment.scanChanges") }}',
    deploy: '{{ route("deployment.deploy") }}',
    createVersion: '{{ route("deployment.createVersion") }}'
};
window.csrfToken = '{{ csrf_token() }}';
</script>
    @vite(['resources/js/deployment.js'])
@stop

<style>

.deployment-page {
    max-width: 1400px;
    margin: 2rem auto;
    padding: 0 1.5rem 2rem;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1.5rem;
    background: linear-gradient(135deg, rgb(var(--brand-primary-rgb) / 0.12), rgb(var(--brand-dark-rgb) / 0.08));
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}

.header-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.header-icon {
    width: 54px;
    height: 54px;
    border-radius: 12px;
    background: var(--primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.header-info h2 {
    margin: 0;
    color: var(--text-primary);
    font-size: 1.5rem;
}

.header-info p {
    margin: 0.25rem 0 0;
    color: var(--text-secondary);
}

.header-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.btn-modern {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.6rem 1.1rem;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-modern.btn-primary {
    background: var(--primary);
    color: white;
}

.btn-modern.btn-primary:hover {
    background: #2aaa5e;
    transform: translateY(-1px);
}

.btn-modern.btn-secondary {
    background: white;
    color: var(--text-primary);
    border: 1px solid var(--border);
}

.btn-modern.btn-secondary:hover {
    border-color: var(--primary);
    color: var(--primary);
    background: rgb(var(--brand-primary-rgb) / 0.08);
}

.btn-modern.btn-warning {
    background: var(--warning);
    color: var(--dark);
}

.btn-modern.btn-warning:hover {
    background: #c9ce3a;
    transform: translateY(-1px);
}

.btn-modern.btn-info {
    background: #3498db;
    color: white;
}

.btn-modern.btn-danger {
    background: var(--danger);
    color: white;
}

.btn-modern.btn-sm {
    padding: 0.4rem 0.75rem;
    font-size: 0.8rem;
}

.deployment-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

@media (max-width: 992px) {
    .deployment-grid {
        grid-template-columns: 1fr;
    }
}

.deploy-card {
    background: white;
    border: 1px solid var(--border);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
}

.deploy-card .card-header {
    padding: 1.25rem 1.5rem;
    background: var(--dark);
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.deploy-card .card-header h3 {
    margin: 0;
    font-size: 1.125rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.deploy-card .card-body {
    padding: 1.5rem;
    background: white;
}

.connection-status .status-badge {
    padding: 0.3rem 0.75rem;
    border-radius: 999px;
    font-size: 0.8rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

.status-badge.connected {
    background: rgba(39, 174, 96, 0.15);
    color: #27ae60;
}

.status-badge.disconnected {
    background: rgba(231, 76, 60, 0.15);
    color: #e74c3c;
}

.badge-count {
    background: var(--primary);
    color: white;
    padding: 0.2rem 0.6rem;
    border-radius: 999px;
    font-size: 0.8rem;
    font-weight: 600;
}

.deploy-form .form-row {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 1rem;
    margin-bottom: 1rem;
}

.deploy-form .form-field {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}

.deploy-form .form-field.small {
    max-width: 100px;
}

.deploy-form .form-field.checkbox {
    flex-direction: row;
    align-items: center;
    padding-top: 1.5rem;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
}

.form-field label {
    font-weight: 600;
    font-size: 0.85rem;
    color: var(--text-primary);
}

.form-control {
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 0.65rem 0.85rem;
    font-size: 0.9rem;
    background: white;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgb(var(--brand-primary-rgb) / 0.12);
}

.form-actions {
    display: flex;
    gap: 0.75rem;
    justify-content: flex-end;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--border);
}

.test-result {
    margin-top: 1rem;
    padding: 0.75rem 1rem;
    border-radius: 8px;
    font-size: 0.9rem;
}

.test-result.success {
    background: rgba(39, 174, 96, 0.1);
    border: 1px solid rgba(39, 174, 96, 0.3);
    color: #27ae60;
}

.test-result.error {
    background: rgba(231, 76, 60, 0.1);
    border: 1px solid rgba(231, 76, 60, 0.3);
    color: #e74c3c;
}

.changes-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}

.search-box {
    position: relative;
    flex: 1;
    min-width: 200px;
}

.search-box i {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-secondary);
}

.search-box input {
    width: 100%;
    padding-left: 2.25rem;
}

.toolbar-actions {
    display: flex;
    gap: 0.5rem;
}

.changes-list {
    max-height: 400px;
    overflow-y: auto;
    border: 1px solid var(--border);
    border-radius: 8px;
}

.change-item {
    padding: 0.6rem 0.75rem;
    border-bottom: 1px solid var(--border);
    transition: background 0.15s ease;
}

.change-item:last-child {
    border-bottom: none;
}

.change-item:hover {
    background: var(--light);
}

.checkbox-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
}

.checkbox-item input[type="checkbox"] {
    width: 18px;
    height: 18px;
    accent-color: var(--primary);
}

.file-info {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    min-width: 0;
}

.file-info i {
    color: var(--primary);
}

.filename {
    font-weight: 600;
    font-size: 0.9rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.filepath {
    font-size: 0.8rem;
    color: var(--text-secondary);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.file-meta {
    display: flex;
    gap: 1rem;
    font-size: 0.8rem;
    color: var(--text-secondary);
}

.versions-section {
    background: white;
    border: 1px solid var(--border);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
}

.versions-section .section-header {
    padding: 1.25rem 1.5rem;
    background: var(--dark);
    color: white;
}

.versions-section .section-header h3 {
    margin: 0;
    font-size: 1.125rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.versions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1rem;
    padding: 1.5rem;
}

.version-card {
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 1rem;
    transition: all 0.2s ease;
}

.version-card:hover {
    border-color: var(--primary);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.version-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
}

.version-name {
    font-weight: 700;
    font-size: 1rem;
    color: var(--text-primary);
}

.version-status {
    padding: 0.2rem 0.5rem;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
}

.version-status.local {
    background: rgb(var(--brand-primary-rgb) / 0.12);
    color: var(--primary);
}

.version-status.success {
    background: rgba(39, 174, 96, 0.15);
    color: #27ae60;
}

.version-status.partial {
    background: rgba(243, 156, 18, 0.15);
    color: #f39c12;
}

.version-info {
    margin-bottom: 1rem;
}

.version-desc {
    margin: 0 0 0.5rem;
    font-size: 0.9rem;
    color: var(--text-secondary);
}

.version-meta {
    display: flex;
    gap: 1rem;
    font-size: 0.8rem;
    color: var(--text-secondary);
}

.version-meta span {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.version-actions {
    display: flex;
    gap: 0.5rem;
    justify-content: flex-end;
}

.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 3rem;
    color: var(--text-secondary);
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state p {
    font-size: 1.1rem;
    margin: 0 0 0.25rem;
}

.empty-state span {
    font-size: 0.9rem;
    opacity: 0.7;
}

.modal-modern {
    border: none;
    border-radius: 14px;
    overflow: hidden;
}

.modal-header-modern {
    background: var(--dark);
    color: white;
    padding: 1.25rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.modal-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    background: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
}

.modal-header-modern p {
    margin: 0.25rem 0 0;
    font-size: 0.85rem;
    opacity: 0.9;
}

.modal-body-modern {
    background: var(--light);
    padding: 1.5rem;
}

.modal-footer-modern {
    border-top: 1px solid var(--border);
    padding: 1rem 1.5rem;
    background: white;
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
}

.form-field {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
    margin-bottom: 1rem;
}

.form-field label {
    font-weight: 600;
    font-size: 0.85rem;
    color: var(--text-primary);
}

.selected-files-info {
    padding: 0.75rem;
    background: rgb(var(--brand-primary-rgb) / 0.08);
    border-radius: 8px;
    text-align: center;
    font-size: 0.9rem;
    color: var(--primary);
}

.version-files-list {
    max-height: 400px;
    overflow-y: auto;
}

.version-file-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem;
    border-bottom: 1px solid var(--border);
}

.version-file-item:last-child {
    border-bottom: none;
}

.version-file-item i {
    color: var(--primary);
}

.deploy-progress {
    position: fixed;
    bottom: 1rem;
    right: 1rem;
    width: 380px;
    background: white;
    border: 1px solid var(--border);
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    z-index: 1050;
}

.deploy-progress .progress-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid var(--border);
}

.deploy-progress .progress-header h4 {
    margin: 0;
    font-size: 0.95rem;
}

.deploy-progress .progress-header .btn-close {
    background: none;
    border: none;
    font-size: 1.25rem;
    cursor: pointer;
    color: var(--text-secondary);
}

.deploy-progress .progress-body {
    padding: 1rem;
}

.progress-info {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
    font-size: 0.85rem;
    color: var(--text-secondary);
}

.progress-bar {
    height: 8px;
    background: var(--light);
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 0.75rem;
}

.progress-fill {
    height: 100%;
    background: var(--primary);
    border-radius: 4px;
    transition: width 0.3s ease;
    width: 0%;
}

.progress-log {
    max-height: 120px;
    overflow-y: auto;
    font-size: 0.8rem;
    color: var(--text-secondary);
}

.progress-log .log-item {
    padding: 0.25rem 0;
}

.progress-log .log-item.success {
    color: #27ae60;
}

.progress-log .log-item.error {
    color: #e74c3c;
}

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .versions-grid {
        grid-template-columns: 1fr;
    }

    .deploy-progress {
        width: calc(100% - 2rem);
        left: 1rem;
    }
}
</style>

@endsection