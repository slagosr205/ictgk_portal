<div class="d-flex justify-content-between align-items-center custom-pagination" id="seguridadPaginationContainer">
    <div class="pagination-info">
        <i class="ri-file-list-line"></i>
        Mostrando <strong>{{ $candidatos->firstItem() ?? 0 }}</strong> a <strong>{{ $candidatos->lastItem() ?? 0 }}</strong> de <strong>{{ $candidatos->total() }}</strong> registros
    </div>
    <nav aria-label="Navegación de página">
        {{ $candidatos->links('pagination::bootstrap-5') }}
    </nav>
</div>
