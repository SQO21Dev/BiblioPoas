<!doctype html>
<html lang="es" class="light">
<head>
  <meta charset="utf-8">
  <title>BiblioPoás · Dashboard</title>
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
      <h1 class="page-title brand-title h2 m-0">Dashboard</h1>
      <button
        class="btn btn-peach"
        type="button"
        data-bs-toggle="modal"
        data-bs-target="#createTicketModal">
        <i class="fa-solid fa-plus me-2"></i> Crear Tiquete
      </button>
    </div>

    <!-- KPIs -->
    <section class="row g-3 mb-4">
      <!-- Libros -->
      <div class="col-12 col-md-6 col-lg-3">
        <div class="kpi-card p-4">
          <div class="d-flex align-items-center gap-3 mb-2">
            <div class="kpi-icon"><i class="fa-solid fa-book"></i></div>
            <p class="m-0 fw-semibold" style="color: var(--sidebar-text)">Libros (total)</p>
          </div>
          <p class="display-6 fw-bold m-0" style="color: var(--sidebar-text)">
            <?= htmlspecialchars((string)($stats['libros'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
          </p>
        </div>
      </div>

      <!-- Tiquetes activos -->
      <div class="col-12 col-md-6 col-lg-3">
        <div class="kpi-card p-4">
          <div class="d-flex align-items-center gap-3 mb-2">
            <div class="kpi-icon"><i class="fa-solid fa-ticket"></i></div>
            <p class="m-0 fw-semibold" style="color: var(--sidebar-text)">Tiquetes activos</p>
          </div>
          <p class="display-6 fw-bold m-0" style="color: var(--sidebar-text)">
            <?= htmlspecialchars((string)($stats['activos'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
          </p>
        </div>
      </div>

      <!-- Clientes -->
      <div class="col-12 col-md-6 col-lg-3">
        <div class="kpi-card p-4">
          <div class="d-flex align-items-center gap-3 mb-2">
            <div class="kpi-icon"><i class="fa-solid fa-user-group"></i></div>
            <p class="m-0 fw-semibold" style="color: var(--sidebar-text)">Clientes</p>
          </div>
          <p class="display-6 fw-bold m-0" style="color: var(--sidebar-text)">
            <?= htmlspecialchars((string)($stats['clientes'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
          </p>
        </div>
      </div>

      <!-- Vencidos -->
      <div class="col-12 col-md-6 col-lg-3">
        <div class="kpi-card p-4">
          <div class="d-flex align-items-center gap-3 mb-2">
            <div class="kpi-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <p class="m-0 fw-semibold" style="color: var(--sidebar-text)">Vencidos</p>
          </div>
          <p class="display-6 fw-bold m-0" style="color: var(--sidebar-text)">
            <?= htmlspecialchars((string)($stats['vencidos'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
          </p>
        </div>
      </div>
    </section>

    <!-- Tiquetes activos y vencidos -->
    <div class="block-users">
      <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
        <h6 class="m-0">Tiquetes activos y vencidos</h6>
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
              <th class="px-3 py-3">Id Tiquete</th>
              <th class="px-3 py-3">Título del Libro</th>
              <th class="px-3 py-3">Cliente</th>
              <th class="px-3 py-3">Fecha de Vencimiento</th>
              <th class="px-3 py-3">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($tiquetes) && is_array($tiquetes)): ?>
              <?php foreach ($tiquetes as $t): ?>
                <?php
                  $id       = (int)($t['id'] ?? 0);
                  $codigo   = $t['codigo'] ?? '';
                  $titulo   = $t['titulo'] ?? '';
                  $cliente  = $t['nombre_cliente'] ?? '';
                  $fecDev   = $t['fecha_devolucion'] ?? '';
                  $estado   = $t['estado'] ?? 'En Prestamo';

                  $badgeEstado = '';
                  if ($estado === 'Atrasado') {
                    $badgeEstado = '<span class="badge-state-inactive ms-2">Vencido</span>';
                  } elseif ($estado === 'En Prestamo') {
                    $badgeEstado = '<span class="badge-state-active ms-2">Activo</span>';
                  }
                ?>
                <tr>
                  <td class="px-3 py-3 fw-semibold">
                    <?= htmlspecialchars($codigo, ENT_QUOTES, 'UTF-8') ?>
                    <?= $badgeEstado ?>
                  </td>
                  <th scope="row" class="px-3 py-3 fw-semibold" style="color: var(--sidebar-text)">
                    <?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?>
                  </th>
                  <td class="px-3 py-3">
                    <?= htmlspecialchars($cliente, ENT_QUOTES, 'UTF-8') ?>
                  </td>
                  <td class="px-3 py-3">
                    <?= htmlspecialchars($fecDev, ENT_QUOTES, 'UTF-8') ?>
                  </td>
                  <td class="px-3 py-3">
                    <div class="d-flex gap-2">
                      <button
                        class="btn-action-edit"
                        type="button"
                        title="Ver / actualizar tiquete"
                        data-bs-toggle="modal"
                        data-bs-target="#ticketQuickModal"
                        data-ticket='<?= json_encode([
                          'id'               => $id,
                          'codigo'           => $codigo,
                          'titulo'           => $titulo,
                          'cliente'          => $cliente,
                          'fecha_devolucion' => $fecDev,
                          'estado'           => $estado,
                        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>'>
                        <i class="fa-solid fa-up-right-from-square"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" class="px-3 py-4 text-center text-muted">
                  No hay tiquetes activos ni vencidos para mostrar.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <!-- MODAL CREAR TIQUETE -->
  <div class="modal fade modal-ticket" id="createTicketModal" tabindex="-1"
       aria-labelledby="createTicketLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header border-0 pb-0">
          <h5 class="modal-title brand-title" id="createTicketLabel">Crear nuevo tiquete</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <form
            id="ticketCreateForm"
            class="needs-validation"
            novalidate
            method="post"
            action="/tiquetes/create"
          >
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <!-- estado fijo: En Prestamo -->
            <input type="hidden" name="estado" value="En Prestamo">

            <div class="row g-3">
              <!-- Cliente -->
              <div class="col-12 col-md-6">
                <label for="tc_cliente" class="form-label">Nombre del cliente</label>
                <input
                  id="tc_cliente"
                  name="cliente"
                  type="text"
                  class="form-control"
                  required
                  placeholder="Nombre completo">
                <div class="invalid-feedback">Ingresa el nombre del cliente.</div>
              </div>

              <!-- Teléfono -->
              <div class="col-12 col-md-3">
                <label for="tc_telefono" class="form-label">Teléfono</label>
                <input
                  id="tc_telefono"
                  name="telefono"
                  type="text"
                  class="form-control"
                  placeholder="Ej. 8888-8888">
              </div>

              <!-- Dirección -->
              <div class="col-12 col-md-3">
                <label for="tc_direccion" class="form-label">Dirección</label>
                <input
                  id="tc_direccion"
                  name="direccion"
                  type="text"
                  class="form-control"
                  placeholder="Provincia, cantón, distrito">
              </div>

              <!-- Libro -->
              <div class="col-12 col-md-6">
                <label for="tc_libro_id" class="form-label">Libro</label>
                <select
                  id="tc_libro_id"
                  name="libro_id"
                  class="form-select"
                  required>
                  <option value="" selected disabled>Selecciona un libro disponible…</option>
                  <?php if (!empty($libros) && is_array($libros)): ?>
                    <?php foreach ($libros as $lib): ?>
                      <option
                        value="<?= htmlspecialchars((string)$lib['id'], ENT_QUOTES, 'UTF-8') ?>"
                        data-titulo="<?= htmlspecialchars($lib['titulo'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        data-autor="<?= htmlspecialchars($lib['autor'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                      >
                        <?= htmlspecialchars(($lib['titulo'] ?? '') . ($lib['autor'] ? ' – ' . $lib['autor'] : ''), ENT_QUOTES, 'UTF-8') ?>
                      </option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </select>
                <div class="invalid-feedback">Selecciona un libro.</div>

                <!-- Hidden para título que espera el controlador -->
                <input type="hidden" id="tc_libro_titulo" name="libro">
              </div>

              <!-- Autor -->
              <div class="col-12 col-md-6">
                <label for="tc_autor" class="form-label">Autor</label>
                <input
                  id="tc_autor"
                  name="autor"
                  type="text"
                  class="form-control"
                  placeholder="Se rellenará según el libro (puedes ajustarlo)">
              </div>

              <!-- Categoría de edad -->
              <div class="col-12 col-md-6 col-lg-4">
                <label for="tc_categoria_edad" class="form-label">Categoría de edad</label>
                <select id="tc_categoria_edad" name="categoria_edad" class="form-select" required>
                  <option value="" disabled selected>Selecciona...</option>

                  <!-- 0 a 5 años -->
                  <option value="OP">OP – Hombres (0 a 5 años)</option>
                  <option value="AP">AP – Mujeres (0 a 5 años)</option>

                  <!-- 6 a 12 años -->
                  <option value="O">O – Hombres (6 a 12 años)</option>
                  <option value="A">A – Mujeres (6 a 12 años)</option>

                  <!-- 13 a 17 años -->
                  <option value="HJ">HJ – Hombres Jóvenes (13 a 17 años)</option>
                  <option value="MJ">MJ – Mujeres Jóvenes (13 a 17 años)</option>

                  <!-- 18 a 35 años -->
                  <option value="HJU">HJU – Hombres Jóvenes Adultos (18 a 35 años)</option>
                  <option value="MJU">MJU – Mujeres Jóvenes Adultas (18 a 35 años)</option>

                  <!-- 36 a 64 años -->
                  <option value="HA">HA – Hombres Adultos (36 a 64 años)</option>
                  <option value="MA">MA – Mujeres Adultas (36 a 64 años)</option>

                  <!-- 65+ -->
                  <option value="HAM">HAM – Hombres Adultos Mayores (65+ años)</option>
                  <option value="NAM">NAM – Mujeres Adultas Mayores (65+ años)</option>
                </select>
                <div class="invalid-feedback">Selecciona la categoría de edad.</div>
              </div>

              <!-- Fechas -->
              <div class="col-12 col-md-6 col-lg-4">
                <label for="tc_fecha_prestamo" class="form-label">Fecha y hora de préstamo</label>
                <input
                  id="tc_fecha_prestamo"
                  name="fecha_prestamo"
                  type="datetime-local"
                  class="form-control"
                  required>
                <div class="invalid-feedback">Ingresa la fecha de préstamo.</div>
              </div>

              <div class="col-12 col-md-6 col-lg-4">
                <label for="tc_fecha_devolucion" class="form-label">Fecha y hora de devolución</label>
                <input
                  id="tc_fecha_devolucion"
                  name="fecha_devolucion"
                  type="datetime-local"
                  class="form-control"
                  required>
                <div class="invalid-feedback">Ingresa la fecha de devolución.</div>
              </div>

              <!-- Observaciones -->
              <div class="col-12">
                <label for="tc_observaciones" class="form-label">Observaciones</label>
                <textarea
                  id="tc_observaciones"
                  name="observaciones"
                  class="form-control"
                  rows="2"
                  placeholder="Notas adicionales (opcional)"></textarea>
              </div>
            </div>

            <div class="d-flex justify-content-end gap-2 pt-3">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                Cancelar
              </button>
              <button type="submit" class="btn btn-peach">
                Guardar tiquete
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL RÁPIDO PARA TIQUETE (lo de siempre, sin cambios grandes) -->
  <div class="modal fade modal-ticket" id="ticketQuickModal" tabindex="-1"
       aria-labelledby="ticketQuickModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <div class="modal-content">
        <div class="modal-header border-0 pb-0">
          <div>
            <h5 class="modal-title brand-title" id="ticketQuickModalLabel">Detalle de tiquete</h5>
            <div id="ticketQuickInfo" class="small text-muted"></div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <form id="ticketQuickForm" class="needs-validation" novalidate>
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" id="quickTicketId"   name="id">
            <input type="hidden" id="quickAccion"     name="accion">

            <div class="mb-3">
              <label class="form-label">Código</label>
              <input type="text" id="quickCodigo" class="form-control" readonly>
            </div>

            <div class="mb-3">
              <label class="form-label">Título del libro</label>
              <input type="text" id="quickTitulo" class="form-control" readonly>
            </div>

            <div class="mb-3">
              <label class="form-label">Cliente</label>
              <input type="text" id="quickCliente" class="form-control" readonly>
            </div>

            <div class="mb-3">
              <label class="form-label">Estado actual</label>
              <input type="text" id="quickEstado" class="form-control" readonly>
            </div>

            <div class="mb-3">
              <label for="quickFechaDev" class="form-label">Fecha y hora de vencimiento</label>
              <input
                type="datetime-local"
                id="quickFechaDev"
                name="fecha_devolucion"
                class="form-control"
                required>
              <div class="invalid-feedback">Selecciona la fecha de vencimiento.</div>
            </div>

            <div class="d-flex justify-content-between flex-wrap gap-2 pt-3">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                Cerrar
              </button>
              <div class="d-flex gap-2">
                <button type="button" id="btnCerrarTiquete" class="btn btn-outline-danger">
                  Cerrar tiquete
                </button>
                <button type="submit" class="btn btn-accent">
                  Guardar cambios
                </button>
              </div>
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
    // Carga el sidebar
    loadComponent('#sidebar-container', '/components/sidebar.html');

    // Prefill fechas del modal de creación
    (function () {
      const fp = document.getElementById('tc_fecha_prestamo');
      const fd = document.getElementById('tc_fecha_devolucion');
      if (!fp || !fd) return;

      const pad = n => String(n).padStart(2, '0');
      const toLocal = d =>
        `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;

      const now = new Date();
      const in7 = new Date(now.getTime() + 7 * 24 * 60 * 60 * 1000);

      fp.value = toLocal(now);
      fd.value = toLocal(in7);

      fp.addEventListener('change', () => {
        if (!fp.value) return;
        // Si la devolución es menor al préstamo, la igualamos
        if (fd.value < fp.value) {
          fd.value = fp.value;
        }
      });
    })();

    // Cuando cambie el libro seleccionado, rellenar título y autor
    (function () {
      const sel = document.getElementById('tc_libro_id');
      const tituloHidden = document.getElementById('tc_libro_titulo');
      const autorInput   = document.getElementById('tc_autor');
      if (!sel) return;

      sel.addEventListener('change', () => {
        const opt = sel.selectedOptions[0];
        if (!opt) return;

        const titulo = opt.getAttribute('data-titulo') || '';
        const autor  = opt.getAttribute('data-autor') || '';

        if (tituloHidden) tituloHidden.value = titulo;
        if (autorInput && autor !== '') autorInput.value = autor;
      });
    })();

    // Export (reutiliza export de tiquetes)
    document.getElementById('btnExportCsv')?.addEventListener('click', () => {
      window.location.href = '/tiquetes/export/csv';
    });
    document.getElementById('btnExportXlsx')?.addEventListener('click', () => {
      window.location.href = '/tiquetes/export/xlsx';
    });

    const ticketQuickModal = document.getElementById('ticketQuickModal');
    const quickForm        = document.getElementById('ticketQuickForm');
    const infoDiv          = document.getElementById('ticketQuickInfo');

    ticketQuickModal.addEventListener('show.bs.modal', event => {
      const btn   = event.relatedTarget;
      const data  = btn && btn.dataset.ticket ? JSON.parse(btn.dataset.ticket) : null;

      quickForm.classList.remove('was-validated');
      document.getElementById('quickAccion').value  = '';
      document.getElementById('quickTicketId').value = data?.id ?? '';
      document.getElementById('quickCodigo').value  = data?.codigo ?? '';
      document.getElementById('quickTitulo').value  = data?.titulo ?? '';
      document.getElementById('quickCliente').value = data?.cliente ?? '';
      document.getElementById('quickEstado').value  = data?.estado ?? '';

      const raw = data?.fecha_devolucion ?? '';
      let dt = '';
      if (raw) {
        dt = raw.replace(' ', 'T').substring(0, 16);
      }
      document.getElementById('quickFechaDev').value = dt;

      infoDiv.textContent = data?.codigo
        ? `ID interno: ${data.id} · Código: ${data.codigo}`
        : '';
    });

    // Botón "Cerrar tiquete" → manda estado Devuelto
    document.getElementById('btnCerrarTiquete').addEventListener('click', async () => {
      const id = document.getElementById('quickTicketId').value;
      if (!id) return;

      const confirm = await Swal.fire({
        icon: 'warning',
        title: 'Cerrar tiquete',
        text: 'El tiquete pasará a estado Devuelto y el libro quedará disponible.',
        showCancelButton: true,
        confirmButtonText: 'Sí, cerrar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#ec6d13'
      });

      if (!confirm.isConfirmed) return;

      document.getElementById('quickAccion').value = 'cerrar';

      submitQuickForm();
    });

    // Guardar cambios (solo fecha de vencimiento)
    quickForm.addEventListener('submit', e => {
      e.preventDefault();
      document.getElementById('quickAccion').value = 'actualizar_fecha';
      submitQuickForm();
    });

    async function submitQuickForm() {
      if (!quickForm.checkValidity()) {
        quickForm.classList.add('was-validated');
        Swal.fire({
          icon: 'warning',
          title: 'Revisa los campos',
          confirmButtonColor: '#ec6d13'
        });
        return;
      }

      const fd = new FormData(quickForm);

      try {
        const rsp = await fetch('/tiquetes/dashboard-update', {
          method: 'POST',
          body: fd
        });

        let data = null;
        try { data = await rsp.json(); } catch (_) {}

        if (!rsp.ok || !data || !data.ok) {
          throw new Error(data && data.message ? data.message : 'No se pudo actualizar el tiquete.');
        }

        await Swal.fire({
          icon: 'success',
          title: data.message || 'Actualizado',
          confirmButtonColor: '#ec6d13'
        });

        const modalInstance = bootstrap.Modal.getInstance(ticketQuickModal);
        modalInstance.hide();
        window.location.reload();

      } catch (err) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: err && err.message ? err.message : 'Error inesperado',
          confirmButtonColor: '#ec6d13'
        });
      }
    }

    // Validación básica para el formulario de creación (lado cliente)
    (function () {
      const form = document.getElementById('ticketCreateForm');
      if (!form) return;

      form.addEventListener('submit', function (e) {
        if (!form.checkValidity()) {
          e.preventDefault();
          e.stopPropagation();
          form.classList.add('was-validated');
          Swal.fire({
            icon: 'warning',
            title: 'Revisa los campos',
            confirmButtonColor: '#ec6d13'
          });
        }
      });
    })();
  </script>
</body>
</html>
