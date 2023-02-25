<?php if (isset($_SESSION["flash"])) : ?>

  <?php
  $flash = $_SESSION["flash"];
  unset($_SESSION["flash"]);
  session_write_close();
  ?>

  <div class="row d-flex justify-content-center" id="flash">
    <div class="col-10 col-md-8 col-lg-6 p-0" style="max-width: 400px;">
      <div class="alert alert-<?= $flash["class"] ?> alert-dismissible fade show" role="alert">
        <?= $flash["message"] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    </div>
  </div>

  <?php
  if ($flash["time"]) {
    echo '<script>
    const flash = document.querySelector("#flash");
    setTimeout(function() { flash.classList.toggle("d-none");}, ' . $flash["time"] . ');
    </script>';
  }
  ?>

<?php endif; ?>