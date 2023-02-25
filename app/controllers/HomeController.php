<?php

class HomeController {

  public static function __constructStatic() {
    require_once __DIR__ . "/../config/config.php";
  }

  /**
   * Show home page
   *
   * @return void
   */
  public static function home() {

    checkSession();

    // Show content
    include("app/views/partials/head.php");
    include("app/views/partials/navbar.php");
    include("app/views/home.php");
    // include("app/views/onderhoud.php"); // activeer voor onderhoudsmodus
    include("app/views/partials/end.php");
  }
}

HomeController::__constructStatic();
