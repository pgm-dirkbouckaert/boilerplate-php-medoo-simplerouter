<!-- CONTENT -->

<div class="container">

  <?php include("app/views/partials/flash.php") ?>

  <div class="row d-flex justify-content-center">

    <div class="col-10 col-md-8 col-lg-6 mt-3 bg-white rounded shadow" style="max-width: 400px;">

      <h3 class="text-center mt-3 mb-3">Registreren</h3>

      <form action="<?= URL ?>/register" method="post" class="needs-validation" novalidate>

        <?= csrf() ?>

        <div class="mb-3">
          <label for="username" class="form-label">Gebruikersnaam</label>
          <input type="text" name="username" id="username" class="form-control" value="<?php repopulate('username') ?>" autofocus required>
          <?php unset($_SESSION["inputs"]["username"]) ?>
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">E-mailadres</label>
          <input type="email" name="email" id="email" class="form-control" value="<?php repopulate('email') ?>" required>
          <?php unset($_SESSION["inputs"]["email"]) ?>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Wachtwoord</label>
          <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <div class="mb-3">
          <label for="passwordConfirm" class="form-label">Bevestig wachtwoord</label>
          <input type="password" name="passwordConfirm" id="passwordConfirm" class="form-control" required>
        </div>

        <div class="d-grid gap-2 mb-5">
          <button type="submit" class="btn btn-dark mb-2">Registreer</button>
          <a href="<?= URL ?>/login" class="btn btn-outline-secondary">Al geregistreerd? Log in.</a>
        </div>
      </form>

    </div>
  </div>
</div>

<!-- SCRIPTS -->
<?php include('app/views/partials/scripts-bs5.php') ?>
<?php include('app/views/partials/scripts-form-validate.php'); ?>

<script>
  document.querySelector("#nav-item-register").classList.add("active");
</script>