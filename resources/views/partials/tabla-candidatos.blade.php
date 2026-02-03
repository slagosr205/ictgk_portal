<div class="table-responsive">
    <table class="table table-hover custom-table">
        <thead>
            <tr>
                <th>Identidad</th>
                <th>Nombre Completo</th>
                <th>Estado</th>
                <th>Comentarios</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $candidato)
            <tr>
                <td>
                    <strong>{{ $candidato->identidad }}</strong>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar-circle me-2">
                            {{ strtoupper(substr($candidato->nombre, 0, 1)) }}{{ strtoupper(substr($candidato->apellido ?? '', 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-semibold">{{ $candidato->nombre }} {{ $candidato->apellido ?? '' }}</div>
                            @if(isset($candidato->email))
                            <small class="text-muted">{{ $candidato->email }}</small>
                            @endif
                        </div>
                    </div>
                </td>
                <td>
                    @if(isset($candidato->activo_ingreso))
                        @if($candidato->activo_ingreso === 's')
                            <span class="badge badge-modern badge-success">
                                <i class="ri-checkbox-circle-line"></i>Activo
                            </span>
                        @else
                            <span class="badge badge-modern badge-danger">
                                <i class="ri-close-circle-line"></i>Inactivo
                            </span>
                        @endif
                    @else
                        <span class="badge bg-secondary">Sin estado</span>
                    @endif
                </td>
               <td>
                
    @if(isset($candidato->comentarios) && is_array($candidato->comentarios) && count($candidato->comentarios) > 0)
        @php
            // Filtrar y convertir comentarios a strings
            $comentariosString = collect($candidato->comentarios)
                ->filter(function($comentario) {
                    return !empty($comentario);
                })
                ->map(function($comentario) {
                    if (is_array($comentario)) {
                        return implode(' - ', array_filter($comentario));
                    }
                    return (string) $comentario;
                })
                ->take(3)
                ->implode(' | ');
        @endphp
        
        <button type="button" 
                class="btn btn-sm btn-outline-info" 
                data-bs-toggle="tooltip" 
                data-bs-placement="top"
                data-bs-html="false"
                title="{{ $comentariosString }}">
            <i class="ri-message-2-line me-1"></i>
            {{ count($candidato->comentarios) }} comentario(s)
        </button>
    @else
        <span class="text-muted">Sin comentarios</span>
    @endif
</td>
                <td class="text-center">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Ver detalle">
                            <i class="ri-eye-line"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="tooltip" title="Editar">
                            <i class="ri-edit-line"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Eliminar">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center py-5">
                    <div class="empty-state">
                        <i class="ri-inbox-line" style="font-size: 4rem; color: #dee2e6;"></i>
                        <h5 class="mt-3 text-muted">No se encontraron registros</h5>
                        <p class="text-muted">Intenta ajustar los filtros de búsqueda</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Información de paginación y controles -->
<div class="d-flex justify-content-between align-items-center custom-pagination">
    <div class="pagination-info">
        <i class="ri-file-list-line"></i>
        Mostrando <strong>{{ $data->firstItem() ?? 0 }}</strong> a <strong>{{ $data->lastItem() ?? 0 }}</strong> de <strong>{{ $data->total() }}</strong> registros
    </div>
    
    <nav aria-label="Navegación de página">
        {{ $data->links('pagination::bootstrap-5') }}
    </nav>
</div>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--primary-gradient);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.875rem;
}

.empty-state {
    padding: 3rem 0;
}

.btn-group .btn {
    border-radius: 8px !important;
    margin: 0 2px;
}
</style>