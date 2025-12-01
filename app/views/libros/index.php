<!doctype html>
<html lang="es" class="light">
<head>
  <meta charset="utf-8">
  <title>BiblioPoás · Libros</title>
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
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
      <h1 class="page-title brand-title h2 m-0">Gestión de Libros</h1>

      <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary d-none d-md-inline-flex" id="btnExportCsv" title="Exportar CSV">
          <i class="fa-solid fa-file-csv me-2"></i> CSV
        </button>
        <button class="btn btn-outline-secondary d-none d-md-inline-flex" id="btnExportXlsx" title="Exportar Excel">
          <i class="fa-regular fa-file-excel me-2"></i> Excel
        </button>
        <button class="btn btn-coral d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#bookModal">
          <i class="fa-solid fa-book-medical"></i> Agregar libro
        </button>
      </div>
    </div>

    <!-- KPIs -->
    <section class="row g-3 mb-4">
      <div class="col-12 col-md-6 col-lg-4">
        <div class="kpi-card p-4">
          <div class="d-flex align-items-center gap-3 mb-2">
            <div class="kpi-icon"><i class="fa-solid fa-book"></i></div>
            <p class="m-0 fw-semibold" style="color: var(--sidebar-text)">Total de Libros</p>
          </div>
          <p class="display-6 fw-bold m-0" style="color: var(--sidebar-text)">
            <?= htmlspecialchars($stats['total'] ?? '0', ENT_QUOTES, 'UTF-8') ?>
          </p>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-4">
        <div class="kpi-card p-4">
          <div class="d-flex align-items-center gap-3 mb-2">
            <div class="kpi-icon"><i class="fa-solid fa-book-open-reader"></i></div>
            <p class="m-0 fw-semibold" style="color: var(--sidebar-text)">Disponibles</p>
          </div>
          <p class="display-6 fw-bold m-0" style="color: var(--sidebar-text)">
            <?= htmlspecialchars($stats['disponible'] ?? '0', ENT_QUOTES, 'UTF-8') ?>
          </p>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-4">
        <div class="kpi-card p-4">
          <div class="d-flex align-items-center gap-3 mb-2">
            <div class="kpi-icon"><i class="fa-solid fa-person-walking-luggage"></i></div>
            <p class="m-0 fw-semibold" style="color: var(--sidebar-text)">Prestados</p>
          </div>
          <p class="display-6 fw-bold m-0" style="color: var(--sidebar-text)">
            <?= htmlspecialchars($stats['prestado'] ?? '0', ENT_QUOTES, 'UTF-8') ?>
          </p>
        </div>
      </div>
    </section>

    <!-- Filtro -->
    <div class="row g-2 align-items-center mb-3">
      <div class="col-12 col-md-4">
        <div class="input-group">
          <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
          <input id="filterInput" type="search" class="form-control"
                 placeholder="Buscar por título, autor, ISBN, DEWEY…">
        </div>
      </div>
      <div class="col-12 col-md-auto ms-auto">
        <span class="text-muted small">Total: <strong id="totalRows"><?= isset($libros) ? count($libros) : 0 ?></strong></span>
      </div>
    </div>

    <!-- Tabla -->
    <div class="block-users">
      <div class="table-responsive">
        <table class="table table-users align-middle mb-0" id="booksTable">
          <thead>
            <tr>
              <th class="px-3 py-3">Título</th>
              <th class="px-3 py-3">Vol/Pte/No/Tomo/Ejemplar</th>
              <th class="px-3 py-3">ISBN</th>
              <th class="px-3 py-3">Clasificación DEWEY</th>
              <th class="px-3 py-3">Autor</th>
              <th class="px-3 py-3">Año</th>
              <th class="px-3 py-3">Categoría</th>
              <th class="px-3 py-3">Etiquetas</th>
              <th class="px-3 py-3 text-center">Cantidad</th>
              <th class="px-3 py-3">Estado</th>
              <th class="px-3 py-3">Creado</th>
              <th class="px-3 py-3">Modificado</th>
              <th class="px-3 py-3">Acciones</th>
            </tr>
          </thead>
          <tbody>
          <?php if (!empty($libros) && is_array($libros)): ?>
            <?php foreach ($libros as $l): ?>
              <tr data-row>
                <td class="px-3 py-3 fw-semibold">
                  <?= htmlspecialchars($l['titulo'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </td>
                <td class="px-3 py-3 text-muted">
                  <?= htmlspecialchars($l['volumen'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </td>
                <td class="px-3 py-3 text-muted">
                  <?= htmlspecialchars($l['isbn'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </td>
                <td class="px-3 py-3 text-muted">
                  <?= htmlspecialchars($l['clasificacion_dewey'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </td>
                <td class="px-3 py-3 text-muted">
                  <?= htmlspecialchars($l['autor'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </td>
                <td class="px-3 py-3 text-muted">
                  <?= htmlspecialchars($l['anio_publicacion'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </td>
                <td class="px-3 py-3 text-muted">
                  <?= htmlspecialchars($l['categoria_nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </td>
                <td class="px-3 py-3 text-muted">
                  <?= htmlspecialchars($l['etiquetas'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </td>
                <td class="px-3 py-3 text-center">
                  <?= htmlspecialchars($l['cantidad'] ?? '0', ENT_QUOTES, 'UTF-8') ?>
                </td>
                <td class="px-3 py-3">
                  <?php
                    $estadoDb = $l['estado'] ?? 'Disponible';
                    $isDisp = ($estadoDb === 'Disponible');
                  ?>
                  <?php if ($isDisp): ?>
                    <span class="badge-state-active">Disponible</span>
                  <?php else: ?>
                    <span class="badge-state-inactive">Prestado</span>
                  <?php endif; ?>
                </td>
                <td class="px-3 py-3 text-muted">
                  <?= htmlspecialchars($l['creado_en'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </td>
                <td class="px-3 py-3 text-muted">
                  <?= htmlspecialchars($l['modificado_en'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </td>
                <td class="px-3 py-3">
                  <div class="d-flex gap-2">
                    <button
                      class="btn-action-edit"
                      title="Editar"
                      data-bs-toggle="modal"
                      data-bs-target="#bookModal"
                      data-book='<?= json_encode([
                        'id'           => (int)($l['id'] ?? 0),
                        'titulo'       => $l['titulo'] ?? '',
                        'volumen'      => $l['volumen'] ?? '',
                        'isbn'         => $l['isbn'] ?? '',
                        'dewey'        => $l['clasificacion_dewey'] ?? '',
                        'autor'        => $l['autor'] ?? '',
                        'anio'         => $l['anio_publicacion'] ?? '',
                        'categoria'    => $l['categoria_id'] ?? '',
                        'etiquetas'    => $l['etiquetas'] ?? '',
                        'cantidad'     => $l['cantidad'] ?? '',
                        'estado'       => strtolower($l['estado'] ?? 'Disponible'),
                        'creado_en'    => $l['creado_en'] ?? '',
                        'modificado_en'=> $l['modificado_en'] ?? '',
                        'observaciones'=> '',
                      ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>'>
                      <i class="fa-solid fa-pen-to-square"></i>
                    </button>

                    <button
                      class="btn-action-del"
                      title="Eliminar"
                      onclick="onDeleteBook(<?= (int)($l['id'] ?? 0) ?>,'<?= htmlspecialchars($l['titulo'] ?? 'Libro', ENT_QUOTES, 'UTF-8') ?>')">
                      <i class="fa-solid fa-trash-can"></i>
                    </button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <!-- Fila demo si aún no hay datos -->
            <tr data-row>
              <th class="px-3 py-3 fw-semibold" scope="row">El Señor de los Anillos</th>
              <td class="px-3 py-3 text-muted">Tomo I / Ej. 2</td>
              <td class="px-3 py-3 text-muted">978-0618640157</td>
              <td class="px-3 py-3 text-muted">863.7 T649e</td>
              <td class="px-3 py-3 text-muted">J. R. R. Tolkien</td>
              <td class="px-3 py-3 text-muted">1954</td>
              <td class="px-3 py-3 text-muted">Fantasía</td>
              <td class="px-3 py-3 text-muted">épico, media-terra</td>
              <td class="px-3 py-3 text-center">3</td>
              <td class="px-3 py-3"><span class="badge-state-active">Disponible</span></td>
              <td class="px-3 py-3 text-muted">2025-09-30 10:12</td>
              <td class="px-3 py-3 text-muted">2025-10-05 14:21</td>
              <td class="px-3 py-3">
                <div class="d-flex gap-2">
                  <button class="btn-action-edit"
                    data-book='{
                      "id":"1",
                      "titulo":"El Señor de los Anillos",
                      "volumen":"Tomo I / Ej. 2",
                      "isbn":"978-0618640157",
                      "dewey":"863.7 T649e",
                      "autor":"J. R. R. Tolkien",
                      "anio":"1954",
                      "categoria":"1",
                      "etiquetas":"épico, media-terra",
                      "cantidad":"3",
                      "estado":"disponible",
                      "creado_en":"2025-09-30 10:12",
                      "modificado_en":"2025-10-05 14:21",
                      "observaciones":""
                    }'
                    title="Editar" data-bs-toggle="modal" data-bs-target="#bookModal">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </button>
                  <button class="btn-action-del" title="Eliminar" onclick="onDeleteBook(1, 'El Señor de los Anillos')">
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

  <!-- Modal Agregar/Editar Libro -->
  <div class="modal fade modal-ticket" id="bookModal" tabindex="-1" aria-labelledby="bookModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header border-0 pb-0">
          <div>
            <h5 class="modal-title brand-title" id="bookModalLabel">Agregar Nuevo Libro</h5>
            <div id="auditInfo" class="small text-muted d-none"></div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <form id="bookForm" class="needs-validation" novalidate>
            <input type="hidden" name="_csrf" id="csrfField" value="<?= htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="id" id="bookId">

            <div class="row g-3">
              <div class="col-12 col-md-6">
                <label for="titulo" class="form-label">Título</label>
                <input id="titulo" name="titulo" type="text" class="form-control" required>
                <div class="invalid-feedback">Ingresa el título.</div>
              </div>
              <div class="col-12 col-md-6">
                <label for="volumen" class="form-label">Vol/Pte/No/Tomo/Ejemplar</label>
                <input id="volumen" name="volumen" type="text" class="form-control" placeholder="p.ej. Tomo I / Ej. 2">
              </div>
              <div class="col-12 col-md-4">
                <label for="isbn" class="form-label">ISBN</label>
                <input id="isbn" name="isbn" type="text" class="form-control">
              </div>
              <div class="col-12 col-md-4">
                <label for="dewey" class="form-label">Clasificación DEWEY</label>
                <input id="dewey" name="dewey" type="text" class="form-control" placeholder="p.ej. 863.7 T649e">
              </div>
              <div class="col-12 col-md-4">
                <label for="anio" class="form-label">Año de publicación</label>
                <input id="anio" name="anio" type="number" class="form-control" min="1000" max="9999">
              </div>
              <div class="col-12 col-md-6">
                <label for="autor" class="form-label">Autor</label>
                <input id="autor" name="autor" type="text" class="form-control" required>
                <div class="invalid-feedback">Ingresa el autor.</div>
              </div>

              <!-- Categoría: ahora sí dropdown con datos de la tabla categorias -->
              <div class="col-12 col-md-6">
                <label for="categoria" class="form-label">Categoría (referencia)</label>
                <select id="categoria" name="categoria_id" class="form-select">
                  <option value="">Sin categoría</option>
                  <?php if (!empty($categorias) && is_array($categorias)): ?>
                    <?php foreach ($categorias as $cat): ?>
                      <option value="<?= (int)$cat['id'] ?>">
                        <?= htmlspecialchars($cat['nombre'], ENT_QUOTES, 'UTF-8') ?>
                      </option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </select>
              </div>

              <div class="col-12">
                <label for="etiquetas" class="form-label">Etiquetas</label>
                <input id="etiquetas" name="etiquetas" type="text" class="form-control" placeholder="Separadas por coma: p.ej. clásico, aventura">
              </div>
              <div class="col-12 col-md-3">
                <label for="cantidad" class="form-label">Cantidad</label>
                <input id="cantidad" name="cantidad" type="number" class="form-control" min="1" value="1" required>
                <div class="invalid-feedback">Ingresa la cantidad (≥1).</div>
              </div>
              <div class="col-12 col-md-3">
                <label for="estado" class="form-label">Estado</label>
                <select id="estado" name="estado" class="form-select" required>
                  <option value="" disabled selected>Selecciona…</option>
                  <option value="disponible">Disponible</option>
                  <option value="prestado">Prestado</option>
                </select>
                <div class="invalid-feedback">Selecciona el estado.</div>
              </div>
              <div class="col-12">
                <label for="observaciones" class="form-label">Observaciones</label>
                <textarea id="observaciones" name="observaciones" class="form-control" rows="3" placeholder="Notas adicionales (opcional)"></textarea>
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
    const tbody       = document.querySelector('#booksTable tbody');
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
      window.location.href = '/libros/export/csv';
    });
    document.getElementById('btnExportXlsx')?.addEventListener('click', () => {
      window.location.href = '/libros/export/xlsx';
    });

    // Modal: alta/edición
    const bookModal = document.getElementById('bookModal');
    bookModal.addEventListener('show.bs.modal', (event) => {
      const btn   = event.relatedTarget;
      const title = document.getElementById('bookModalLabel');
      const form  = document.getElementById('bookForm');
      const audit = document.getElementById('auditInfo');

      form.classList.remove('was-validated');
      form.reset();
      document.getElementById('bookId').value = '';
      audit.classList.add('d-none');
      audit.textContent = '';

      title.textContent = 'Agregar Nuevo Libro';

      if (btn && btn.dataset.book) {
        const data = JSON.parse(btn.dataset.book);

        title.textContent = 'Editar Libro';

        document.getElementById('bookId').value        = data.id || '';
        document.getElementById('titulo').value        = data.titulo || '';
        document.getElementById('volumen').value       = data.volumen || '';
        document.getElementById('isbn').value          = data.isbn || '';
        document.getElementById('dewey').value         = data.dewey || '';
        document.getElementById('autor').value         = data.autor || '';
        document.getElementById('anio').value          = data.anio || '';
        document.getElementById('categoria').value     = data.categoria || '';
        document.getElementById('etiquetas').value     = data.etiquetas || '';
        document.getElementById('cantidad').value      = data.cantidad || 1;
        document.getElementById('estado').value        = data.estado || 'disponible';
        document.getElementById('observaciones').value = data.observaciones || '';

        const creado = data.creado_en ? `Creado: ${data.creado_en}` : '';
        const modif  = data.modificado_en ? ` · Modificado: ${data.modificado_en}` : '';
        if (creado || modif) {
          audit.textContent = `${creado}${modif}`;
          audit.classList.remove('d-none');
        }
      }
    });

    // Submit (create/update via fetch + redirecciones del backend)
    document.getElementById('bookForm').addEventListener('submit', async (e) => {
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

      const isEdit = !!document.getElementById('bookId').value;
      const url    = isEdit ? '/libros/update' : '/libros/create';

      const fd = new FormData(form);

      try {
        const rsp = await fetch(url, {
          method: 'POST',
          body: fd
        });

        if (rsp.redirected) {
          window.location.href = rsp.url;
          return;
        }

        await Swal.fire({
          icon: 'success',
          title: isEdit ? 'Libro actualizado' : 'Libro agregado',
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

    // Eliminar libro
    async function onDeleteBook(id, titulo) {
      const ok = await Swal.fire({
        icon: 'warning',
        title: '¿Eliminar libro?',
        text: titulo,
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
        const rsp = await fetch('/libros/delete', {
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
    window.onDeleteBook = onDeleteBook;
  </script>
</body>
</html>
