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

  <!-- Tom Select (para SELECT con buscador) -->
  <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">

  <!-- Estilos propios -->
  <link rel="stylesheet" href="/assets/css/style.css">

  <!-- (Opcional) Estilo extra para que "Devuelto" SIEMPRE tenga color aunque tu CSS no tenga badge-state-neutral -->
  <style>
    /* Si ya existe en tu CSS, esto no estorba; si no existe, lo define. */
    .badge-state-neutral{
      display:inline-flex;
      align-items:center;
      padding:.35rem .6rem;
      border-radius:999px;
      font-weight:700;
      font-size:.85rem;
      background:#e8f5ee;
      color:#1f7a4f;
      border:1px solid rgba(31,122,79,.15);
    }
  </style>
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

    <!-- FILTROS + PAGINACION -->
    <div class="row g-2 align-items-end mb-3">
      <div class="col-12 col-md-4">
        <label for="monthFilter" class="form-label small mb-1">Filtrar por mes</label>
        <input id="monthFilter" type="month" class="form-control" placeholder="YYYY-MM">
      </div>

      <div class="col-12 col-md-4">
        <label class="form-label small mb-1">Rango de fechas</label>
        <div class="d-flex gap-2">
          <input id="fromDate" type="date" class="form-control" title="Desde">
          <input id="toDate" type="date" class="form-control" title="Hasta">
        </div>
      </div>

      <div class="col-12 col-md-4 d-flex flex-wrap gap-2 align-items-end justify-content-md-end">
        <div class="ms-md-auto">
          <label for="pageSize" class="form-label small mb-1">Tiquetes por página</label>
          <select id="pageSize" class="form-select">
            <option value="10" selected>10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
        </div>

        <div class="text-muted small ms-md-2">
          Mostrando: <strong id="totalRows">0</strong>
        </div>
      </div>
    </div>

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
        <table class="table table-users align-middle mb-0" id="ticketsTable">
          <thead>
            <tr>
              <th class="px-3 py-3">Código</th>
              <th class="px-3 py-3">Cliente</th>
              <th class="px-3 py-3">Teléfono</th>
              <th class="px-3 py-3">Libro</th>
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
                <?php
                  // Para filtros en front: guardamos fecha_prestamo en data-fecha (YYYY-MM-DD)
                  // Si viene como "YYYY-MM-DD HH:MM:SS", se toma la parte de fecha.
                  $fpRaw = (string)($t['fecha_prestamo'] ?? '');
                  $fpDateOnly = $fpRaw !== '' ? substr($fpRaw, 0, 10) : '';
                ?>
                <tr data-row data-fecha-prestamo="<?= htmlspecialchars($fpDateOnly, ENT_QUOTES, 'UTF-8') ?>">
                  <td class="px-3 py-3 fw-semibold">
                    <?= htmlspecialchars($t['codigo'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                  </td>

                  <td class="px-3 py-3">
                    <?= htmlspecialchars($t['nombre_cliente'] ?? $t['cliente_rel'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                  </td>

                  <td class="px-3 py-3 text-muted">
                    <?= htmlspecialchars($t['telefono'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                  </td>

                  <td class="px-3 py-3">
                    <?= htmlspecialchars($t['titulo'] ?? $t['libro_rel'] ?? '', ENT_QUOTES, 'UTF-8') ?>
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
                      // IMPORTANTE: aquí garantizamos que sí lleve "badge-state-neutral"
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
                        title="Editar (pendiente replicar edición)"
                        type="button"
                        data-bs-toggle="modal"
                        data-bs-target="#ticketModal"
                        data-ticket='<?= json_encode([
                                        'id'              => (int)($t['id'] ?? 0),
                                        'codigo'          => $t['codigo'] ?? '',
                                        'cliente'         => $t['nombre_cliente'] ?? $t['cliente_rel'] ?? '',
                                        'telefono'        => $t['telefono'] ?? '',
                                        'direccion'       => $t['direccion'] ?? '',
                                        'libro_id'        => $t['libro_id'] ?? null,
                                        'libro'           => $t['titulo'] ?? $t['libro_rel'] ?? '',
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
              <tr data-row>
                <td colspan="9" class="px-3 py-4 text-center text-muted">
                  No hay tiquetes para mostrar.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Controles paginación -->
      <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between px-3 py-3 border-top">
        <div class="small text-muted">
          Página <strong id="pageInfo">1</strong>
        </div>

        <nav aria-label="Paginación de tiquetes">
          <ul class="pagination mb-0" id="pagination"></ul>
        </nav>
      </div>
    </div>
  </main>

  <!-- MODAL CREAR TIQUETE (Replica Dashboard) -->
  <div class="modal fade modal-ticket" id="ticketModal" tabindex="-1"
    aria-labelledby="ticketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header border-0 pb-0">
          <div>
            <h5 class="modal-title brand-title" id="ticketModalLabel">Crear nuevo tiquete</h5>
            <div id="auditTicket" class="small text-muted d-none"></div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <div class="modal-body">
          <form
            id="ticketCreateForm"
            class="needs-validation"
            novalidate
            method="post"
            action="/tiquetes/create">

            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="estado" value="En Prestamo">

            <div class="row g-3 mb-3">
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

              <div class="col-12 col-md-6">
                <label for="tc_telefono" class="form-label">Teléfono</label>
                <input
                  id="tc_telefono"
                  name="telefono"
                  type="text"
                  class="form-control"
                  placeholder="Ej. 8888-8888">
              </div>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-12">
                <label for="tc_direccion" class="form-label">Dirección</label>
                <textarea
                  id="tc_direccion"
                  name="direccion"
                  class="form-control"
                  placeholder="Provincia, cantón, distrito"></textarea>
              </div>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-12 col-md-6">
                <label for="tc_libro_id" class="form-label">Libro</label>
                <select
                  id="tc_libro_id"
                  name="libro_id"
                  class="form-select"
                  required>
                  <option value=""></option>

                  <?php if (!empty($libros) && is_array($libros)): ?>
                    <?php foreach ($libros as $lib): ?>
                      <?php
                      $titulo  = (string)($lib['titulo'] ?? '');
                      $autor   = (string)($lib['autor'] ?? '');
                      $volumen = (string)($lib['volumen'] ?? '');
                      $label   = $titulo;
                      if ($volumen !== '') {
                        $label .= ' · ' . $volumen;
                      }
                      ?>
                      <option
                        value="<?= htmlspecialchars((string)($lib['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        data-titulo="<?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?>"
                        data-autor="<?= htmlspecialchars($autor, ENT_QUOTES, 'UTF-8') ?>"
                        data-volumen="<?= htmlspecialchars($volumen, ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                      </option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </select>
                <div class="invalid-feedback">Selecciona un libro.</div>

                <!-- Hidden para título que espera el controlador -->
                <input type="hidden" id="tc_libro_titulo" name="libro">
              </div>

              <div class="col-12 col-md-6">
                <label for="tc_autor" class="form-label">Autor</label>
                <input
                  id="tc_autor"
                  name="autor"
                  type="text"
                  class="form-control"
                  placeholder="">
              </div>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-12 col-md-6 col-lg-4">
                <label for="tc_categoria_edad" class="form-label">Categoría de edad</label>
                <select id="tc_categoria_edad" name="categoria_edad" class="form-select" required>
                  <option value="" disabled selected>Selecciona...</option>
                  <option value="OP">OP – Hombres (0 a 5 años)</option>
                  <option value="AP">AP – Mujeres (0 a 5 años)</option>
                  <option value="O">O – Hombres (6 a 12 años)</option>
                  <option value="A">A – Mujeres (6 a 12 años)</option>
                  <option value="HJ">HJ – Hombres Jóvenes (13 a 17 años)</option>
                  <option value="MJ">MJ – Mujeres Jóvenes (13 a 17 años)</option>
                  <option value="HJU">HJU – Hombres Jóvenes Adultos (18 a 35 años)</option>
                  <option value="MJU">MJU – Mujeres Jóvenes Adultas (18 a 35 años)</option>
                  <option value="HA">HA – Hombres Adultos (36 a 64 años)</option>
                  <option value="MA">MA – Mujeres Adultas (36 a 64 años)</option>
                  <option value="HAM">HAM – Hombres Adultos Mayores (65+ años)</option>
                  <option value="NAM">NAM – Mujeres Adultas Mayores (65+ años)</option>
                </select>
                <div class="invalid-feedback">Selecciona la categoría de edad.</div>
              </div>

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
            </div>

            <div class="row g-3">
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

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Tom Select JS -->
  <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

  <!-- Sidebar loader -->
  <script src="/assets/js/components.js"></script>

  <script>
    // Cargar sidebar
    loadComponent('#sidebar-container', '/components/sidebar.html');

    // Export
    document.getElementById('btnExportCsv')?.addEventListener('click', () => {
      window.location.href = '/tiquetes/export/csv';
    });
    document.getElementById('btnExportXlsx')?.addEventListener('click', () => {
      window.location.href = '/tiquetes/export/xlsx';
    });

    // ----------------------------
    // PAGINACIÓN + FILTRO POR MES/RANGO (FRONT)
    // ----------------------------
    const monthFilter     = document.getElementById('monthFilter');
    const fromDate        = document.getElementById('fromDate');
    const toDate          = document.getElementById('toDate');
    const pageSizeSelect  = document.getElementById('pageSize');

    const table           = document.getElementById('ticketsTable');
    const tbody           = table ? table.querySelector('tbody') : null;

    const totalRowsEl     = document.getElementById('totalRows');
    const paginationEl    = document.getElementById('pagination');
    const pageInfoEl      = document.getElementById('pageInfo');

    let currentPage = 1;
    let pageSize = 10;

    function getAllRows() {
      if (!tbody) return [];
      return Array.from(tbody.querySelectorAll('tr[data-row]'))
        .filter(tr => tr.querySelectorAll('td').length > 0); // excluye fila "no hay"
    }

    function parseISODateOnly(s) {
      // Espera "YYYY-MM-DD"
      if (!s || typeof s !== 'string' || s.length < 10) return null;
      const y = parseInt(s.slice(0,4), 10);
      const m = parseInt(s.slice(5,7), 10);
      const d = parseInt(s.slice(8,10), 10);
      if (!Number.isFinite(y) || !Number.isFinite(m) || !Number.isFinite(d)) return null;
      // Date en UTC para comparar sin líos de zona horaria
      return new Date(Date.UTC(y, m-1, d, 0, 0, 0));
    }

    function rowMatchesDateFilters(tr) {
      const fp = (tr.getAttribute('data-fecha-prestamo') || '').trim(); // YYYY-MM-DD
      const fpDt = parseISODateOnly(fp);
      if (!fpDt) return true; // si no hay fecha, no filtramos esa fila

      const fromVal = (fromDate?.value || '').trim(); // YYYY-MM-DD
      const toVal   = (toDate?.value || '').trim();   // YYYY-MM-DD
      const monthVal = (monthFilter?.value || '').trim(); // YYYY-MM

      // Si hay rango (desde/hasta), prioriza el rango
      if (fromVal || toVal) {
        const fromDt = fromVal ? parseISODateOnly(fromVal) : null;
        const toDt   = toVal ? parseISODateOnly(toVal) : null;

        if (fromDt && fpDt < fromDt) return false;
        if (toDt) {
          // incluye el día completo "hasta"
          const toEnd = new Date(Date.UTC(toDt.getUTCFullYear(), toDt.getUTCMonth(), toDt.getUTCDate(), 23, 59, 59));
          if (fpDt > toEnd) return false;
        }
        return true;
      }

      // Si NO hay rango, y hay mes seleccionado => filtra por mes
      if (monthVal) {
        // monthVal: "YYYY-MM"
        const ym = monthVal;
        return fp.slice(0, 7) === ym;
      }

      // Sin filtros
      return true;
    }

    function getFilteredRows() {
      const rows = getAllRows();
      return rows.filter(tr => rowMatchesDateFilters(tr));
    }

    function clampPage(page, totalPages) {
      if (totalPages <= 1) return 1;
      if (page < 1) return 1;
      if (page > totalPages) return totalPages;
      return page;
    }

    function makePageItem(page, current) {
      const li = document.createElement('li');
      li.className = 'page-item' + (page === current ? ' active' : '');
      li.innerHTML = `<a class="page-link" href="#">${page}</a>`;
      li.addEventListener('click', (e) => {
        e.preventDefault();
        currentPage = page;
        applyFilterAndPagination();
      });
      return li;
    }

    function makeEllipsis() {
      const li = document.createElement('li');
      li.className = 'page-item disabled';
      li.innerHTML = `<span class="page-link">…</span>`;
      return li;
    }

    function renderPagination(totalItems) {
      const totalPages = Math.max(1, Math.ceil(totalItems / pageSize));
      currentPage = clampPage(currentPage, totalPages);

      paginationEl.innerHTML = '';

      // Prev
      const liPrev = document.createElement('li');
      liPrev.className = 'page-item' + (currentPage === 1 ? ' disabled' : '');
      liPrev.innerHTML = `<a class="page-link" href="#" aria-label="Anterior">&laquo;</a>`;
      liPrev.addEventListener('click', (e) => {
        e.preventDefault();
        if (currentPage === 1) return;
        currentPage--;
        applyFilterAndPagination();
      });
      paginationEl.appendChild(liPrev);

      const windowSize = 5;
      const half = Math.floor(windowSize / 2);
      let start = Math.max(1, currentPage - half);
      let end = Math.min(totalPages, start + windowSize - 1);
      start = Math.max(1, end - windowSize + 1);

      if (start > 1) {
        paginationEl.appendChild(makePageItem(1, currentPage));
        if (start > 2) paginationEl.appendChild(makeEllipsis());
      }

      for (let p = start; p <= end; p++) {
        paginationEl.appendChild(makePageItem(p, currentPage));
      }

      if (end < totalPages) {
        if (end < totalPages - 1) paginationEl.appendChild(makeEllipsis());
        paginationEl.appendChild(makePageItem(totalPages, currentPage));
      }

      // Next
      const liNext = document.createElement('li');
      liNext.className = 'page-item' + (currentPage === totalPages ? ' disabled' : '');
      liNext.innerHTML = `<a class="page-link" href="#" aria-label="Siguiente">&raquo;</a>`;
      liNext.addEventListener('click', (e) => {
        e.preventDefault();
        if (currentPage === totalPages) return;
        currentPage++;
        applyFilterAndPagination();
      });
      paginationEl.appendChild(liNext);

      pageInfoEl.textContent = `${currentPage} de ${totalPages}`;
    }

    function applyFilterAndPagination() {
      const allRows = getAllRows();
      const filtered = getFilteredRows();

      // Oculta todas
      allRows.forEach(tr => tr.style.display = 'none');

      const totalItems = filtered.length;
      const totalPages = Math.max(1, Math.ceil(totalItems / pageSize));
      currentPage = clampPage(currentPage, totalPages);

      const startIdx = (currentPage - 1) * pageSize;
      const endIdx = startIdx + pageSize;
      const pageRows = filtered.slice(startIdx, endIdx);

      pageRows.forEach(tr => tr.style.display = '');

      totalRowsEl.textContent = String(totalItems);
      renderPagination(totalItems);
    }

    // Eventos filtros
    monthFilter?.addEventListener('change', () => {
      // Si eligen mes, limpiamos rango para evitar confusión
      if (monthFilter.value) {
        if (fromDate) fromDate.value = '';
        if (toDate) toDate.value = '';
      }
      currentPage = 1;
      applyFilterAndPagination();
    });

    fromDate?.addEventListener('change', () => {
      // Si usan rango, limpiamos mes
      if (fromDate.value || (toDate && toDate.value)) {
        if (monthFilter) monthFilter.value = '';
      }
      currentPage = 1;
      applyFilterAndPagination();
    });

    toDate?.addEventListener('change', () => {
      if ((fromDate && fromDate.value) || toDate.value) {
        if (monthFilter) monthFilter.value = '';
      }
      currentPage = 1;
      applyFilterAndPagination();
    });

    pageSizeSelect?.addEventListener('change', () => {
      const val = parseInt(pageSizeSelect.value, 10);
      pageSize = Number.isFinite(val) && val > 0 ? val : 10;
      currentPage = 1;
      applyFilterAndPagination();
      try { localStorage.setItem('tickets_page_size', pageSizeSelect.value); } catch (_) {}
    });

    (function initPagination() {
      try {
        const saved = localStorage.getItem('tickets_page_size');
        if (saved) {
          const s = parseInt(saved, 10);
          if (Number.isFinite(s) && s > 0) {
            pageSize = s;
            pageSizeSelect.value = String(s);
          }
        }
      } catch (_) {}

      // Sugerencia útil: por defecto, setear mes actual automáticamente
      // (si no quieres esto, comenta estas líneas)
      try {
        const now = new Date();
        const y = now.getFullYear();
        const m = String(now.getMonth() + 1).padStart(2, '0');
        // monthFilter.value = `${y}-${m}`; // descomentá si querés iniciar filtrado al mes actual
      } catch (_) {}

      applyFilterAndPagination();
    })();

    // ----------------------------
    // MODAL CREAR (igual que tenías)
    // ----------------------------

    // Prefill fechas del modal de creación (igual que Dashboard)
    (function() {
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
        if (fd.value < fp.value) fd.value = fp.value;
      });
    })();

    // TomSelect para libros (igual que Dashboard)
    (function() {
      const sel = document.getElementById('tc_libro_id');
      if (!sel || sel.tomselect) return;

      const ts = new TomSelect(sel, {
        create: false,
        allowEmptyOption: true,
        placeholder: 'Selecciona un libro disponible…',
        searchField: ['text'],
        maxOptions: 5000,
        closeAfterSelect: true,
        openOnFocus: true,
        items: [],
        onInitialize: function() {
          this.clear(true);
        }
      });

      ts.on('focus', () => {
        ts.open();
        ts.control_input.value = '';
      });
    })();

    // Libro -> título (hidden) y autor (input) (igual que Dashboard)
    (function() {
      const sel = document.getElementById('tc_libro_id');
      const tituloHidden = document.getElementById('tc_libro_titulo');
      const autorInput = document.getElementById('tc_autor');
      if (!sel) return;

      const syncFromOption = () => {
        const opt = sel.selectedOptions && sel.selectedOptions[0] ? sel.selectedOptions[0] : null;
        if (!opt) return;

        const titulo = opt.getAttribute('data-titulo') || '';
        const autor = opt.getAttribute('data-autor') || '';

        if (tituloHidden) tituloHidden.value = titulo;
        if (autorInput && autor !== '') autorInput.value = autor;
      };

      sel.addEventListener('change', syncFromOption);

      const modal = document.getElementById('ticketModal');
      if (modal) {
        modal.addEventListener('shown.bs.modal', () => {
          document.getElementById('ticketModalLabel').textContent = 'Crear nuevo tiquete';
          document.getElementById('auditTicket').classList.add('d-none');
          document.getElementById('auditTicket').textContent = '';
          syncFromOption();

          const cliente = document.getElementById('tc_cliente');
          if (cliente) cliente.focus();
        });
      }
    })();

    // Validación form creación (igual que Dashboard)
    (function() {
      const form = document.getElementById('ticketCreateForm');
      if (!form) return;

      form.addEventListener('submit', function(e) {
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
