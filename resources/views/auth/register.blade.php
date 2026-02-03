@extends('layouts.app')

@section('content')
@php
    $isAdmin = auth()->check() && auth()->user()->perfil_id === 1;
@endphp

<div class="register-page">
    <div class="register-hero">
        <div class="hero-icon">
            <i class="ri-user-add-line"></i>
        </div>
        <div class="hero-text">
            <h2>Registrar Nuevo Usuario</h2>
            <p>Gestiona usuarios, empresas y permisos desde un solo lugar.</p>
        </div>
    </div>

    <div class="register-grid">
        <div class="register-card">
            <div class="card-header">
                <h3>Crear Usuario</h3>
                <p>Completa los datos para registrar un nuevo acceso.</p>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('register2') }}" class="modern-form">
                    @csrf

                    <div class="form-row">
                        <label for="name" class="form-label">{{ __('Nombre') }}</label>
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-row">
                        <label for="email" class="form-label">{{ __('Correo electronico') }}</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-row two-cols">
                        <div>
                            <label for="password" class="form-label">{{ __('Clave') }}</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div>
                            <label for="password-confirm" class="form-label">{{ __('Confirme Clave') }}</label>
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                        </div>
                    </div>

                    <div class="form-row">
                        <label for="empresas" class="form-label">{{ __('Seleccione Empresa:') }}</label>
                        <div class="input-group">
                            <select name="empresas" id="empresas" class="form-control">
                                <option value=""><------------></option>
                                @foreach ($empresas as $empresa)
                                    <option value="{{$empresa['id']}}">{{$empresa['nombre']}}</option>
                                @endforeach
                            </select>
                            <button class="btn-icon" type="button" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                                <i class="ri-add-line"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-row">
                        <label for="rol" class="form-label">Seleccione un rol</label>
                        <select name="rol" id="rol" class="form-control">
                            <option value=""><------------></option>
                            @foreach ($roles as $rol)
                                <option value="{{$rol['id']}}">{{$rol['perfilesdescrip']}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-modern btn-primary">
                            <i class="ri-user-add-line"></i>
                            <span>Registrar</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="register-card info-card">
            <div class="card-header">
                <h3>Consejos Rápidos</h3>
                <p>Recomendaciones para una gestión más segura.</p>
            </div>
            <div class="card-body">
                <ul class="tips-list">
                    <li>Usa correos corporativos para facilitar el acceso.</li>
                    <li>Asigna perfiles con el mínimo privilegio necesario.</li>
                    <li>Actualiza contraseñas si detectas accesos inusuales.</li>
                </ul>
            </div>
        </div>
    </div>

    <x-modal-empresas />

    <div class="users-card">
        <div class="card-header">
            <h3>Usuarios Registrados</h3>
            <p>Controla estado, empresa y perfil en un solo vistazo.</p>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ url()->current() }}" class="table-filters" id="usersFilterForm">
                <div class="filter-group">
                    <label for="search" class="form-label">Buscar</label>
                    <input type="text" id="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Nombre o correo">
                </div>
                <div class="filter-group">
                    <label for="empresa_id" class="form-label">Empresa</label>
                    <select id="empresa_id" name="empresa_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach ($empresas as $empresa)
                            <option value="{{$empresa['id']}}" {{ (string) request('empresa_id') === (string) $empresa['id'] ? 'selected' : '' }}>
                                {{$empresa['nombre']}}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label for="perfil_id" class="form-label">Perfil</label>
                    <select id="perfil_id" name="perfil_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach ($roles as $rol)
                            <option value="{{$rol['id']}}" {{ (string) request('perfil_id') === (string) $rol['id'] ? 'selected' : '' }}>
                                {{$rol['perfilesdescrip']}}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label for="status" class="form-label">Estado</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Activo</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="per_page" class="form-label">Registros</label>
                    <select id="per_page" name="per_page" class="form-select">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="15" {{ request('per_page') == 15 || !request('per_page') ? 'selected' : '' }}>15</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn-modern btn-primary">
                        <i class="ri-filter-2-line"></i>
                        <span>Filtrar</span>
                    </button>
                    <a href="{{ url()->current() }}" class="btn-modern btn-secondary">
                        <i class="ri-refresh-line"></i>
                        <span>Limpiar</span>
                    </a>
                </div>
            </form>
            <div class="table-wrapper" id="usersTableWrapper">
                <table class="modern-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuarios</th>
                        <th>Cuenta</th>
                        <th>Empresa</th>
                        <th>Perfil</th>
                        <th>Fecha de Creación</th>
                        <th>Fecha de Actualización</th>
                        <th>Estado</th>
                        @if ($isAdmin)
                            <th>Acciones</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($usuarios as $us)
                        <tr>
                            <td><span class="badge-id">{{$us->id}}</span></td>
                            <td>{{$us->name}}</td>
                            <td>{{$us->email}}</td>
                            <td>{{$us->nombre}}</td>
                            <td>{{$us->perfilesdescrip}}</td>
                            <td>{{\Carbon\Carbon::parse($us->created_at)->isoFormat('LL LTS')}}</td>
                            <td>{{\Carbon\Carbon::parse($us->updated_at)->isoFormat('LL LTS')}}</td>
                            <td>
                                <form id="update-status-form-{{ $us->id }}" action="{{ route('users.updateStatus', $us->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="form-select status-select" onchange="document.getElementById('update-status-form-{{ $us->id }}').submit();">
                                        <option value="1" {{ $us->status == 1 ? 'selected' : '' }}>Activo</option>
                                        <option value="0" {{ $us->status == 0 ? 'selected' : '' }}>Inactivo</option>
                                    </select>
                                </form>
                            </td>
                            @if ($isAdmin)
                                <td>
                                    <button type="button"
                                            class="btn-modern btn-secondary btn-change-password"
                                            data-bs-toggle="modal"
                                            data-bs-target="#passwordModal"
                                            data-user-id="{{$us->id}}"
                                            data-user-name="{{$us->name}}">
                                        <i class="ri-key-2-line"></i>
                                        <span>Cambiar</span>
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="table-scroll-actions">
                <button type="button" class="btn-scroll" id="usersScrollLeft" aria-label="Desplazar izquierda">
                    <i class="ri-arrow-left-s-line"></i>
                </button>
                <button type="button" class="btn-scroll" id="usersScrollRight" aria-label="Desplazar derecha">
                    <i class="ri-arrow-right-s-line"></i>
                </button>
            </div>
            <div class="pagination-container">
                <div class="pagination-info">
                    Mostrando <strong>{{$usuarios->firstItem() ?? 0}}</strong> a <strong>{{$usuarios->lastItem() ?? 0}}</strong> de <strong>{{$usuarios->total()}}</strong> registros
                </div>
                <div class="pagination-controls">
                    {{ $usuarios->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    @if (session('registro'))
        <div class="alert-card success">
            <i class="ri-check-line"></i>
            <strong>{{session('registro')}}</strong>
        </div>
    @endif
    @if (session('success'))
        <div class="alert-card success">
            <i class="ri-check-line"></i>
            <strong>{{session('success')}}</strong>
        </div>
    @endif
</div>

@if ($isAdmin)
<div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content password-modal">
            <div class="modal-header password-modal-header">
                <div class="password-header-info">
                    <div class="password-icon">
                        <i class="ri-key-2-line"></i>
                    </div>
                    <div>
                        <h5 class="modal-title" id="passwordModalLabel">Cambiar Contraseña</h5>
                        <p class="password-subtitle" id="passwordModalUser">Usuario seleccionado</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" class="password-form" data-action-base="{{ url('/users') }}">
                @csrf
                @method('PATCH')
                <div class="modal-body password-modal-body">
                    <div class="form-row">
                        <label for="new-password" class="form-label">Nueva contraseña</label>
                        <input id="new-password" type="password" name="password" class="form-control" required minlength="8" autocomplete="new-password">
                    </div>
                    <div class="form-row">
                        <label for="new-password-confirm" class="form-label">Confirmar contraseña</label>
                        <input id="new-password-confirm" type="password" name="password_confirmation" class="form-control" required minlength="8" autocomplete="new-password">
                    </div>
                </div>
                <div class="modal-footer password-modal-footer">
                    <button type="button" class="btn-modern btn-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line"></i>
                        <span>Cancelar</span>
                    </button>
                    <button type="submit" class="btn-modern btn-primary">
                        <i class="ri-save-3-line"></i>
                        <span>Guardar</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<style>
:root {
    --primary: #32C36C;
    --light: #F6F7F8;
    --dark: #1A2A36;
    --danger: #e74c3c;
    --success: #32C36C;
    --warning: #dce442;
    --text-primary: #2c3e50;
    --text-secondary: #6c757d;
    --border: #e1e8ed;
}

.register-page {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1.5rem 2rem;
}

.register-hero {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: linear-gradient(135deg, rgba(50, 195, 108, 0.12), rgba(26, 42, 54, 0.08));
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.hero-icon {
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

.hero-text h2 {
    margin: 0;
    font-size: 1.5rem;
    color: var(--text-primary);
}

.hero-text p {
    margin: 0.25rem 0 0;
    color: var(--text-secondary);
    font-size: 0.95rem;
}

.register-grid {
    display: grid;
    grid-template-columns: minmax(0, 2fr) minmax(0, 1fr);
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.register-card,
.users-card {
    background: white;
    border: 1px solid var(--border);
    border-radius: 16px;
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
    overflow: hidden;
}

.card-header {
    padding: 1.25rem 1.5rem;
    background: var(--dark);
    color: white;
}

.card-header h3 {
    margin: 0;
    font-size: 1.125rem;
}

.card-header p {
    margin: 0.35rem 0 0;
    opacity: 0.85;
    font-size: 0.9rem;
}

.card-body {
    padding: 1.5rem;
    background: var(--light);
}

.modern-form .form-row {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.modern-form .two-cols {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1rem;
}

.form-label {
    font-weight: 600;
    font-size: 0.875rem;
    color: var(--text-primary);
}

.form-control,
.form-select {
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 0.65rem 0.85rem;
    font-size: 0.9rem;
    background: white;
    transition: all 0.2s ease;
}

.form-control:focus,
.form-select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(50, 195, 108, 0.12);
}

.input-group {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.btn-icon {
    border: 1px dashed var(--border);
    background: white;
    color: var(--text-primary);
    border-radius: 8px;
    width: 40px;
    height: 40px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.btn-icon:hover {
    border-color: var(--primary);
    color: var(--primary);
    background: rgba(50, 195, 108, 0.08);
}

.form-actions {
    display: flex;
    justify-content: flex-start;
    margin-top: 0.5rem;
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
    background: rgba(50, 195, 108, 0.08);
}

.tips-list {
    padding-left: 1rem;
    color: var(--text-secondary);
}

.tips-list li {
    margin-bottom: 0.5rem;
}

.users-card .card-body {
    background: white;
}

.table-filters {
    display: grid;
    grid-template-columns: repeat(6, minmax(0, 1fr));
    gap: 1rem;
    padding: 1rem;
    margin-bottom: 1rem;
    background: var(--light);
    border: 1px solid var(--border);
    border-radius: 12px;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}

.filter-actions {
    display: flex;
    align-items: flex-end;
    gap: 0.75rem;
    grid-column: 1 / -1;
    justify-content: flex-end;
}

.table-wrapper {
    overflow-x: auto;
}

.modern-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 900px;
}

.modern-table thead {
    background: var(--dark);
    color: white;
}

.modern-table th,
.modern-table td {
    padding: 0.85rem 1rem;
    text-align: left;
    font-size: 0.85rem;
}

.modern-table tbody tr {
    border-bottom: 1px solid var(--border);
}

.modern-table tbody tr:hover {
    background: var(--light);
}

.badge-id {
    display: inline-block;
    padding: 0.2rem 0.6rem;
    background: rgba(50, 195, 108, 0.12);
    color: var(--primary);
    border-radius: 999px;
    font-weight: 600;
    font-size: 0.75rem;
}

.status-select {
    min-width: 120px;
}

.alert-card {
    margin-top: 1rem;
    padding: 0.85rem 1rem;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(50, 195, 108, 0.12);
    color: var(--text-primary);
    border: 1px solid rgba(50, 195, 108, 0.3);
}

.alert-card.success i {
    color: var(--success);
}

.table-scroll-actions {
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
    margin-top: 0.75rem;
}

.btn-scroll {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    border: 1px solid var(--border);
    background: white;
    color: var(--text-primary);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.btn-scroll:hover {
    border-color: var(--primary);
    color: var(--primary);
    background: rgba(50, 195, 108, 0.08);
}

.pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1rem;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.pagination-info {
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.pagination-controls .pagination {
    margin: 0;
}

.password-modal {
    border: none;
    border-radius: 14px;
    overflow: hidden;
}

.password-modal-header {
    background: var(--dark);
    color: white;
    padding: 1.25rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.password-header-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.password-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    background: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.password-subtitle {
    margin: 0.25rem 0 0;
    font-size: 0.85rem;
    opacity: 0.85;
}

.password-modal-body {
    padding: 1.25rem 1.5rem;
    background: var(--light);
}

.password-modal-footer {
    border-top: 1px solid var(--border);
    padding: 1rem 1.5rem;
    background: white;
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
}

@media (max-width: 992px) {
    .register-grid {
        grid-template-columns: 1fr;
    }

    .table-filters {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 768px) {
    .modern-form .two-cols {
        grid-template-columns: 1fr;
    }

    .table-filters {
        grid-template-columns: 1fr;
    }

    .filter-actions {
        align-items: stretch;
        flex-direction: column;
    }
}
</style>

@if ($isAdmin)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('passwordModal');
    const userLabel = document.getElementById('passwordModalUser');
    const form = document.querySelector('#passwordModal .password-form');

    if (!modal || !userLabel || !form) {
        return;
    }

    document.querySelectorAll('.btn-change-password').forEach(function (button) {
        button.addEventListener('click', function () {
            const userId = button.getAttribute('data-user-id');
            const userName = button.getAttribute('data-user-name');
            const base = form.getAttribute('data-action-base');
            form.action = base + '/' + userId + '/update-password';
            userLabel.textContent = 'Usuario: ' + userName;
        });
    });
});
</script>
@endif

<script>
document.addEventListener('DOMContentLoaded', function () {
    const wrapper = document.getElementById('usersTableWrapper');
    const btnLeft = document.getElementById('usersScrollLeft');
    const btnRight = document.getElementById('usersScrollRight');

    if (!wrapper || !btnLeft || !btnRight) {
        return;
    }

    const scrollByAmount = 240;
    btnLeft.addEventListener('click', function () {
        wrapper.scrollBy({ left: -scrollByAmount, behavior: 'smooth' });
    });
    btnRight.addEventListener('click', function () {
        wrapper.scrollBy({ left: scrollByAmount, behavior: 'smooth' });
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterForm = document.getElementById('usersFilterForm');
    if (!filterForm) {
        return;
    }

    const searchInput = filterForm.querySelector('#search');
    const selects = filterForm.querySelectorAll('select');

    if (searchInput) {
        searchInput.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                filterForm.submit();
            }
        });
    }

    selects.forEach(function (select) {
        select.addEventListener('change', function () {
            filterForm.submit();
        });
    });
});
</script>

@endsection
