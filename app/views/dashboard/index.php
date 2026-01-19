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

  <!-- Chart.js para gráficos -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <!-- Tom Select (para SELECT con buscador) -->
  <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">

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

    <!-- FILTRO POR FECHAS -->
    <section class="mb-3">
      <form method="get" action="/dashboard" class="row g-2 align-items-end">
        <div class="col-12 col-md-3">
          <label for="f_desde" class="form-label small mb-1">Desde (fecha de préstamo)</label>
          <input
            type="date"
            id="f_desde"
            name="from"
            class="form-control"
            value="<?= htmlspecialchars($fromFilter ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="col-12 col-md-3">
          <label for="f_hasta" class="form-label small mb-1">Hasta</label>
          <input
            type="date"
            id="f_hasta"
            name="to"
            class="form-control"
            value="<?= htmlspecialchars($toFilter ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="col-12 col-md-6 d-flex gap-2 mt-2 mt-md-0 align-items-center">
          <button type="submit" class="btn btn-outline-secondary">
            Aplicar filtro
          </button>

          <?php if (!empty($fromFilter) || !empty($toFilter)): ?>
            <a href="/dashboard" class="btn btn-link">
              Quitar filtro
            </a>
            <span class="small text-muted">
              Filtro activo
              <?= $fromFilter ? ('desde ' . htmlspecialchars($fromFilter, ENT_QUOTES, 'UTF-8')) : '' ?>
              <?= ($fromFilter && $toFilter) ? ' · ' : '' ?>
              <?= $toFilter ? ('hasta ' . htmlspecialchars($toFilter, ENT_QUOTES, 'UTF-8')) : '' ?>
            </span>
          <?php else: ?>
            <a href="/dashboard" class="btn btn-link">
              Quitar filtro
            </a>
          <?php endif; ?>

          <?php if (($totalPeriodoTiquetes ?? 0) > 0): ?>
            <div class="ms-auto small text-muted d-none d-md-block">
              Tiquetes en el período: <strong><?= (int)$totalPeriodoTiquetes ?></strong>
            </div>
          <?php endif; ?>
        </div>
      </form>
    </section>

    <!-- GRÁFICOS -->
    <section class="row g-3 mb-4">
      <!-- Pie chart por categoría de edad -->
      <div class="col-12 col-lg">
        <div class="kpi-card chart-card p-4 h-100">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="m-0">Distribución por categoría de edad</h6>
            <small class="text-muted">
              Período:
              <?php if ($fromFilter || $toFilter): ?>
                <?= htmlspecialchars($fromFilter ?? '...', ENT_QUOTES, 'UTF-8') ?>
                –
                <?= htmlspecialchars($toFilter ?? '...', ENT_QUOTES, 'UTF-8') ?>
              <?php else: ?>
                Mes actual
              <?php endif; ?>
            </small>
          </div>
          <canvas id="chartCategoriaEdad"></canvas>
          <?php if (empty($chartCategoria)): ?>
            <p class="text-muted small mt-3 mb-0">
              No hay tiquetes registrados en el período seleccionado.
            </p>
          <?php endif; ?>
        </div>
      </div>

      <!-- Gráfico adicional: estados -->
      <div class="col-12 col-lg">
        <div class="kpi-card chart-card p-4 h-100">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="m-0">Tiquetes por estado</h6>
            <small class="text-muted">Mismo período</small>
          </div>
          <canvas id="chartEstados"></canvas>
          <?php if (empty($chartEstados)): ?>
            <p class="text-muted small mt-3 mb-0">
              No hay tiquetes registrados en el período seleccionado.
            </p>
          <?php endif; ?>
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
                  type="text"
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
                        value="<?= htmlspecialchars((string)$lib['id'], ENT_QUOTES, 'UTF-8') ?>"
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

  <!-- MODAL RÁPIDO PARA TIQUETE -->
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
            <input type="hidden" id="quickTicketId" name="id">
            <input type="hidden" id="quickAccion" name="accion">

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
                  Devolver Libro
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

  <!-- Tom Select JS -->
  <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

  <!-- Sidebar loader + helpers -->
  <script src="/assets/js/components.js"></script>

  <script>
    loadComponent('#sidebar-container', '/components/sidebar.html');

    window.CHART_CATEGORIA_EDAD = <?= json_encode($chartCategoria ?? [], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    window.CHART_ESTADOS = <?= json_encode($chartEstados ?? [], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

    function buildExportUrl(base) {
      const from = document.getElementById('f_desde')?.value || '';
      const to = document.getElementById('f_hasta')?.value || '';
      const params = new URLSearchParams();
      if (from) params.append('from', from);
      if (to) params.append('to', to);
      const qs = params.toString();
      return qs ? `${base}?${qs}` : base;
    }

    document.getElementById('btnExportCsv')?.addEventListener('click', () => {
      window.location.href = buildExportUrl('/tiquetes/export/csv');
    });
    document.getElementById('btnExportXlsx')?.addEventListener('click', () => {
      window.location.href = buildExportUrl('/tiquetes/export/xlsx');
    });

    // === Gráfico: categorías de edad (Pie) ===
    (function() {
      const data = window.CHART_CATEGORIA_EDAD || [];
      if (!data.length) return;

      const ctx = document.getElementById('chartCategoriaEdad');
      if (!ctx) return;

      const codigos = data.map(d => d.codigo ?? d.label);
      const descs = data.map(d => d.descripcion ?? d.label);
      const values = data.map(d => parseInt(d.cantidad, 10) || 0);

      new Chart(ctx, {
        type: 'pie',
        data: {
          labels: codigos,
          datasets: [{
            data: values
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: 'bottom'
            },
            tooltip: {
              callbacks: {
                label: function(context) {
                  const total = values.reduce((a, b) => a + b, 0);
                  const val = context.parsed;
                  const pct = total ? ((val * 100) / total).toFixed(1) : 0;
                  const idx = context.dataIndex;
                  return `${codigos[idx]} – ${descs[idx]}: ${val} (${pct}%)`;
                }
              }
            }
          }
        }
      });
    })();

    // === Gráfico: estados (Bar) ===
    (function() {
      const data = window.CHART_ESTADOS || [];
      if (!data.length) return;

      const ctx = document.getElementById('chartEstados');
      if (!ctx) return;

      const labels = data.map(d => d.label);
      const values = data.map(d => parseInt(d.cantidad, 10) || 0);

      new Chart(ctx, {
        type: 'bar',
        data: {
          labels,
          datasets: [{
            data: values
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                precision: 0
              }
            }
          }
        }
      });
    })();

    // Prefill fechas del modal de creación
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
          // Asegura que NO seleccione nada al iniciar
          this.clear(true);
        }
      });

      // Cuando el usuario hace foco, abrimos y limpiamos el input de búsqueda
      ts.on('focus', () => {
        ts.open();
        ts.control_input.value = '';
      });
    })();


    // Libro -> título (hidden) y autor (input)
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

      // Si el modal abre y ya hay selección por defecto
      const modal = document.getElementById('createTicketModal');
      if (modal) {
        modal.addEventListener('shown.bs.modal', () => {
          syncFromOption();
          const cliente = document.getElementById('tc_cliente');
          if (cliente) cliente.focus();
          // const ts = sel.tomselect;
          // if (ts) ts.focus();
        });
      }
    })();

    // Modal rápido
    const ticketQuickModal = document.getElementById('ticketQuickModal');
    const quickForm = document.getElementById('ticketQuickForm');
    const infoDiv = document.getElementById('ticketQuickInfo');

    if (ticketQuickModal) {
      ticketQuickModal.addEventListener('show.bs.modal', event => {
        const btn = event.relatedTarget;
        const data = btn && btn.dataset.ticket ? JSON.parse(btn.dataset.ticket) : null;

        quickForm.classList.remove('was-validated');
        document.getElementById('quickAccion').value = '';
        document.getElementById('quickTicketId').value = data?.id ?? '';
        document.getElementById('quickCodigo').value = data?.codigo ?? '';
        document.getElementById('quickTitulo').value = data?.titulo ?? '';
        document.getElementById('quickCliente').value = data?.cliente ?? '';
        document.getElementById('quickEstado').value = data?.estado ?? '';

        const raw = data?.fecha_devolucion ?? '';
        let dt = '';
        if (raw) dt = raw.replace(' ', 'T').substring(0, 16);
        document.getElementById('quickFechaDev').value = dt;

        infoDiv.textContent = data?.codigo ? `ID interno: ${data.id} · Código: ${data.codigo}` : '';
      });
    }

    document.getElementById('btnCerrarTiquete')?.addEventListener('click', async () => {
      const id = document.getElementById('quickTicketId').value;
      if (!id) return;

      const confirmRes = await Swal.fire({
        icon: 'warning',
        title: 'Devolver libro',
        text: 'El tiquete pasará a estado Devuelto y el libro quedará disponible.',
        showCancelButton: true,
        confirmButtonText: 'Sí, devolver',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#ec6d13'
      });

      if (!confirmRes.isConfirmed) return;
      document.getElementById('quickAccion').value = 'cerrar';
      submitQuickForm();
    });

    if (quickForm) {
      quickForm.addEventListener('submit', e => {
        e.preventDefault();
        document.getElementById('quickAccion').value = 'actualizar_fecha';
        submitQuickForm();
      });
    }

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
        try {
          data = await rsp.json();
        } catch (_) {}

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

    // Validación form creación
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
  </script>
</body>

</html>