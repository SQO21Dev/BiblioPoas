<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>BiblioPoás · Recuperar contraseña</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=Nunito:wght@700;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
  <main class="auth-wrapper">
    <div class="auth-card">
      <div class="text-center mb-4">
        <p class="h1 brand-title mb-1">Recuperar contraseña</p>
        <p class="text-subtle">Ingresa tu correo para recibir una contraseña temporal.</p>
      </div>

      <form class="needs-validation" novalidate method="post" action="/forgot">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <div class="mb-3">
          <label for="rec-email" class="form-label">Correo</label>
          <input id="rec-email" name="correo" type="email" class="form-control input-xl" placeholder="usuario@bibliopoas.cr" required>
          <div class="invalid-feedback">Escribe un correo válido.</div>
        </div>

        <div class="d-grid mt-3">
          <button class="btn btn-accent" type="submit">
            <i class="fa-solid fa-paper-plane me-1"></i> Enviar contraseña temporal
          </button>
        </div>

        <p class="text-center mt-3">
          <a class="btn-link-underline" href="/login"><i class="fa-solid fa-arrow-left"></i> Volver al inicio</a>
        </p>
      </form>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
