<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">

  <div class="container-fluid">

    <div class="navbar-brand"><img src="<?= URL ?>/public/img/logo.png" alt=""></div>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
      <div class="navbar-nav">
        <a id="nav-item-home" class="nav-link" aria-current="page" href="<?= URL ?>/">Home</a>
        <?php if (isset_user()) : ?>
          <!--  Add navbar items for logged in users -->
        <?php endif; ?>
      </div>
      <div class="navbar-nav ms-auto">
        <?php if (!isset_user()) : ?>
          <a id="nav-item-login" class="nav-link" href="<?= URL ?>/login">Inloggen</a>
          <a id="nav-item-register" class="nav-link" href="<?= URL ?>/register">Registreren</a>
        <?php elseif (isset_user()) : ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="nav-item-account" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-user-circle fs-3"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="nav-item-account">
              <li><a class="dropdown-item" href="<?= URL ?>/account">Account</a></li>
              <?php if (is_admin()) : ?>
                <li><a class="dropdown-item" href="#">Admin</a></li>
              <?php endif; ?>
              <li><a class="dropdown-item" href="<?= URL ?>/logout">Uitloggen</a></li>
            </ul>
          </li>
      </div>
    <?php endif; ?>
    </div>
  </div>
</nav>