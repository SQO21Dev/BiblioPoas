<!doctype html>
<html lang="es" class="light">

<head>
  <meta charset="utf-8">
  <title>BiblioPoás · Tiquetes</title>
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
      <h1 class="page-title brand-title h2 m-0">Gestión de Tiquetes</h1>
      <button class="btn btn-peach d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#ticketModal">
        <i class="fa-solid fa-ticket"></i> Crear tiquete
      </button>
    </div>

    <!-- KPI CARDS -->
    <section class="row g-3 mb-4">
      <div class="col-12 col-md-6 col-lg-4">
        <div class="kpi-card p-4">
          <div class="d-flex align-items-center gap-3 mb-2">
            <div class="kpi-icon"><i class="fa-solid fa-ticket"></i></div>
            <p class="m-0 fw-semibold" style="color: var(--sidebar-text)">Total de Tiquetes</p>
          </div>
          <p class="display-6 fw-bold m-0" style="color: var(--sidebar-text)">
            <?= htmlspecialchars($stats['total'] ?? '0', ENT_QUOTES, 'UTF-8') ?>
          </p>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-4">
        <div class="kpi-card p-4">
          <div class="d-flex align-items-center gap-3 mb-2">
            <div class="kpi-icon"><i class="fa-solid fa-folder-open"></i></div>
            <p class="m-0 fw-semibold" style="color: var(--sidebar-text)">Tiquetes Activos</p>
          </div>
          <p class="display-6 fw-bold m-0" style="color: var(--sidebar-text)">
            <?= htmlspecialchars($stats['activos'] ?? '0', ENT_QUOTES, 'UTF-8') ?>
          </p>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-4">
        <div class="kpi-card p-4">
          <div class="d-flex align-items-center gap-3 mb-2">
            <div class="kpi-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <p class="m-0 fw-semibold" style="color: var(--sidebar-text)">Vencidos</p>
          </div>
          <p class="display-6 fw-bold m-0" style="color: var(--sidebar-text)">
            <?= htmlspecialchars($stats['vencidos'] ?? '0', ENT_QUOTES, 'UTF-8') ?>
          </p>
        </div>
      </div>
    </section>

    <!-- TABLA DE TIQUETES -->
    <div class="block-users">
      <div class="d-flex justify-content-end gap-2 px-3 py-2 border-bottom">
        <button class="btn btn-sm btn-outline-secondary" id="btnExportCsv" title="Exportar CSV">
          <i class="fa-solid fa-file-csv"></i>
        </button>
        <button class="btn btn-sm btn-outline-secondary" id="btnExportXlsx" title="Exportar Excel">
          <i class="fa-regular fa-file-excel"></i>
        </button>
      </div>

      <div class="table-responsive">
        <table class="table table-users align-middle mb-0">
          <thead>
            <tr>
              <th class="px-3 py-3">Código</th>
              <th class="px-3 py-3">Cliente</th>
              <th class="px-3 py-3">Teléfono</th>
              <th class="px-3 py-3">Dirección</th>
              <th class="px-3 py-3">Libro</th>
              <th class="px-3 py-3">Autor</th>
              <th class="px-3 py-3">Fecha Préstamo</th>
              <th class="px-3 py-3">Fecha Devolución</th>
              <th class="px-3 py-3">Estado</th>
              <th class="px-3 py-3">Creado</th>
              <th class="px-3 py-3">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($tiquetes) && is_array($tiquetes)): ?>
              <?php foreach ($tiquetes as $t): ?>
                <tr data-row>
                  <td class="px-3 py-3 fw-semibold">
                    <?= htmlspecialchars($t['codigo'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                  </td>
                  <td class="px-3 py-3">
                    <?= htmlspecialchars($t['nombre_cliente'] ?? $t['cliente_rel'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                  </td>
                  <td class="px-3 py-3 text-muted">
                    <?= htmlspecialchars($t['telefono'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                  </td>
                  <td class="px-3 py-3 text-muted">
                    <?= htmlspecialchars($t['direccion'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                  </td>
                  <td class="px-3 py-3">
                    <?= htmlspecialchars($t['titulo'] ?? $t['libro_rel'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                  </td>
                  <td class="px-3 py-3 text-muted">
                    <?= htmlspecialchars($t['autor'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                  </td>
                  <td class="px-3 py-3 text-muted">
                    <?= htmlspecialchars($t['fecha_prestamo'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                  </td>
                  <td class="px-3 py-3 text-muted">
                    <?= htmlspecialchars($t['fecha_devolucion'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                  </td>
                  <td class="px-3 py-3">
                    <?php
                    $estado = $t['estado'] ?? 'En Prestamo';
                    if ($estado === 'En Prestamo') {
                      $badgeClass  = 'badge-state-active';
                      $textoEstado = 'En préstamo';
                    } elseif ($estado === 'Devuelto') {
                      $badgeClass  = 'badge-state-neutral';
                      $textoEstado = 'Devuelto';
                    } else {
                      $badgeClass  = 'badge-state-inactive';
                      $textoEstado = 'Retrasado';
                    }
                    ?>
                    <span class="<?= $badgeClass ?>"><?= $textoEstado ?></span>
                  </td>
                  <td class="px-3 py-3 text-muted">
                    <?= htmlspecialchars($t['creado_en'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                  </td>
                  <td class="px-3 py-3">
                    <div class="d-flex gap-2">
                      <button
                        class="btn-action-edit"
                        title="Editar"
                        data-bs-toggle="modal"
                        data-bs-target="#ticketModal"
                        data-ticket='<?= json_encode([
                                        'id'              => (int)($t['id'] ?? 0),
                                        'codigo'          => $t['codigo'] ?? '',
                                        'cliente'         => $t['nombre_cliente'] ?? $t['cliente_rel'] ?? '',
                                        'cliente_id'      => $t['cliente_id'] ?? null,
                                        'telefono'        => $t['telefono'] ?? '',
                                        'direccion'       => $t['direccion'] ?? '',
                                        'libro'           => $t['titulo'] ?? $t['libro_rel'] ?? '',
                                        'libro_id'        => $t['libro_id'] ?? null,
                                        'autor'           => $t['autor'] ?? '',
                                        'fecha_prestamo'  => isset($t['fecha_prestamo'])
                                          ? str_replace(' ', 'T', substr($t['fecha_prestamo'], 0, 16))
                                          : '',
                                        'fecha_devolucion' => isset($t['fecha_devolucion'])
                                          ? str_replace(' ', 'T', substr($t['fecha_devolucion'], 0, 16))
                                          : '',
                                        'estado'          => $t['estado'] ?? 'En Prestamo',
                                        'categoria_edad'  => $t['categoria_edad'] ?? '',
                                        'observaciones'   => $t['observaciones'] ?? '',
                                        'creado_en'       => $t['creado_en'] ?? '',
                                        'modificado_en'   => $t['modificado_en'] ?? '',
                                      ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>'>
                        <i class="fa-solid fa-pen-to-square"></i>
                      </button>

                      <button
                        class="btn-action-del"
                        title="Eliminar"
                        onclick="onDeleteTicket(<?= (int)($t['id'] ?? 0) ?>,'<?= htmlspecialchars($t['codigo'] ?? '', ENT_QUOTES, 'UTF-8') ?>')">
                        <i class="fa-solid fa-trash-can"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <!-- DEMO SI NO HAY DATOS -->
              <tr data-row>
                <td class="px-3 py-3 fw-semibold">BBPO-0001</td>
                <td class="px-3 py-3">Ana Rodríguez</td>
                <td class="px-3 py-3 text-muted">8888-8888</td>
                <td class="px-3 py-3 text-muted">San Rafael de Poás</td>
                <td class="px-3 py-3">El Señor de los Anillos</td>
                <td class="px-3 py-3 text-muted">J. R. R. Tolkien</td>
                <td class="px-3 py-3 text-muted">2025-10-10 09:00</td>
                <td class="px-3 py-3 text-muted">2025-10-17 09:00</td>
                <td class="px-3 py-3"><span class="badge-state-active">En préstamo</span></td>
                <td class="px-3 py-3 text-muted">2025-10-10</td>
                <td class="px-3 py-3">
                  <div class="d-flex gap-2">
                    <button class="btn-action-edit"
                      data-ticket='{
                      "id":"1",
                      "codigo":"BBPO-0001",
                      "cliente":"Ana Rodríguez",
                      "cliente_id":"1",
                      "telefono":"8888-8888",
                      "direccion":"San Rafael de Poás",
                      "libro":"El Señor de los Anillos",
                      "libro_id":"1",
                      "autor":"J. R. R. Tolkien",
                      "fecha_prestamo":"2025-10-10T09:00",
                      "fecha_devolucion":"2025-10-17T09:00",
                      "estado":"En Prestamo",
                      "categoria_edad":"HA",
                      "observaciones":"Lectura escolar",
                      "creado_en":"2025-10-10 09:00",
                      "modificado_en":"2025-10-10 09:00"
                    }'
                      data-bs-toggle="modal" data-bs-target="#ticketModal">
                      <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button class="btn-action-del" onclick="onDeleteTicket(1,'BBPO-0001')">
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

  <!-- MODAL CREAR/EDITAR TIQUETE -->
  <div class="modal fade modal-ticket" id="ticketModal" tabindex="-1" aria-labelledby="ticketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header border-0 pb-0">
          <div>
            <h5 class="modal-title brand-title" id="ticketModalLabel">Crear Nuevo Tiquete</h5>
            <div id="auditTicket" class="small text-muted d-none"></div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <div class="modal-body">
          <form id="ticketForm" class="needs-validation" novalidate>
            <input type="hidden" id="ticketId" name="id">
            <input type="hidden" name="_csrf" id="csrfField" value="<?= htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="libro_id" id="libro_id">
            <input type="hidden" name="cliente_id" id="cliente_id">

            <div class="row g-3">

              <!-- Cliente -->
              <div class="col-12 col-md-6">
                <label for="cliente" class="form-label">Cliente</label>
                <input
                  id="cliente"
                  name="cliente"
                  type="text"
                  class="form-control"
                  list="clientesOptions"
                  required
                  placeholder="Nombre del cliente">
                <datalist id="clientesOptions">
                  <?php if (!empty($clientes) && is_array($clientes)): ?>
                    <?php foreach ($clientes as $c): ?>
                      <option value="<?= htmlspecialchars($c['nombre'], ENT_QUOTES, 'UTF-8') ?>"></option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </datalist>
                <div class="invalid-feedback">Selecciona el cliente.</div>
              </div>

              <!-- Teléfono -->
              <div class="col-12 col-md-3">
                <label for="telefono" class="form-label">Teléfono</label>
                <input
                  id="telefono"
                  name="telefono"
                  type="text"
                  class="form-control"
                  placeholder="Ej. 8888-8888">
              </div>

              <!-- Dirección -->
              <div class="col-12 col-md-3">
                <label for="direccion" class="form-label">Dirección</label>
                <input
                  id="direccion"
                  name="direccion"
                  type="text"
                  class="form-control"
                  placeholder="Provincia, cantón, distrito">
              </div>

              <!-- Libro -->
              <div class="col-12 col-md-6">
                <label for="libro" class="form-label">Libro</label>
                <input
                  id="libro"
                  name="libro"
                  type="text"
                  class="form-control"
                  list="librosOptions"
                  required
                  placeholder="Título del libro">
                <datalist id="librosOptions">
                  <?php if (!empty($libros) && is_array($libros)): ?>
                    <?php foreach ($libros as $l): ?>
                      <option value="<?= htmlspecialchars($l['titulo'], ENT_QUOTES, 'UTF-8') ?>"></option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </datalist>
                <div class="invalid-feedback">Selecciona el libro.</div>
              </div>

              <!-- Autor -->
              <div class="col-12 col-md-6">
                <label for="autor" class="form-label">Autor</label>
                <input id="autor" name="autor" type="text" class="form-control" placeholder="Autor del libro">
              </div>

              <!-- Categoría edad -->
              <div class="col-12 col-md-6 col-lg-4">
                <label for="categoria_edad" class="form-label">Categoría de edad</label>
                <select id="categoria_edad" name="categoria_edad" class="form-select" required>
                  <option value="" disabled selected>Selecciona...</option>

                  <!-- 0 a 5 años -->
                  <option value="OP">OP – (0 a 5 años)</option>
                  <option value="AP">AP – (0 a 5 años)</option>

                  <!-- 6 a 12 años -->
                  <option value="O">O – (6 a 12 años)</option>
                  <option value="A">A – (6 a 12 años)</option>

                  <!-- 13 a 17 años -->
                  <option value="HJ">HJ – Hombres Jóvenes (13 a 17 años)</option>
                  <option value="MJ">MJ – Mujeres Jóvenes (13 a 17 años)</option>

                  <!-- 18 a 35 años -->
                  <option value="HJU">HJU – Hombres Jóvenes Adultos (18 a 35 años)</option>
                  <option value="MJU">MJU – Mujeres Jóvenes Adultas (18 a 35 años)</option>

                  <!-- 36 a 64 años -->
                  <option value="HA">HA – Hombres Adultos (36 a 64 años)</option>
                  <option value="MA">MA – Mujeres Adultas (36 a 64 años)</option>

                  <!-- 65+ años -->
                  <option value="HAM">HAM – Hombres Adultos Mayores (65+ años)</option>
                  <option value="NAM">NAM – Mujeres Adultas Mayores (65+ años)</option>
                </select>

                <div class="invalid-feedback">Selecciona la categoría de edad.</div>
              </div>

              <!-- Fechas -->
              <div class="col-12 col-md-6 col-lg-4">
                <label for="fecha_prestamo" class="form-label">Fecha y hora de préstamo</label>
                <input id="fecha_prestamo" name="fecha_prestamo" type="datetime-local" class="form-control" required>
                <div class="invalid-feedback">Selecciona la fecha de préstamo.</div>
              </div>

              <div class="col-12 col-md-6 col-lg-4">
                <label for="fecha_devolucion" class="form-label">Fecha y hora de devolución</label>
                <input id="fecha_devolucion" name="fecha_devolucion" type="datetime-local" class="form-control" required>
                <div class="invalid-feedback">Selecciona la fecha de devolución.</div>
              </div>

              <!-- Estado -->
              <div class="col-12 col-md-4">
                <label for="estado" class="form-label">Estado</label>
                <select id="estado" name="estado" class="form-select" required>
                  <!-- Por defecto En Prestamo -->
                  <option value="En Prestamo" selected>En préstamo</option>
                  <option value="Devuelto">Devuelto</option>
                  <option value="Retrasado">Retrasado</option>
                </select>
                <div class="invalid-feedback">Selecciona el estado.</div>
              </div>

              <!-- Observaciones -->
              <div class="col-12 col-md-8">
                <label for="observaciones" class="form-label">Observaciones</label>
                <textarea id="observaciones" name="observaciones" class="form-control" rows="2" placeholder="Notas adicionales (opcional)"></textarea>
              </div>
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

    // Pasar listas PHP -> JS
    window.LIBROS = <?= json_encode($libros ?? [], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    window.CLIENTES = <?= json_encode($clientes ?? [], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

    const libroInput = document.getElementById('libro');
    const libroIdInput = document.getElementById('libro_id');
    const autorInput = document.getElementById('autor');
    const clienteInput = document.getElementById('cliente');
    const clienteIdInput = document.getElementById('cliente_id');

    function syncLibroFields() {
      const titulo = (libroInput.value || '').toLowerCase().trim();
      let found = window.LIBROS.find(l => (l.titulo || '').toLowerCase().trim() === titulo);

      if (found) {
        libroIdInput.value = found.id;
        if (!autorInput.value) {
          autorInput.value = found.autor || '';
        }
      } else {
        // Si no coincide con ninguno, limpiamos el ID
        libroIdInput.value = '';
      }
    }

    function syncClienteFields() {
      const nombre = (clienteInput.value || '').toLowerCase().trim();
      let found = window.CLIENTES.find(c => (c.nombre || '').toLowerCase().trim() === nombre);

      if (found) {
        clienteIdInput.value = found.id;
      } else {
        clienteIdInput.value = '';
      }
    }

    if (libroInput) {
      libroInput.addEventListener('change', syncLibroFields);
      libroInput.addEventListener('blur', syncLibroFields);
    }
    if (clienteInput) {
      clienteInput.addEventListener('change', syncClienteFields);
      clienteInput.addEventListener('blur', syncClienteFields);
    }

    // Export
    document.getElementById('btnExportCsv')?.addEventListener('click', () => {
      window.location.href = '/tiquetes/export/csv';
    });
    document.getElementById('btnExportXlsx')?.addEventListener('click', () => {
      window.location.href = '/tiquetes/export/xlsx';
    });

    // Modal: alta/edición
    const ticketModal = document.getElementById('ticketModal');
    ticketModal.addEventListener('show.bs.modal', event => {
      const btn = event.relatedTarget;
      const form = document.getElementById('ticketForm');
      const audit = document.getElementById('auditTicket');
      const title = document.getElementById('ticketModalLabel');

      form.reset();
      form.classList.remove('was-validated');
      audit.classList.add('d-none');
      audit.textContent = '';
      document.getElementById('ticketId').value = '';
      document.getElementById('libro_id').value = '';
      document.getElementById('cliente_id').value = '';
      document.getElementById('telefono').value = '';
      document.getElementById('direccion').value = '';
      // Estado por defecto En Prestamo
      document.getElementById('estado').value = 'En Prestamo';

      title.textContent = 'Crear Nuevo Tiquete';

      if (btn && btn.dataset.ticket) {
        const data = JSON.parse(btn.dataset.ticket);

        title.textContent = `Editar Tiquete ${data.codigo || data.id || ''}`;

        document.getElementById('ticketId').value = data.id || '';
        document.getElementById('libro_id').value = data.libro_id || '';
        document.getElementById('cliente_id').value = data.cliente_id || '';
        document.getElementById('cliente').value = data.cliente || '';
        document.getElementById('telefono').value = data.telefono || '';
        document.getElementById('direccion').value = data.direccion || '';
        document.getElementById('libro').value = data.libro || '';
        document.getElementById('autor').value = data.autor || '';
        document.getElementById('fecha_prestamo').value = data.fecha_prestamo || '';
        document.getElementById('fecha_devolucion').value = data.fecha_devolucion || '';
        document.getElementById('estado').value = data.estado || 'En Prestamo';
        document.getElementById('categoria_edad').value = data.categoria_edad || '';
        document.getElementById('observaciones').value = data.observaciones || '';

        const creado = data.creado_en ? `Creado: ${data.creado_en}` : '';
        const modif = data.modificado_en ? ` · Modificado: ${data.modificado_en}` : '';
        if (creado || modif) {
          audit.textContent = `${creado}${modif}`;
          audit.classList.remove('d-none');
        }
      }
    });

    // Submit (create/update via fetch, respetando redirecciones del backend)
    document.getElementById('ticketForm').addEventListener('submit', async e => {
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

      const isEdit = !!document.getElementById('ticketId').value;
      const url = isEdit ? '/tiquetes/update' : '/tiquetes/create';

      const fd = new FormData(form);

      try {
        const rsp = await fetch(url, {
          method: 'POST',
          body: fd
        });

        // Si el backend redirige: asumimos éxito normal
        if (rsp.redirected) {
          window.location.href = rsp.url;
          return;
        }

        // Si NO hay redirect, asumimos que vino un JSON con error (por ejemplo libro no disponible)
        let data = null;
        try {
          data = await rsp.json();
        } catch (_) {
          // nada
        }

        await Swal.fire({
          icon: 'error',
          title: data && data.message ? data.message : 'No se pudo guardar el tiquete.',
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

    // Eliminar tiquete
    async function onDeleteTicket(id, codigo) {
      const ok = await Swal.fire({
        icon: 'warning',
        title: '¿Eliminar tiquete?',
        text: codigo || `ID ${id}`,
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
        const rsp = await fetch('/tiquetes/delete', {
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
    window.onDeleteTicket = onDeleteTicket;
  </script>
</body>

</html>
