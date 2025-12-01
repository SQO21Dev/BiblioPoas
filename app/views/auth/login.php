<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>BiblioPoás - Iniciar sesión</title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=Nunito:wght@700;900&display=swap" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="/assets/css/style.css"/>

  <!-- SweetAlert2 (solo lo usamos si hay error) -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <main class="auth-wrapper">
    <div class="auth-card">

      <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success" role="alert">
          <?= htmlspecialchars($_SESSION['flash_success'], ENT_QUOTES, 'UTF-8'); ?>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
      <?php endif; ?>

      <div class="text-center mb-4">
        <p class="h1 brand-title mb-1">BiblioPoás</p>
        <p class="text-subtle">Inicia sesión para continuar</p>
      </div>

      <form class="needs-validation" novalidate method="post" action="/login">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <div class="mb-3">
          <label for="user" class="form-label">Usuario</label>
          <input id="user" name="usuario" type="text" class="form-control input-xl" placeholder="Ingresa tu usuario" required>
          <div class="invalid-feedback">Este campo es obligatorio.</div>
        </div>

        <div class="mb-2">
          <label for="password" class="form-label">Contraseña</label>
          <div class="input-group input-group-auth">
            <input id="password" name="contrasena" type="password" class="form-control input-xl" placeholder="Ingresa tu contraseña" minlength="6" required>
            <button type="button" class="btn btn-eye px-3" aria-label="Mostrar u ocultar contraseña"
                    onclick="import('/assets/js/app.js').then(m=>m.togglePassword('password', this))">
              <i class="fa-solid fa-eye"></i>
            </button>
          </div>
          <div class="invalid-feedback">La contraseña debe tener al menos 6 caracteres.</div>
        </div>

        <div class="d-grid mt-4">
          <button class="btn btn-accent" type="submit">Iniciar sesión</button>
        </div>

        <p class="text-center mt-3">
          <a class="btn-link-underline" href="/forgot">¿Olvidaste tu contraseña? Recupérala</a>
        </p>
      </form>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

  <?php if (!empty($_SESSION['flash_error'])):
    $err = $_SESSION['flash_error'];
    unset($_SESSION['flash_error']); ?>
    <script>
      // SweetAlert SOLO para errores de login
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: <?= json_encode($err) ?>,
        confirmButtonColor: '#ec6d13'
      });
    </script>
  <?php endif; ?>
</body>
</html>



