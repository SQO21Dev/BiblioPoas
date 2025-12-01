<!doctype html>
<html lang="es" class="light">
<head>
  <meta charset="utf-8">
  <title>BiblioPoás · Logs y Auditoría</title>
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
      <h1 class="page-title brand-title h2 m-0">Logs y Auditoría</h1>
      <div class="d-flex gap-2">
        <button class="btn btn-peach d-flex align-items-center gap-2" onclick="exportLogs('csv')">
          <i class="fa-solid fa-file-csv"></i> CSV
        </button>
        <button class="btn btn-peach d-flex align-items-center gap-2" onclick="exportLogs('xlsx')">
          <i class="fa-regular fa-file-excel"></i> Excel
        </button>
      </div>
    </div>

    <!-- FILTROS -->
    <div class="block-users mb-3">
      <form class="p-3 row g-3" method="get" action="/logs">
        <!-- GET: no ocupamos CSRF aquí -->

        <div class="col-12 col-md-3">
          <label class="form-label">Rango de fechas (inicio)</label>
          <input
            type="date"
            class="form-control"
            id="fini"
            name="fini"
            value="<?= htmlspecialchars($filters['fini'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="col-12 col-md-3">
          <label class="form-label">Rango de fechas (fin)</label>
          <input
            type="date"
            class="form-control"
            id="ffin"
            name="ffin"
            value="<?= htmlspecialchars($filters['ffin'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="col-12 col-md-3">
          <label class="form-label">Entidad</label>
          <select class="form-select" id="entidad" name="entidad">
            <?php $entidadSel = $filters['entidad'] ?? ''; ?>
            <option value="">Todas</option>
            <option value="libro"   <?= $entidadSel === 'libro'   ? 'selected' : '' ?>>libro</option>
            <option value="cliente" <?= $entidadSel === 'cliente' ? 'selected' : '' ?>>cliente</option>
            <option value="usuario" <?= $entidadSel === 'usuario' ? 'selected' : '' ?>>usuario</option>
            <option value="tiquete" <?= $entidadSel === 'tiquete' ? 'selected' : '' ?>>tiquete</option>
            <option value="auth"    <?= $entidadSel === 'auth'    ? 'selected' : '' ?>>auth</option>
          </select>
        </div>

        <div class="col-12 col-md-3">
          <label class="form-label">Acción</label>
          <select class="form-select" id="accion" name="accion">
            <?php $accionSel = $filters['accion'] ?? ''; ?>
            <option value="">Todas</option>
            <option value="crear"           <?= $accionSel === 'crear'           ? 'selected' : '' ?>>crear</option>
            <option value="editar"          <?= $accionSel === 'editar'          ? 'selected' : '' ?>>editar</option>
            <option value="eliminar"        <?= $accionSel === 'eliminar'        ? 'selected' : '' ?>>eliminar</option>
            <option value="login"           <?= $accionSel === 'login'           ? 'selected' : '' ?>>login</option>
            <option value="logout"          <?= $accionSel === 'logout'          ? 'selected' : '' ?>>logout</option>
            <option value="reset_password"  <?= $accionSel === 'reset_password'  ? 'selected' : '' ?>>reset_password</option>
            <option value="cambio_estado"   <?= $accionSel === 'cambio_estado'   ? 'selected' : '' ?>>cambio_estado</option>
          </select>
        </div>

        <div class="col-12 d-flex justify-content-end">
          <button type="submit" class="btn btn-accent">Aplicar filtros</button>
        </div>
      </form>
    </div>

    <!-- TABLA DE LOGS -->
    <div class="block-users">
      <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
        <div class="small text-muted">
          Mostrando <?= htmlspecialchars((string)($total ?? 0), ENT_QUOTES, 'UTF-8') ?>
          de <?= htmlspecialchars((string)($total ?? 0), ENT_QUOTES, 'UTF-8') ?>
        </div>
        <div class="d-flex gap-2">
          <button class="btn btn-sm btn-outline-secondary" title="Refrescar" onclick="refreshLogs()">
            <i class="fa-solid fa-rotate"></i>
          </button>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-users align-middle mb-0">
          <thead>
            <tr>
              <th class="px-3 py-3">Fecha</th>
              <th class="px-3 py-3">Usuario</th>
              <th class="px-3 py-3">Rol</th>
              <th class="px-3 py-3">Acción</th>
              <th class="px-3 py-3">Entidad</th>
              <th class="px-3 py-3">Descripción</th>
              <th class="px-3 py-3">Resultado</th>
            </tr>
          </thead>
          <tbody id="logsBody">
            <?php if (!empty($logs) && is_array($logs)): ?>
              <?php foreach ($logs as $log): ?>
                <tr>
                  <td class="px-3 py-3 text-muted">
                    <?= htmlspecialchars($log['fecha_evento'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                  </td>
                  <td class="px-3 py-3">
                    <?= htmlspecialchars($log['usuario_actor'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                  </td>
                  <td class="px-3 py-3">
                    <?= htmlspecialchars($log['rol'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                  </td>
                  <td class="px-3 py-3">
                    <?= htmlspecialchars($log['accion'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                  </td>
                  <td class="px-3 py-3">
                    <?= htmlspecialchars($log['entidad'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                  </td>
                  <td class="px-3 py-3">
                    <?= htmlspecialchars($log['descripcion'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                  </td>
                  <td class="px-3 py-3">
                    <?php
                      $resultado = trim((string)($log['resultado'] ?? ''));
                      if ($resultado === '') {
                        $resultado = 'ok';
                      }
                      if (strtolower($resultado) === 'ok') {
                        $badgeClass = 'badge-state-active';
                      } elseif (strtolower($resultado) === 'error') {
                        $badgeClass = 'badge-state-inactive';
                      } else {
                        $badgeClass = 'badge-state-neutral';
                      }
                    ?>
                    <span class="<?= $badgeClass ?>">
                      <?= htmlspecialchars($resultado, ENT_QUOTES, 'UTF-8') ?>
                    </span>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" class="px-3 py-4 text-center text-muted">
                  No hay registros de auditoría para los filtros seleccionados.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </main>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Sidebar loader -->
  <script src="/assets/js/components.js"></script>

  <script>
    // Cargar sidebar
    loadComponent('#sidebar-container', '/components/sidebar.html');

    function exportLogs(fmt) {
      // Conserva filtros en la exportación
      const params = new URLSearchParams(window.location.search);
      const query  = params.toString();
      let url = '/logs/export/' + fmt;
      if (query) {
        url += '?' + query;
      }
      window.location.href = url;
    }

    function refreshLogs() {
      window.location.reload();
    }
  </script>
</body>
</html>
