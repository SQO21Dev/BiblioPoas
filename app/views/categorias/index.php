<!doctype html>
<html lang="es" class="light">
<head>
  <meta charset="utf-8">
  <title>BiblioPoás · Categorías</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Fuentes -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=Nunito:wght@700;900&display=swap" rel="stylesheet">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Font Awesome + SweetAlert2 -->
  <script src="https://kit.fontawesome.com/5152164a0e.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Estilos propios -->
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="dashboard-root">

  <!-- Sidebar dinámico -->
  <div id="sidebar-container"></div>

  <!-- MAIN -->
  <main class="main-content">

    <!-- ENCABEZADO -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
      <h1 class="page-title brand-title h2 m-0">Categorías</h1>
      <button class="btn btn-peach d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#catModal">
        <i class="fa-solid fa-tags"></i> Agregar categoría
      </button>
    </div>

    <!-- TABLA DE CATEGORÍAS -->
    <div class="block-users">
      <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
        <div class="small text-muted">
          Mostrando <?= htmlspecialchars((string)($total ?? 0), ENT_QUOTES, 'UTF-8') ?>
          de <?= htmlspecialchars((string)($total ?? 0), ENT_QUOTES, 'UTF-8') ?>
        </div>
        <div class="d-flex gap-2">
          <button class="btn btn-sm btn-outline-secondary" id="btnExportCsv" title="Exportar CSV">
            <i class="fa-solid fa-file-csv"></i>
          </button>
          <button class="btn btn-sm btn-outline-secondary" id="btnExportXlsx" title="Exportar Excel">
            <i class="fa-regular fa-file-excel"></i>
          </button>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-users align-middle mb-0">
          <thead>
            <tr>
              <th class="px-3 py-3">Nombre</th>
              <th class="px-3 py-3">Descripción</th>
              <th class="px-3 py-3">Creado</th>
              <th class="px-3 py-3">Modificado</th>
              <th class="px-3 py-3">Acciones</th>
            </tr>
          </thead>
          <tbody>
          <?php if (!empty($categorias) && is_array($categorias)): ?>
            <?php foreach ($categorias as $c): ?>
              <tr data-row>
                <th class="px-3 py-3 fw-semibold" scope="row">
                  <?= htmlspecialchars($c['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </th>
                <td class="px-3 py-3 text-muted">
                  <?= htmlspecialchars($c['descripcion'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </td>
                <td class="px-3 py-3 text-muted">
                  <?= htmlspecialchars($c['creado_en'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </td>
                <td class="px-3 py-3 text-muted">
                  <?= htmlspecialchars($c['modificado_en'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </td>
                <td class="px-3 py-3">
                  <div class="d-flex gap-2">
                    <button
                      class="btn-action-edit"
                      title="Editar"
                      data-bs-toggle="modal"
                      data-bs-target="#catModal"
                      data-cat='<?= json_encode([
                        'id'          => (int)($c['id'] ?? 0),
                        'nombre'      => $c['nombre'] ?? '',
                        'descripcion' => $c['descripcion'] ?? '',
                      ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>'>
                      <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button
                      class="btn-action-del"
                      title="Eliminar"
                      onclick="onDeleteCategoria(<?= (int)($c['id'] ?? 0) ?>,'<?= htmlspecialchars($c['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>')">
                      <i class="fa-solid fa-trash-can"></i>
                    </button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" class="px-3 py-4 text-center text-muted">
                No hay categorías registradas.
              </td>
            </tr>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <!-- MODAL CREAR/EDITAR CATEGORÍA -->
  <div class="modal fade modal-ticket" id="catModal" tabindex="-1" aria-labelledby="catModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <div class="modal-content">
        <div class="modal-header border-0 pb-0">
          <h5 class="modal-title brand-title" id="catModalLabel">Agregar Categoría</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <form id="catForm" class="needs-validation" novalidate>
            <input type="hidden" name="_csrf" id="csrfField"
                   value="<?= htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" id="catId" name="id">

            <div class="mb-3">
              <label for="catNombre" class="form-label">Nombre</label>
              <input id="catNombre" name="nombre" type="text" class="form-control" required>
              <div class="invalid-feedback">Ingresa el nombre.</div>
            </div>

            <div class="mb-3">
              <label for="catDesc" class="form-label">Descripción</label>
              <textarea id="catDesc" name="descripcion" class="form-control" rows="3" placeholder="Opcional"></textarea>
            </div>

            <div class="d-flex justify-content-end gap-2 pt-3">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-accent">Guardar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Sidebar loader -->
  <script src="/assets/js/components.js"></script>

  <script>
    // Cargar sidebar
    loadComponent('#sidebar-container', '/components/sidebar.html');

    // Export
    document.getElementById('btnExportCsv')?.addEventListener('click', () => {
      window.location.href = '/categorias/export/csv';
    });
    document.getElementById('btnExportXlsx')?.addEventListener('click', () => {
      window.location.href = '/categorias/export/xlsx';
    });

    // Modal alta/edición
    const catModal = document.getElementById('catModal');
    catModal.addEventListener('show.bs.modal', (ev) => {
      const btn   = ev.relatedTarget;
      const title = document.getElementById('catModalLabel');
      const form  = document.getElementById('catForm');

      form.reset();
      form.classList.remove('was-validated');
      document.getElementById('catId').value = '';

      title.textContent = 'Agregar Categoría';

      if (btn && btn.dataset.cat) {
        const data = JSON.parse(btn.dataset.cat);
        title.textContent = 'Editar Categoría';
        document.getElementById('catId').value     = data.id || '';
        document.getElementById('catNombre').value = data.nombre || '';
        document.getElementById('catDesc').value   = data.descripcion || '';
      }
    });

    // Submit (create/update via fetch)
    document.getElementById('catForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      const form = e.target;

      if (!form.checkValidity()) {
        e.stopPropagation();
        form.classList.add('was-validated');
        Swal.fire({
          icon: 'warning',
          title: 'Revisa los campos',
          confirmButtonColor: '#ec6d13'
        });
        return;
      }

      const isEdit = !!document.getElementById('catId').value;
      const url    = isEdit ? '/categorias/update' : '/categorias/create';

      const fd = new FormData(form);

      try {
        const rsp = await fetch(url, {
          method: 'POST',
          body: fd
        });

        // El backend siempre redirige en éxito o error,
        // así que si viene redirect lo seguimos.
        if (rsp.redirected) {
          window.location.href = rsp.url;
          return;
        }

        // Si no redirige, algo raro pasó.
        await Swal.fire({
          icon: 'error',
          title: 'No se pudo guardar la categoría.',
          confirmButtonColor: '#ec6d13'
        });
      } catch (err) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: (err && err.message) ? err.message : 'Error inesperado',
          confirmButtonColor: '#ec6d13'
        });
      }
    });

    // Eliminar categoría
    async function onDeleteCategoria(id, nombre) {
      const ok = await Swal.fire({
        icon: 'warning',
        title: '¿Eliminar categoría?',
        text: nombre || `ID ${id}`,
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#ec6d13'
      });

      if (!ok.isConfirmed) return;

      const fd = new FormData();
      fd.append('id', String(id));
      fd.append('_csrf', document.getElementById('csrfField').value);

      try {
        const rsp = await fetch('/categorias/delete', {
          method: 'POST',
          body: fd
        });

        if (rsp.redirected) {
          window.location.href = rsp.url;
          return;
        }

        await Swal.fire({
          icon: 'success',
          title: 'Eliminada',
          confirmButtonColor: '#ec6d13'
        });

        window.location.reload();
      } catch (err) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: (err && err.message) ? err.message : 'Error inesperado',
          confirmButtonColor: '#ec6d13'
        });
      }
    }
    window.onDeleteCategoria = onDeleteCategoria;
  </script>
</body>
</html>
