@php
    use Jenssegers\Date\Date;
@endphp

<tbody id="seguridadTableBody">
    @forelse ($candidatos as $candidato)
        @php
            $comentariosRaw = $candidato->comentarios ?? null;
            $comentarios = [];
            if (is_string($comentariosRaw)) {
                $decoded = json_decode($comentariosRaw, true);
                $comentarios = is_array($decoded) ? $decoded : [];
            } elseif (is_array($comentariosRaw)) {
                $comentarios = $comentariosRaw;
            } elseif (is_object($comentariosRaw)) {
                $comentarios = (array) $comentariosRaw;
            }

            $eventos = collect($comentarios)->map(function ($item) {
                return is_object($item) ? (array) $item : (is_array($item) ? $item : null);
            })->filter(function ($item) {
                return is_array($item) && (isset($item['fechaBloqueo']) || isset($item['fechaDesbloqueo']) || isset($item['comentarios']));
            })->values();

            $ultimoEvento = $eventos->first();
            $motivo = is_array($ultimoEvento) ? ($ultimoEvento['comentarios'] ?? null) : null;
            $fechaBloqueo = is_array($ultimoEvento) ? ($ultimoEvento['fechaBloqueo'] ?? null) : null;
            $fechaBloqueoFmt = $fechaBloqueo ? (new Date($fechaBloqueo))->format('d/m/Y H:i') : null;
            $fechaNacRaw = $candidato->getRawOriginal('fecha_nacimiento');
        @endphp

        <tr>
            <td><strong>{{ $candidato->identidad }}</strong></td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-circle me-2">
                        {{ strtoupper(substr($candidato->nombre, 0, 1)) }}{{ strtoupper(substr($candidato->apellido ?? '', 0, 1)) }}
                    </div>
                    <div>
                        <div class="fw-semibold">{{ $candidato->nombre }} {{ $candidato->apellido ?? '' }}</div>
                    </div>
                </div>
            </td>
            <td>{{ $candidato->telefono ?? 'N/A' }}</td>
            <td>{{ $candidato->correo ?? 'N/A' }}</td>
            <td>
                @if($candidato->generoM_F == 'M')
                    <span class="badge bg-primary">Masculino</span>
                @elseif($candidato->generoM_F == 'F')
                    <span class="badge bg-pink">Femenino</span>
                @else
                    <span class="badge bg-secondary">N/A</span>
                @endif
            </td>
            <td>
                {{ $fechaNacRaw ? Date::parse($fechaNacRaw)->format('d/m/Y') : 'N/A' }}
            </td>
            <td>
                @if($fechaBloqueoFmt)
                    <span class="text-nowrap">{{ $fechaBloqueoFmt }}</span>
                @else
                    <span class="text-muted">N/A</span>
                @endif
            </td>
            <td>
                @if($motivo)
                    <button
                        type="button"
                        class="btn btn-sm btn-outline-info"
                        data-action="motivo"
                        data-identidad="{{ $candidato->identidad }}"
                        data-nombre="{{ e(trim(($candidato->nombre ?? '').' '.($candidato->apellido ?? ''))) }}"
                        data-motivo="{{ e($motivo) }}"
                        data-fecha="{{ e($fechaBloqueoFmt ?? '') }}"
                        data-bs-toggle="tooltip"
                        title="Ver motivo"
                    >
                        <i class="ri-message-2-line"></i> Motivo
                    </button>
                @else
                    <span class="text-muted">Sin comentarios</span>
                @endif
            </td>
            <td class="text-center">
                <div class="btn-group" role="group">
                    <button
                        type="button"
                        class="btn btn-sm btn-outline-primary"
                        data-action="ficha"
                        data-ficha-url="{{ route('infopersonal', $candidato->identidad) }}?embed=1"
                        data-ficha-full-url="{{ route('infopersonal', $candidato->identidad) }}"
                        data-identidad="{{ $candidato->identidad }}"
                        data-nombre="{{ e(trim(($candidato->nombre ?? '').' '.($candidato->apellido ?? ''))) }}"
                        data-bs-toggle="tooltip"
                        title="Ver ficha"
                    >
                        <i class="ri-eye-line"></i>
                    </button>

                    <button
                        type="button"
                        class="btn btn-sm btn-outline-secondary"
                        data-action="historial"
                        data-identidad="{{ $candidato->identidad }}"
                        data-nombre="{{ e(trim(($candidato->nombre ?? '').' '.($candidato->apellido ?? ''))) }}"
                        data-history='@json($eventos->values()->all())'
                        data-bs-toggle="tooltip"
                        title="Historial"
                        {{ $eventos->count() > 0 ? '' : 'disabled' }}
                    >
                        <i class="ri-history-line"></i>
                    </button>
                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="9" class="text-center py-5">
                <div class="empty-state">
                    <i class="ri-inbox-line" style="font-size: 4rem; color: #dee2e6;"></i>
                    <h5 class="mt-3 text-muted">No se encontraron registros</h5>
                    <p class="text-muted">Intenta ajustar los filtros de búsqueda</p>
                </div>
            </td>
        </tr>
    @endforelse
</tbody>
