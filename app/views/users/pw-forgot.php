<!-- CONTENT -->

<div class="container">

  <?php include("app/views/partials/flash.php") ?>

  <div class="row d-flex justify-content-center">

    <div class="col-10 col-md-8 col-lg-6 mt-3 bg-white rounded shadow" style="max-width: 400px;">

      <h3 class="text-center mt-3 mb-3">Wachtwoord vergeten</h3>

      <p> Vul je e-mailadres in en klik op 'Verzenden'. Je ontvangt een e-mail waarmee je een nieuw wachtwoord kan aanmaken.</p>

      <form action="<?= URL ?>/pw-forgot" method="post" class="needs-validation" novalidate>

        <?= csrf() ?>

        <div class="mb-3">
          <input type="email" name="email" id="email" class="form-control" value="<?php repopulate('email') ?>" autofocus required>
          <?php unset($_SESSION["inputs"]["email"]) ?>
        </div>

        <div class="d-grid gap-2 mt-5 mb-5">
          <button type="submit" class="btn btn-dark mb-2">Verzenden</button>
          <a href="<?= URL ?>/login" class="btn btn-outline-secondary">Annuleren</a>
        </div>

      </form>

    </div>
  </div>
</div>

<!-- SCRIPTS -->
<?php include('app/views/partials/scripts-bs5.php') ?>
<?php include('app/views/partials/scripts-form-validate.php'); ?>


<script>
  document.querySelector("#nav-item-login").classList.add("active");
</script>