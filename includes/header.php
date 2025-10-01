<!-- ==========================
     HEADER PRINCIPAL DEL SITIO
     Responsive, moderno y con toggle animado + carrito junto al menú
========================== -->
<header class="header">
  <div class="header__container">

    <div class="header__top">

      <!-- ===== LOGO ===== -->
      <a href="<?php echo $base_url; ?>index.php" class="header__brand" aria-label="Ir a inicio">
        <div class="header__logo">
          <img src="<?php echo $base_url; ?>assets/icons/logo.png" alt="Logo de LoginApp">
        </div>
        <div class="header__text">
          <h1 class="header__title">LoginApp</h1>
        </div>
      </a>

      <!-- ===== BOTÓN HAMBURGUESA + CARRITO ===== -->
      <div class="header__right">
        <button class="header__menu-toggle" id="menu-toggle" aria-label="Abrir menú" aria-expanded="false" aria-controls="menu">
          <span></span><span></span><span></span>
          <span class="sr-only">Abrir menú</span>
        </button>
        <a href="<?php echo $base_url; ?>carrito.php" class="actions__btn actions__btn--mobile">
          <img src="<?php echo $base_url; ?>assets/icons/icon_cart.svg" alt="Carrito" width="22" height="22">
          <span class="actions__cart-count">0</span>
        </a>
      </div>

      <!-- ===== NAVEGACIÓN ===== -->
      <nav class="nav" id="menu" role="navigation">
        <ul class="nav__list">
          <li><a href="<?php echo $base_url; ?>index.php" class="nav__link"><img src="<?php echo $base_url; ?>assets/icons/icon_home.svg" width="18" height="18">Inicio</a></li>
          <li><a href="<?php echo $base_url; ?>page/productos.php" class="nav__link"><img src="<?php echo $base_url; ?>assets/icons/icon_box.svg" width="18" height="18">Productos</a></li>
          <li><a href="<?php echo $base_url; ?>page/contacto.php" class="nav__link"><img src="<?php echo $base_url; ?>assets/icons/icon_mail.svg" width="18" height="18">Contacto</a></li>
          <li><a href="<?php echo $base_url; ?>login.php" class="actions__btn"><img src="<?php echo $base_url; ?>assets/icons/icon_user.svg" width="18" height="18">Iniciar</a></li>
        </ul>
      </nav>

      <!-- ===== BUSCADOR ===== -->
      <form class="search" action="<?php echo $base_url; ?>buscar.php" method="get">
        <input class="search__input" type="text" name="q" placeholder="Buscar productos...">
        <button class="search__button" type="submit">
          <img src="<?php echo $base_url; ?>assets/icons/icon_lupa.svg" alt="Buscar" width="18" height="18">
        </button>
      </form>

      <!-- ===== ACCIONES (carrito escritorio) ===== -->
      <div class="actions actions--desktop">
        <a href="<?php echo $base_url; ?>carrito.php" class="actions__btn">
          <img src="<?php echo $base_url; ?>assets/icons/icon_cart.svg" alt="Carrito" width="20" height="20">
          <span class="actions__cart-count">0</span>
        </a>
      </div>

    </div> <!-- /.header__top -->

  </div> <!-- /.header__container -->
</header>

<!-- Overlay para mobile -->
<div class="overlay" id="overlay"></div>

<!-- ===== ESTILOS ===== -->
<style>
/* Reset básico */
.sr-only { position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);border:0; }

.header__top {
  display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;
  padding:10px 20px;background:#fff;border-bottom:1px solid #ddd;
}
.header__brand {display:flex;align-items:center;text-decoration:none;color:inherit;}
.header__logo img {max-height:40px;margin-right:8px;}

/* Navegación */
.nav {display:flex;flex:1;justify-content:center;}
.nav__list {display:flex;gap:20px;list-style:none;padding:0;margin:0;}
.nav__link {text-decoration:none;color:#333;display:flex;align-items:center;gap:6px;}

/* Buscador */
.search {display:flex;align-items:center;margin-left:auto;}
.search__input {padding:5px 10px;border:1px solid #ccc;border-radius:4px 0 0 4px;}
.search__button {background:#333;border:none;padding:6px 10px;border-radius:0 4px 4px 0;cursor:pointer;}
.search__button img {filter:invert(1);}

/* Acciones */
.actions {display:flex;align-items:center;gap:15px;margin-left:15px;}
.actions__btn {display:flex;align-items:center;gap:6px;text-decoration:none;color:#333;position:relative;}
.actions__cart-count {
  position:absolute;top:-6px;right:-8px;background:red;color:#fff;
  font-size:12px;font-weight:bold;border-radius:50%;padding:2px 6px;
  transition:transform 0.2s ease;
}

/* Pop animación */
@keyframes cart-pop {
  0%{transform:scale(1);}30%{transform:scale(1.3);}
  60%{transform:scale(0.9);}100%{transform:scale(1);}
}
.actions__cart-count.pop {animation:cart-pop 0.4s ease;}

/* Hamburguesa */
.header__menu-toggle {
  width:28px;height:22px;display:none;flex-direction:column;justify-content:space-between;
  background:none;border:none;cursor:pointer;padding:0;z-index:1001;
}
.header__menu-toggle span {display:block;height:3px;width:100%;background:#333;border-radius:2px;transition:all 0.3s;}
.header__menu-toggle.active span:nth-child(1){transform:rotate(45deg) translate(5px,5px);}
.header__menu-toggle.active span:nth-child(2){opacity:0;}
.header__menu-toggle.active span:nth-child(3){transform:rotate(-45deg) translate(5px,-5px);}

/* Overlay */
.overlay {position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);opacity:0;visibility:hidden;transition:opacity 0.35s;z-index:999;}
.overlay.active {opacity:1;visibility:visible;}

/* Responsive */
.header__right {display:flex;align-items:center;gap:12px;}
.actions__btn--mobile {display:none;}

@media (max-width:768px){
  .header__menu-toggle{display:flex;}
  .actions--desktop{display:none;}
  .actions__btn--mobile{display:flex;}

  .nav{
    position:fixed;top:0;left:0;flex-direction:column;background:#fff;
    width:240px;height:100%;padding:60px 20px;box-shadow:2px 0 6px rgba(0,0,0,0.2);
    transform:translateX(-100%);opacity:0;transition:transform 0.35s,opacity 0.35s;z-index:1000;
  }
  .nav.active{transform:translateX(0);opacity:1;}
  .nav__list{flex-direction:column;gap:15px;}
  .search{display:none;}
}
</style>

<!-- ===== SCRIPT ===== -->
<script>
  const toggle = document.getElementById('menu-toggle');
  const menu = document.getElementById('menu');
  const overlay = document.getElementById('overlay');
  const cartCount = document.querySelectorAll('.actions__cart-count');

  toggle.addEventListener('click', () => {
    toggle.classList.toggle('active');
    menu.classList.toggle('active');
    overlay.classList.toggle('active');
  });
  overlay.addEventListener('click', () => {
    menu.classList.remove('active');
    toggle.classList.remove('active');
    overlay.classList.remove('active');
  });

  // 🔹 Función para animar contador
  function actualizarCarrito(nuevoValor){
    cartCount.forEach(c => {
      c.textContent = nuevoValor;
      c.classList.remove('pop');
      void c.offsetWidth; // reset animación
      c.classList.add('pop');
    });
  }

  // Demo: simula que el carrito aumenta cada 6s
  let count = 0;
  setInterval(()=>{
    count++;
    actualizarCarrito(count);
  },6000);
</script>
