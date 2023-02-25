<!-- CONTENT -->

<div class="container">

  <?php include("app/views/partials/flash.php") ?>

  <div class="row d-flex justify-content-center">

    <div class="col-10 col-md-8 col-lg-6 mt-3" style="max-width: 400px;">

      <div class="card shadow">

        <div class="card-header text-center">
          <h5>Sessie verlopen</h5>
          <p>Gelieve opnieuw in te loggen.</p>
        </div>

        <div class="card-body">

          <form action="<?= URL ?>/login/post" method="post" class="needs-validation" novalidate>

            <?= csrf() ?>

            <div class="mb-3">
              <label for="email" class="form-label">E-mailadres</label>
              <input type="email" name="email" id="email" class="form-control" value="<?php repopulate('email') ?>" autofocus required>
              <?php unset($_SESSION["inputs"]["email"]) ?>
            </div>

            <div class="mb-3">
              <label for="password" class="form-label">Wachtwoord</label>
              <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <div class="d-grid gap-2 mt-5 mb-3">
              <button type="submit" class="btn btn-dark mb-2">Log in</button>
              <a href="<?= URL ?>/register" class="btn btn-outline-secondary">Nog geen account? Registreer.</a>
            </div>

            <a href="<?= URL ?>/pw-forgot" class="float-end mb-4"><small>Wachtwoord vergeten?</small></a>

          </form>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- SCRIPTS -->
<?php include("app/views/partials/scripts-bs5.php") ?>
<?php include("app/views/partials/scripts-form-validate.php"); ?>

<script>
  document.querySelector("#nav-item-login").classList.add("active");
</script>