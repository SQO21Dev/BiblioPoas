// public/assets/js/components.js
window.loadComponent = async function(selector, path) {
  try {
    const container = document.querySelector(selector);
    if (!container) {
      console.error(`[sidebar] Contenedor no encontrado: ${selector}`);
      return;
    }

    // Evita caché mientras depuras
    const url = `${path}?v=${Date.now()}`;
    const res = await fetch(url, { credentials: 'same-origin' });

    if (!res.ok) {
      console.error(`[sidebar] No se pudo cargar ${path}. HTTP ${res.status}`);
      container.innerHTML = `<div class="text-danger small p-2">No se pudo cargar el sidebar (${res.status}).</div>`;
      return;
    }

    const html = await res.text();
    container.innerHTML = html;

    // Marca activa según path actual
    const current = window.location.pathname.replace(/\/+$/, '');
    const links = container.querySelectorAll(".nav-sidebar .nav-link");
    links.forEach(link => {
      const href = link.getAttribute("href") || "";
      // Normaliza: /dashboard vs /dashboard/
      const normalized = href.replace(/\/+$/, '');
      if (normalized && current === normalized) {
        link.classList.add("active");
      }
    });

    console.log('[sidebar] Cargado OK:', path);
  } catch (err) {
    console.error('[sidebar] Error cargando componente:', err);
    const container = document.querySelector(selector);
    if (container) {
      container.innerHTML = `<div class="text-danger small p-2">Error cargando sidebar.</div>`;
    }
  }
};
