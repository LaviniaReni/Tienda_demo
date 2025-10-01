<!-- ==========================
     HEADER PRINCIPAL DEL SITIO
     Responsive, accesible y con carrito dinámico
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

      <!-- ===== BOTÓN HAMBURGUESA + CARRITO MOBILE ===== -->
      <div class="header__right">
        <button class="header__menu-toggle" id="menu-toggle" aria-label="Abrir menú" aria-expanded="false" aria-controls="menu">
          <span></span><span></span><span></span>
          <span class="sr-only">Abrir menú</span>
        </button>
        <a href="<?php echo $base_url; ?>carrito.php" class="actions__btn actions__btn--mobile" aria-label="Ver carrito">
          <img src="<?php echo $base_url; ?>assets/icons/icon_cart.svg" alt="Carrito" width="22" height="22">
          <span class="actions__cart-count" id="cart-count-mobile">0</span>
        </a>
      </div>

      <!-- ===== NAVEGACIÓN ===== -->
      <nav class="nav" id="menu" role="navigation">
        <ul class="nav__list">
          <li><a href="<?php echo $base_url; ?>index.php" class="nav__link"><img src="<?php echo $base_url; ?>assets/icons/icon_home.svg" width="18" height="18" alt="">Inicio</a></li>
          <li><a href="<?php echo $base_url; ?>page/productos.php" class="nav__link"><img src="<?php echo $base_url; ?>assets/icons/icon_box.svg" width="18" height="18" alt="">Productos</a></li>
          <li><a href="<?php echo $base_url; ?>page/contacto.php" class="nav__link"><img src="<?php echo $base_url; ?>assets/icons/icon_mail.svg" width="18" height="18" alt="">Contacto</a></li>
          <li><a href="<?php echo $base_url; ?>login.php" class="actions__btn"><img src="<?php echo $base_url; ?>assets/icons/icon_user.svg" width="18" height="18" alt="">Iniciar</a></li>
        </ul>
      </nav>

      <!-- ===== BUSCADOR ===== -->
      <form class="search" action="<?php echo $base_url; ?>buscar.php" method="get">
        <label for="search-input" class="sr-only">Buscar productos</label>
        <input id="search-input" class="search__input" type="text" name="q" placeholder="Buscar productos...">
        <button class="search__button" type="submit" aria-label="Buscar">
          <img src="<?php echo $base_url; ?>assets/icons/icon_lupa.svg" alt="" width="18" height="18">
        </button>
      </form>

      <!-- ===== CARRITO DESKTOP ===== -->
      <div class="actions actions--desktop">
        <a href="<?php echo $base_url; ?>carrito.php" class="actions__btn" aria-label="Ver carrito">
          <img src="<?php echo $base_url; ?>assets/icons/icon_cart.svg" alt="Carrito" width="20" height="20">
          <span class="actions__cart-count" id="cart-count-desktop">0</span>
        </a>
      </div>

    </div> <!-- /.header__top -->

  </div> <!-- /.header__container -->
</header>

<!-- Overlay para mobile -->
<div class="overlay" id="overlay"></div>
