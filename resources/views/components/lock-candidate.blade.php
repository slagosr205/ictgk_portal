{{---Modal para exclusion---}}

<div class="modal fade" id="lockcandidate" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="lockCandidateLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content lock-modal">
      <div class="modal-header lock-modal-header">
        <div class="lock-header-info">
          <div class="lock-icon">
            <i class="ri-lock-line"></i>
          </div>
          <div>
            <h1 class="modal-title fs-5" id="lockCandidateLabel">Bloqueo de Ingreso</h1>
            <p class="lock-subtitle">Este bloqueo impide nuevos ingresos para el candidato.</p>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{route('lockCandidate')}}" method="post" class="lock-form">
        @csrf
        <div class="modal-body lock-modal-body">
          <input type="hidden" id="lockidentidad" name="identidad" value="">
          <input type="hidden" name="prohibirIngreso" value="x">

          <div class="lock-field">
            <label for="ComenProhibirLock" class="form-label">Comentarios de Bloqueo</label>
            <textarea class="form-control lock-textarea" name="ComenProhibir" id="ComenProhibirLock" rows="4" placeholder="Describe el motivo del bloqueo..." required></textarea>
            <small class="lock-error" aria-live="polite">Este campo es obligatorio.</small>
          </div>
        </div>
        <div class="modal-footer lock-modal-footer">
          <button type="button" class="btn-modern btn-secondary" data-bs-dismiss="modal">
            <i class="ri-close-line"></i>
            <span>Cancelar</span>
          </button>
          <button type="submit" class="btn-modern btn-danger">
            <i class="ri-lock-line"></i>
            <span>Bloquear</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
.lock-modal {
  border: none;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

.lock-modal-header {
  background: var(--dark);
  color: white;
  padding: 1.5rem;
  border: none;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.lock-header-info {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.lock-icon {
  width: 44px;
  height: 44px;
  border-radius: 10px;
  background: var(--danger);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.25rem;
}

.lock-subtitle {
  margin: 0.25rem 0 0;
  font-size: 0.875rem;
  opacity: 0.9;
}

.lock-modal-body {
  background: var(--light);
  padding: 1.5rem;
}

.lock-field .form-label {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--text-primary);
}

.lock-textarea {
  border: 1px solid var(--border);
  border-radius: 8px;
  padding: 0.75rem;
  font-size: 0.875rem;
  min-height: 120px;
  transition: all 0.3s ease;
}

.lock-textarea:focus {
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(50, 195, 108, 0.12);
  outline: none;
}

.lock-modal-footer {
  border-top: 1px solid var(--border);
  padding: 1rem 1.5rem;
  background: white;
  display: flex;
  gap: 0.75rem;
  justify-content: flex-end;
}

.btn-modern.btn-danger {
  background: var(--danger);
  color: white;
}

.btn-modern.btn-danger:hover {
  background: #c0392b;
  transform: translateY(-2px);
}

.lock-error {
  display: none;
  color: var(--danger);
  font-size: 0.75rem;
  margin-top: 0.375rem;
}

.lock-textarea.is-invalid {
  border-color: var(--danger);
  box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.12);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('#lockcandidate .lock-form');
  const textarea = document.querySelector('#lockcandidate textarea[name="ComenProhibir"]');
  const error = document.querySelector('#lockcandidate .lock-error');

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

