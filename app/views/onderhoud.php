<!-- CONTENT -->
<div class="row d-flex justify-content-center" id="divAlertContainer">

  <div class="col-10 col-md-8 col-lg-6 p-3" style="max-width: 600px;">
    <div class="alert alert-info" role="alert" id="divAlert">
      De website is momenteel niet bereikbaar wegens onderhoud.
      <br>
      Onze excuses voor het ongemak.
    </div>
  </div>

</div>

<!-- SCRIPTS -->
<?php include('app/views/partials/scripts-bs5.php') ?>

<?php if (isset_user()) : ?>
  <script>
    document.querySelector("#nav-item-home").classList.add("active");
  </script>
<?php endif; ?>