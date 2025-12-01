<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>BiblioPoás · Cambiar contraseña</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=Nunito:wght@700;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/5152164a0e.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
  <main class="auth-wrapper">
    <div class="auth-card">
      <div class="text-center mb-4">
        <p class="h1 brand-title mb-1">Cambiar contraseña</p>
        <p class="text-subtle">Define tu nueva contraseña para continuar.</p>
      </div>

      <form class="needs-validation" novalidate method="post" action="/reset">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <div class="mb-3">
          <label for="newpass" class="form-label">Nueva contraseña</label>
          <div class="input-group input-group-auth">
            <input id="newpass" name="contrasena" type="password" class="form-control input-xl" placeholder="********" minlength="6" required>
            <button type="button" class="btn btn-eye px-3" onclick="import('/assets/js/app.js').then(m=>m.togglePassword('newpass', this))" aria-label="Mostrar u ocultar contraseña">
              <i class="fa-solid fa-eye"></i>
            </button>
          </div>
          <div class="invalid-feedback">Mínimo 6 caracteres.</div>
        </div>

        <div class="mb-2">
          <label for="newpass2" class="form-label">Confirmar contraseña</label>
          <div class="input-group input-group-auth">
            <input id="newpass2" name="contrasena2" type="password" class="form-control input-xl" placeholder="********" minlength="6" required>
            <button type="button" class="btn btn-eye px-3" onclick="import('/assets/js/app.js').then(m=>m.togglePassword('newpass2', this))" aria-label="Mostrar u ocultar contraseña">
              <i class="fa-solid fa-eye"></i>
            </button>
          </div>
          <div class="invalid-feedback">Deben coincidir.</div>
        </div>

        <div class="d-grid mt-3">
          <button class="btn btn-accent" type="submit">
            <i class="fa-solid fa-key me-1"></i> Guardar
          </button>
        </div>

        <p class="text-center mt-3">
          <a class="btn-link-underline" href="/logout"><i class="fa-solid fa-arrow-left"></i> Salir</a>
        </p>
      </form>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
