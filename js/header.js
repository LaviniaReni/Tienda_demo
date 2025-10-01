const toggle = document.getElementById('menu-toggle');
const menu = document.getElementById('menu');
const overlay = document.getElementById('overlay');
const cartCounts = document.querySelectorAll('.actions__cart-count');

// ===== Toggle menú =====
toggle.addEventListener('click', () => {
  const isActive = toggle.classList.toggle('active');
  menu.classList.toggle('active');
  overlay.classList.toggle('active');
  toggle.setAttribute('aria-expanded', isActive);
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
  toggle.setAttribute('aria-expanded', false);
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

// 👉 Llamar a actualizarCarrito(desde backend) cuando el carrito cambie
// Ejemplo:
// actualizarCarrito(3);
