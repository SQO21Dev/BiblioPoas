<!doctype html>
<html lang="es" class="light">
<head>
  <meta charset="utf-8">
  <title>BiblioPoás · Usuarios</title>
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
      <h1 class="page-title brand-title h2 m-0">Gestión de Usuarios</h1>

      <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary d-none d-md-inline-flex" id="btnExportCsv" title="Exportar CSV">
          <i class="fa-solid fa-file-csv me-2"></i> CSV
        </button>
        <button class="btn btn-outline-secondary d-none d-md-inline-flex" id="btnExportXlsx" title="Exportar Excel">
          <i class="fa-regular fa-file-excel me-2"></i> Excel
        </button>
        <button class="btn btn-peach d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#userModal">
          <i class="fa-solid fa-user-plus"></i> Agregar usuario
        </button>
      </div>
    </div>

    <!-- KPIs -->
    <section class="row g-3 mb-4">
      <div class="col-12 col-md-6 col-lg-4">
        <div class="kpi-card p-4">
          <div class="d-flex align-items-center gap-3 mb-2">
            <div class="kpi-icon"><i class="fa-solid fa-user-shield"></i></div>
            <p class="m-0 fw-semibold" style="color: var(--sidebar-text)">Total de Administradores</p>
          </div>
          <p class="display-6 fw-bold m-0" style="color: var(--sidebar-text)">
            <?= htmlspecialchars($stats['admins'] ?? '0', ENT_QUOTES, 'UTF-8') ?>
          </p>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-4">
        <div class="kpi-card p-4">
          <div class="d-flex align-items-center gap-3 mb-2">
            <div class="kpi-icon"><i class="fa-solid fa-users"></i></div>
            <p class="m-0 fw-semibold" style="color: var(--sidebar-text)">Total de Empleados</p>
          </div>
          <p class="display-6 fw-bold m-0" style="color: var(--sidebar-text)">
            <?= htmlspecialchars($stats['empleados'] ?? '0', ENT_QUOTES, 'UTF-8') ?>
          </p>
        </div>
      </div>
    </section>

    <!-- Filtro -->
    <div class="row g-2 align-items-center mb-3">
      <div class="col-12 col-md-4">
        <div class="input-group">
          <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
          <input id="filterInput" type="search" class="form-control" placeholder="Buscar por nombre, correo o usuario…">
        </div>
      </div>
      <div class="col-12 col-md-auto ms-auto">
        <span class="text-muted small">Total: <strong id="totalRows"><?= isset($usuarios) ? count($usuarios) : 0 ?></strong></span>
      </div>
    </div>

    <!-- Tabla -->
    <div class="block-users">
      <div class="table-responsive">
        <table class="table table-users align-middle mb-0" id="usersTable">
          <thead>
            <tr>
              <th class="px-3 py-3">Usuario</th>
              <th class="px-3 py-3">Nombre</th>
              <th class="px-3 py-3">Correo</th>
              <th class="px-3 py-3">Rol</th>
              <th class="px-3 py-3">Estado</th>
              <th class="px-3 py-3">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($usuarios) && is_array($usuarios)): ?>
              <?php foreach ($usuarios as $u): ?>
                <tr data-row>
                  <td class="px-3 py-3 fw-semibold">
                    <?= htmlspecialchars($u['usuario'], ENT_QUOTES, 'UTF-8') ?>
                  </td>
                  <td class="px-3 py-3">
                    <?= htmlspecialchars($u['nombre'], ENT_QUOTES, 'UTF-8') ?>
                  </td>
                  <td class="px-3 py-3 text-muted">
                    <?= htmlspecialchars($u['correo'], ENT_QUOTES, 'UTF-8') ?>
                  </td>
                  <td class="px-3 py-3">
                    <?php if (($u['rol'] ?? '') === 'admin'): ?>
                      <span class="badge-role-admin">Admin</span>
                    <?php else: ?>
                      <span class="badge-role-emp">Empleado</span>
                    <?php endif; ?>
                  </td>
                  <td class="px-3 py-3">
                    <?php if (($u['estado'] ?? 'activo') === 'activo'): ?>
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
                        data-bs-target="#userModal"
                        data-user='<?= json_encode([
                          'id'      => (int)($u['id'] ?? 0),
                          'usuario' => $u['usuario'] ?? '',
                          'nombre'  => $u['nombre'] ?? '',
                          'correo'  => $u['correo'] ?? '',
                          'rol'     => $u['rol'] ?? 'empleado',
                          'estado'  => $u['estado'] ?? 'activo',
                        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>'>
                        <i class="fa-solid fa-pen-to-square"></i>
                      </button>

                      <button
                        class="btn-action-del"
                        title="Eliminar"
                        onclick="onDeleteUser(<?= (int)($u['id'] ?? 0) ?>,'<?= htmlspecialchars($u['nombre'] ?? $u['usuario'] ?? 'usuario', ENT_QUOTES, 'UTF-8') ?>')">
                        <i class="fa-solid fa-trash-can"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <!-- Fila demo si no vienen datos -->
              <tr data-row>
                <td class="px-3 py-3 fw-semibold">sebasqo</td>
                <td class="px-3 py-3">Sebastián Admin</td>
                <td class="px-3 py-3 text-muted">sebasqo21@outlook.com</td>
                <td class="px-3 py-3"><span class="badge-role-admin">Admin</span></td>
                <td class="px-3 py-3"><span class="badge-state-active">Activo</span></td>
                <td class="px-3 py-3">
                  <div class="d-flex gap-2">
                    <button
                      class="btn-action-edit"
                      title="Editar"
                      data-bs-toggle="modal"
                      data-bs-target="#userModal"
                      data-user='{"id":1,"usuario":"sebasqo","nombre":"Sebastián Admin","correo":"sebasqo21@outlook.com","rol":"admin","estado":"activo"}'>
                      <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button
                      class="btn-action-del"
                      title="Eliminar"
                      onclick="onDeleteUser(1,'Sebastián Admin')">
                      <i class="fa-solid fa-trash-can"></i>
                    </button>
                  </div>
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <!-- Modal Crear/Editar Usuario -->
  <div class="modal fade modal-ticket" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <div class="modal-content">
        <div class="modal-header border-0 pb-0">
          <h5 class="modal-title brand-title" id="userModalLabel">Agregar Nuevo Usuario</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <form id="userForm" class="needs-validation" novalidate>
            <input type="hidden" name="_csrf" id="csrfField" value="<?= htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="id" id="userId" value="">

            <div class="mb-3">
              <label for="usuario" class="form-label">Usuario</label>
              <input name="usuario" id="usuario" type="text" class="form-control" placeholder="p.ej. agarcia" required>
              <div class="invalid-feedback">Ingresa el usuario.</div>
            </div>

            <div class="mb-3">
              <label for="nombre" class="form-label">Nombre completo</label>
              <input name="nombre" id="nombre" type="text" class="form-control" placeholder="p.ej. Ana García" required>
              <div class="invalid-feedback">Ingresa el nombre.</div>
            </div>

            <div class="mb-3">
              <label for="correo" class="form-label">Correo electrónico</label>
              <input name="correo" id="correo" type="email" class="form-control" placeholder="usuario@bibliopoas.cr" required>
              <div class="invalid-feedback">Correo inválido.</div>
            </div>

            <div class="mb-3" id="passwordGroup">
              <label for="contrasena" class="form-label">Contraseña</label>
              <input name="contrasena" id="contrasena" type="password" class="form-control" minlength="6" placeholder="********" required>
              <div class="invalid-feedback">Mínimo 6 caracteres.</div>
            </div>

            <div class="row g-3">
              <div class="col-12 col-md-6">
                <label for="rol" class="form-label">Rol</label>
                <select name="rol" id="rol" class="form-select" required>
                  <option value="" disabled selected>Selecciona…</option>
                  <option value="admin">Admin</option>
                  <option value="empleado">Empleado</option>
                </select>
                <div class="invalid-feedback">Selecciona un rol.</div>
              </div>
              <div class="col-12 col-md-6">
                <label for="estado" class="form-label">Estado</label>
                <select name="estado" id="estado" class="form-select" required>
                  <option value="" disabled selected>Selecciona…</option>
                  <option value="activo">Activo</option>
                  <option value="inactivo">Inactivo</option>
                </select>
                <div class="invalid-feedback">Selecciona un estado.</div>
              </div>
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
    const tbody       = document.querySelector('#usersTable tbody');
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
      window.location.href = '/usuarios/export/csv';
    });
    document.getElementById('btnExportXlsx')?.addEventListener('click', () => {
      window.location.href = '/usuarios/export/xlsx';
    });

    // Modal: alta/edición
    const userModal = document.getElementById('userModal');
    userModal.addEventListener('show.bs.modal', (event) => {
      const btn           = event.relatedTarget;
      const title         = document.getElementById('userModalLabel');
      const passwordGroup = document.getElementById('passwordGroup');
      const form          = document.getElementById('userForm');

      form.classList.remove('was-validated');
      form.reset();
      document.getElementById('userId').value = '';

      // Modo por defecto: crear
      title.textContent = 'Agregar Nuevo Usuario';
      passwordGroup.classList.remove('d-none');
      document.getElementById('contrasena').required = true;

      if (btn && btn.dataset.user) {
        const data = JSON.parse(btn.dataset.user);
        title.textContent = 'Editar Usuario';

        document.getElementById('userId').value   = data.id || '';
        document.getElementById('usuario').value  = data.usuario || '';
        document.getElementById('nombre').value   = data.nombre || '';
        document.getElementById('correo').value   = data.correo || '';
        document.getElementById('rol').value      = data.rol || 'empleado';
        document.getElementById('estado').value   = data.estado || 'activo';

        // En edición no pedimos contraseña obligatoria
        passwordGroup.classList.add('d-none');
        document.getElementById('contrasena').required = false;
      }
    });

    // Submit (usa fetch pero respeta tus redirecciones del backend)
    document.getElementById('userForm').addEventListener('submit', async (e) => {
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

      const isEdit = !!document.getElementById('userId').value;
      const url    = isEdit ? '/usuarios/update' : '/usuarios/create';

      const fd = new FormData(form);

      try {
        const rsp = await fetch(url, {
          method: 'POST',
          body: fd
        });

        if (rsp.redirected) {
          // El controlador redirige a /usuarios (con flash)
          window.location.href = rsp.url;
          return;
        }

        // Si algún día cambias a JSON, aquí se podría leer.
        await Swal.fire({
          icon: 'success',
          title: isEdit ? 'Usuario actualizado' : 'Usuario agregado',
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

    // Eliminar
    async function onDeleteUser(id, nombre) {
      const ok = await Swal.fire({
        icon: 'warning',
        title: '¿Eliminar usuario?',
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
        const rsp = await fetch('/usuarios/delete', {
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
    window.onDeleteUser = onDeleteUser;
  </script>
</body>
</html>
