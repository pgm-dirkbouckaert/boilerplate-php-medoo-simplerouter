<?php
require_once "app/config/config.php";
?>

<!-- HEAD -->
<?php include('app/views/partials/head.php') ?>

<!-- NAVBAR -->
<?php include('app/views/partials/navbar.php') ?>

<!-- CONTENT -->
<div class="row d-flex justify-content-center" id="divAlertContainer">

  <div class="col-10 col-md-8 col-lg-6 p-0" style="max-width: 400px;">
    <div class="alert alert-danger" role="alert" id="divAlert">
      Sorry, de gevraagde methode is niet toegestaan.
    </div>
  </div>

</div>

<!-- SCRIPTS -->
<script src="<?= URL ?>/public/js/bootstrap.bundle.min.js"></script>

<script>
  document.querySelector("#nav-item-home").classList.add("active");
</script>

<!-- DOCUMENT END -->
<?php include('app/views/partials/end.php') ?>