<!doctype html>
<html lang="es" class="light">
<head>
  <meta charset="utf-8">
  <title>BiblioPoás · Clientes</title>
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
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
      <h1 class="page-title brand-title h2 m-0">Gestión de Clientes</h1>

      <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary d-none d-md-inline-flex" id="btnExportCsv" title="Exportar CSV">
          <i class="fa-solid fa-file-csv me-2"></i> CSV
        </button>
        <button class="btn btn-outline-secondary d-none d-md-inline-flex" id="btnExportXlsx" title="Exportar Excel">
          <i class="fa-regular fa-file-excel me-2"></i> Excel
        </button>
        <button class="btn btn-peach d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#clientModal">
          <i class="fa-solid fa-user-plus"></i> Agregar cliente
        </button>
      </div>
    </div>

    <!-- KPIs -->
    <section class="row g-3 mb-4">
      <div class="col-12 col-md-6 col-lg-4">
        <div class="kpi-card p-4">
          <div class="d-flex align-items-center gap-3 mb-2">
            <div class="kpi-icon"><i class="fa-solid fa-users"></i></div>
            <p class="m-0 fw-semibold" style="color: var(--sidebar-text)">Total de Clientes</p>
          </div>
          <p class="display-6 fw-bold m-0" style="color: var(--sidebar-text)">
            <?= htmlspecialchars($stats['total'] ?? '0', ENT_QUOTES, 'UTF-8') ?>
          </p>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-4">
        <div class="kpi-card p-4">
          <div class="d-flex align-items-center gap-3 mb-2">
            <div class="kpi-icon"><i class="fa-solid fa-user-check"></i></div>
            <p class="m-0 fw-semibold" style="color: var(--sidebar-text)">Clientes Activos</p>
          </div>
          <p class="display-6 fw-bold m-0" style="color: var(--sidebar-text)">
            <?= htmlspecialchars($stats['activos'] ?? '0', ENT_QUOTES, 'UTF-8') ?>
          </p>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-4">
        <div class="kpi-card p-4">
          <div class="d-flex align-items-center gap-3 mb-2">
            <div class="kpi-icon"><i class="fa-solid fa-bell"></i></div>
            <p class="m-0 fw-semibold" style="color: var(--sidebar-text)">Con préstamos activos</p>
          </div>
          <p class="display-6 fw-bold m-0" style="color: var(--sidebar-text)">
            <?= htmlspecialchars($stats['con_prestamo'] ?? '0', ENT_QUOTES, 'UTF-8') ?>
          </p>
        </div>
      </div>
    </section>

    <!-- Filtro -->
    <div class="row g-2 align-items-center mb-3">
      <div class="col-12 col-md-4">
        <div class="input-group">
          <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
          <input id="filterInput" type="search" class="form-control" placeholder="Buscar por nombre, cédula, teléfono o correo…">
        </div>
      </div>
      <div class="col-12 col-md-auto ms-auto">
        <span class="text-muted small">Total: <strong id="totalRows"><?= isset($clientes) ? count($clientes) : 0 ?></strong></span>
      </div>
    </div>

    <!-- Tabla -->
    <div class="block-users">
      <div class="table-responsive">
        <table class="table table-users align-middle mb-0" id="clientsTable">
          <thead>
            <tr>
              <th class="px-3 py-3">Nombre</th>
              <th class="px-3 py-3">Cédula</th>
              <th class="px-3 py-3">Teléfono</th>
              <th class="px-3 py-3">Correo Electrónico</th>
              <th class="px-3 py-3">Estado</th>
              <th class="px-3 py-3">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($clientes) && is_array($clientes)): ?>
              <?php foreach ($clientes as $c): ?>
                <tr data-row>
                  <td class="px-3 py-3 fw-semibold">
                    <?= htmlspecialchars($c['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                  </td>
                  <td class="px-3 py-3 text-muted">
                    <?= htmlspecialchars($c['cedula'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                  </td>
                  <td class="px-3 py-3 text-muted">
                    <?= htmlspecialchars($c['telefono'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                  </td>
                  <td class="px-3 py-3 text-muted">
                    <?= htmlspecialchars($c['correo'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                  </td>
                  <td class="px-3 py-3">
                    <?php if (($c['estado'] ?? 'activo') === 'activo'): ?>
                      <span class="badge-state-active">Activo</span>
                    <?php else: ?>
                      <span class="badge-state-inactive">Inactivo</span>
                    <?php endif; ?>
                  </td>
                  <td class="px-3 py-3">
                    <div class="d-flex gap-2">
                      <button
                        class="btn-action-edit"
                        title="Editar"
                        data-bs-toggle="modal"
                        data-bs-target="#clientModal"
                        data-client='<?= json_encode([
                          'id'        => (int)($c['id'] ?? 0),
                          'nombre'    => $c['nombre'] ?? '',
                          'cedula'    => $c['cedula'] ?? '',
                          'telefono'  => $c['telefono'] ?? '',
                          'correo'    => $c['correo'] ?? '',
                          'direccion' => $c['direccion'] ?? '',
                          'estado'    => $c['estado'] ?? 'activo',
                        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>'>
                        <i class="fa-solid fa-pen-to-square"></i>
                      </button>

                      <button
                        class="btn-action-del"
                        title="Eliminar"
                        onclick="onDeleteClient(<?= (int)($c['id'] ?? 0) ?>,'<?= htmlspecialchars($c['nombre'] ?? 'Cliente', ENT_QUOTES, 'UTF-8') ?>')">
                        <i class="fa-solid fa-trash-can"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <!-- Modal Agregar/Editar Cliente -->
  <div class="modal fade modal-ticket" id="clientModal" tabindex="-1" aria-labelledby="clientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <div class="modal-content">
        <div class="modal-header border-0 pb-0">
          <h5 class="modal-title brand-title" id="clientModalLabel">Agregar Nuevo Cliente</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <form id="clientForm" class="needs-validation" novalidate>
            <input type="hidden" name="_csrf" id="csrfField" value="<?= htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="id" id="clientId">

            <div class="mb-3">
              <label for="clienteNombre" class="form-label">Nombre completo</label>
              <input name="nombre" id="clienteNombre" type="text" class="form-control" placeholder="p.ej. Ana Rodríguez" required>
              <div class="invalid-feedback">Ingresa el nombre.</div>
            </div>

            <div class="mb-3">
              <label for="clienteCedula" class="form-label">Cédula</label>
              <input name="cedula" id="clienteCedula" type="text" class="form-control" placeholder="p.ej. 1-2345-0678" required>
              <div class="invalid-feedback">Ingresa la cédula.</div>
            </div>

            <div class="row g-3">
              <div class="col-12 col-md-6">
                <label for="clienteTelefono" class="form-label">Teléfono</label>
                <input name="telefono" id="clienteTelefono" type="tel" class="form-control" placeholder="8888-0000" required>
                <div class="invalid-feedback">Ingresa el teléfono.</div>
              </div>
              <div class="col-12 col-md-6">
                <label for="clienteCorreo" class="form-label">Correo electrónico</label>
                <input name="correo" id="clienteCorreo" type="email" class="form-control" placeholder="cliente@correo.com" required>
                <div class="invalid-feedback">Correo inválido.</div>
              </div>
            </div>

            <div class="mb-3 mt-3">
              <label for="clienteDireccion" class="form-label">Dirección</label>
              <input name="direccion" id="clienteDireccion" type="text" class="form-control" placeholder="Provincia, Cantón, Distrito, Detalle" required>
              <div class="invalid-feedback">Ingresa la dirección.</div>
            </div>

            <div class="mb-1">
              <label for="clienteEstado" class="form-label">Estado</label>
              <select name="estado" id="clienteEstado" class="form-select" required>
                <option value="" disabled selected>Selecciona…</option>
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
              </select>
              <div class="invalid-feedback">Selecciona un estado.</div>
            </div>

            <div class="d-flex justify-content-end gap-2 pt-3">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-coral">Guardar</button>
            </div>
          </form>
        </div>

      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Sidebar loader + helpers -->
  <script src="/assets/js/components.js"></script>
  <script>
    // Cargar sidebar
    loadComponent('#sidebar-container', '/components/sidebar.html');

    // Filtro rápido por texto
    const filterInput = document.getElementById('filterInput');
    const tbody       = document.querySelector('#clientsTable tbody');
    const totalRows   = document.getElementById('totalRows');

    function applyFilter() {
      const q = (filterInput.value || '').toLowerCase();
      let visible = 0;
      tbody.querySelectorAll('tr[data-row]').forEach(tr => {
        const text = tr.innerText.toLowerCase();
        const ok   = !q || text.includes(q);
        tr.style.display = ok ? '' : 'none';
        if (ok) visible++;
      });
      totalRows.textContent = visible;
    }

    if (filterInput) {
      filterInput.addEventListener('input', applyFilter);
    }

    // Export
    document.getElementById('btnExportCsv')?.addEventListener('click', () => {
      window.location.href = '/clientes/export/csv';
    });
    document.getElementById('btnExportXlsx')?.addEventListener('click', () => {
      window.location.href = '/clientes/export/xlsx';
    });

    // Modal: alta/edición
    const clientModal = document.getElementById('clientModal');
    clientModal.addEventListener('show.bs.modal', (event) => {
      const btn   = event.relatedTarget;
      const title = document.getElementById('clientModalLabel');
      const form  = document.getElementById('clientForm');

      form.classList.remove('was-validated');
      form.reset();
      document.getElementById('clientId').value = '';

      title.textContent = 'Agregar Nuevo Cliente';

      if (btn && btn.dataset.client) {
        const data = JSON.parse(btn.dataset.client);
        title.textContent = 'Editar Cliente';

        document.getElementById('clientId').value         = data.id || '';
        document.getElementById('clienteNombre').value    = data.nombre || '';
        document.getElementById('clienteCedula').value    = data.cedula || '';
        document.getElementById('clienteTelefono').value  = data.telefono || '';
        document.getElementById('clienteCorreo').value    = data.correo || '';
        document.getElementById('clienteDireccion').value = data.direccion || '';
        document.getElementById('clienteEstado').value    = data.estado || 'activo';
      }
    });

    // Submit (create/update via fetch + redirecciones del backend)
    document.getElementById('clientForm').addEventListener('submit', async (e) => {
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

      const isEdit = !!document.getElementById('clientId').value;
      const url    = isEdit ? '/clientes/update' : '/clientes/create';

      const fd = new FormData(form);

      try {
        const rsp = await fetch(url, {
          method: 'POST',
          body: fd
        });

        if (rsp.redirected) {
          // El controlador redirige a /clientes (con flash)
          window.location.href = rsp.url;
          return;
        }

        await Swal.fire({
          icon: 'success',
          title: isEdit ? 'Cliente actualizado' : 'Cliente agregado',
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
    });

    // Eliminar cliente
    async function onDeleteClient(id, nombre) {
      const ok = await Swal.fire({
        icon: 'warning',
        title: '¿Eliminar cliente?',
        text: nombre,
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
        const rsp = await fetch('/clientes/delete', {
          method: 'POST',
          body: fd
        });

        if (rsp.redirected) {
          window.location.href = rsp.url;
          return;
        }

        await Swal.fire({
          icon: 'success',
          title: 'Eliminado',
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
    window.onDeleteClient = onDeleteClient;
  </script>
</body>
</html>
