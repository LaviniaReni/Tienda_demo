const toggle = document.getElementById('menu-toggle');
const menu = document.getElementById('menu');
const overlay = document.getElementById('overlay');
const cartCounts = document.querySelectorAll('.actions__cart-count');

// ===== Verificar que existan los elementos principales =====
if (toggle && menu && overlay) {
  // ===== Toggle menú =====
  toggle.addEventListener('click', () => {
    const isActive = toggle.classList.toggle('active');
    menu.classList.toggle('active');
    overlay.classList.toggle('active');
    toggle.setAttribute('aria-expanded', isActive ? "true" : "false");
  });

  // ===== Cerrar menú al hacer clic en overlay =====
  overlay.addEventListener('click', closeMenu);

  // ===== Cerrar menú con Escape =====
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && menu.classList.contains('active')) {
      closeMenu();
    }
  });

  function closeMenu() {
    menu.classList.remove('active');
    toggle.classList.remove('active');
    overlay.classList.remove('active');
    toggle.setAttribute('aria-expanded', "false");
  }
}

// ===== Actualizar contador de carrito =====
function actualizarCarrito(nuevoValor) {
  cartCounts.forEach(c => {
    c.textContent = nuevoValor;
    c.classList.remove('pop');
    void c.offsetWidth; // reset animación
    c.classList.add('pop');
  });
}

// ===== Sistema de Toast =====
function showToast(message, type = "info", duration = 3000) {
  let container = document.querySelector(".toast-container");

  // Crear contenedor si no existe
  if (!container) {
    container = document.createElement("div");
    container.className = "toast-container";
    document.body.appendChild(container);
  }

  // Crear toast
  const toast = document.createElement("div");
  toast.className = `toast ${type}`;
  toast.textContent = message;

  container.appendChild(toast);

  // Mostrar con animación
  requestAnimationFrame(() => toast.classList.add("show"));

  // Quitar luego de X segundos
  setTimeout(() => {
    toast.classList.remove("show");
    setTimeout(() => toast.remove(), 300);
  }, duration);
}

// 👉 Delegación de eventos para botones dinámicos
document.addEventListener("click", async (e) => {
  if (e.target.classList.contains("add-to-cart")) {
    const id = e.target.dataset.id;

    try {
      const response = await fetch("agregar_carrito.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `producto_id=${encodeURIComponent(id)}&cantidad=1`
      });

      const data = await response.json();

      if (data.success) {
        actualizarCarrito(data.count);
        showToast("Producto agregado al carrito 🛒", "success");
      } else {
        showToast(data.error || "Error al agregar al carrito", "error");
      }
    } catch (err) {
      console.error("Error:", err);
      showToast("Error de conexión. Intenta de nuevo", "error");
    }
  }
});
