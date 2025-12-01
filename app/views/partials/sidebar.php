<aside class="sidebar">
  <div>
    <div class="brand">
      <div class="brand-logo" style="background-image:url('/assets/img/logo-bibliopoas.svg');"></div>
      <div class="brand-name">BiblioPoás</div>
    </div>

    <nav class="nav-sidebar mt-3">
      <a class="nav-link <?= ($_SERVER['REQUEST_URI'] === '/dashboard' ? 'active' : '') ?>" href="/dashboard">
        <i class="fa-solid fa-gauge"></i><span>Dashboard</span>
      </a>
      <a class="nav-link <?= (str_starts_with($_SERVER['REQUEST_URI'], '/usuarios') ? 'active' : '') ?>" href="/usuarios">
        <i class="fa-solid fa-users"></i><span>Usuarios</span>
      </a>
      <a class="nav-link <?= (str_starts_with($_SERVER['REQUEST_URI'], '/clientes') ? 'active' : '') ?>" href="/clientes">
        <i class="fa-regular fa-id-card"></i><span>Clientes</span>
      </a>
      <a class="nav-link <?= (str_starts_with($_SERVER['REQUEST_URI'], '/libros') ? 'active' : '') ?>" href="/libros">
        <i class="fa-solid fa-book"></i><span>Libros</span>
      </a>
      <a class="nav-link <?= (str_starts_with($_SERVER['REQUEST_URI'], '/tiquetes') ? 'active' : '') ?>" href="/tiquetes">
        <i class="fa-solid fa-ticket"></i><span>Tiquetes</span>
      </a>
    </nav>
  </div>

  <div class="sidebar-footer">
    <form method="post" action="/logout" class="m-0">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8') ?>">
      <button class="nav-link w-100 text-start border-0 bg-transparent">
        <i class="fa-solid fa-right-from-bracket"></i><span> Cerrar sesión</span>
      </button>
    </form>
  </div>
</aside>
