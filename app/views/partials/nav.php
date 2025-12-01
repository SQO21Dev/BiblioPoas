<nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom">
  <div class="container">
    <a class="navbar-brand fw-bold" href="/"><i class="fa-solid fa-book"></i> BiblioPoás</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div id="mainNav" class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="/libros">Libros</a></li>
        <li class="nav-item"><a class="nav-link" href="/tiquetes">Tiquetes</a></li>
        <li class="nav-item"><a class="nav-link" href="/clientes">Clientes</a></li>
        <li class="nav-item"><a class="nav-link" href="/reportes">Reportes</a></li>
        <li class="nav-item"><a class="nav-link" href="/logs">Logs</a></li>
        <li class="nav-item"><a class="nav-link" href="/usuarios">Usuarios</a></li>
      </ul>
      <form method="post" action="/logout" class="d-flex">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <button class="btn btn-outline-danger btn-sm">Salir</button>
      </form>
    </div>
  </div>
</nav>
