<div class="modal fade" id="unlockcandidate" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="unlockCandidateLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content unlock-modal">
      <div class="modal-header unlock-modal-header">
        <div class="unlock-header-info">
          <div class="unlock-icon">
            <i class="ri-lock-unlock-line"></i>
          </div>
          <div>
            <h1 class="modal-title fs-5" id="unlockCandidateLabel">Desbloqueo de Ingreso</h1>
            <p class="unlock-subtitle">Restablece el acceso del candidato en el sistema.</p>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{route('unlockCandidate')}}" method="POST" class="unlock-form">
        @csrf
        {{--
          1. Cuando se haga click en el boton desbloquear en el archivo app.js asignara el valor de la identidad del registro
          2. Lineas de ejecucion 409 al 421 en el app.js
        --}}
        <div class="modal-body unlock-modal-body">
          <input type="hidden" id="modalidentidad" name="identidad" value="">
          <input type="hidden" name="prohibirIngreso" value="n">

          <div class="unlock-field">
            <label for="ComenProhibirUnlock" class="form-label">Comentarios de Desbloqueo</label>
            <textarea class="form-control unlock-textarea" name="ComenProhibir" id="ComenProhibirUnlock" rows="4" placeholder="Describe el motivo del desbloqueo..." required></textarea>
            <small class="unlock-error" aria-live="polite">Este campo es obligatorio.</small>
          </div>
        </div>
        <div class="modal-footer unlock-modal-footer">
          <button type="button" class="btn-modern btn-secondary" data-bs-dismiss="modal">
            <i class="ri-close-line"></i>
            <span>Cancelar</span>
          </button>
          <button type="submit" class="btn-modern btn-success">
            <i class="ri-lock-unlock-line"></i>
            <span>Desbloquear</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
.unlock-modal {
  border: none;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

.unlock-modal-header {
  background: var(--dark);
  color: white;
  padding: 1.5rem;
  border: none;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.unlock-header-info {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.unlock-icon {
  width: 44px;
  height: 44px;
  border-radius: 10px;
  background: var(--success);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.25rem;
}

.unlock-subtitle {
  margin: 0.25rem 0 0;
  font-size: 0.875rem;
  opacity: 0.9;
}

.unlock-modal-body {
  background: var(--light);
  padding: 1.5rem;
}

.unlock-field .form-label {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--text-primary);
}

.unlock-textarea {
  border: 1px solid var(--border);
  border-radius: 8px;
  padding: 0.75rem;
  font-size: 0.875rem;
  min-height: 120px;
  transition: all 0.3s ease;
}

.unlock-textarea:focus {
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(50, 195, 108, 0.12);
  outline: none;
}

.unlock-modal-footer {
  border-top: 1px solid var(--border);
  padding: 1rem 1.5rem;
  background: white;
  display: flex;
  gap: 0.75rem;
  justify-content: flex-end;
}

.btn-modern.btn-success {
  background: var(--success);
  color: white;
}

.btn-modern.btn-success:hover {
  background: #2aaa5e;
  transform: translateY(-2px);
}

.unlock-error {
  display: none;
  color: var(--danger);
  font-size: 0.75rem;
  margin-top: 0.375rem;
}

.unlock-textarea.is-invalid {
  border-color: var(--danger);
  box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.12);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('#unlockcandidate .unlock-form');
  const textarea = document.querySelector('#unlockcandidate textarea[name="ComenProhibir"]');
  const error = document.querySelector('#unlockcandidate .unlock-error');

  if (!form || !textarea || !error) {
    return;
  }

  form.addEventListener('submit', function (e) {
    const value = textarea.value.trim();
    if (!value) {
      e.preventDefault();
      textarea.classList.add('is-invalid');
      error.style.display = 'block';
      textarea.focus();
    }
  });

  textarea.addEventListener('input', function () {
    if (textarea.value.trim()) {
      textarea.classList.remove('is-invalid');
      error.style.display = 'none';
    }
  });
});
</script>
