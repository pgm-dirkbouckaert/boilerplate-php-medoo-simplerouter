<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class PasswordController {

  public static function __constructStatic() {
    require_once __DIR__ . "/../config/config.php";
    require_once __DIR__ . "/../models/User.php";
    require_once __DIR__ . "/../models/PasswordReset.php";
  }

  /**
   * Show forgot password form
   *
   * @return void
   */
  public static function showForgotPasswordForm() {

    checkSession();
    checkLoggedOut();

    // Set page title
    // $title = "Forgot Password";

    // Show content
    include("app/views/partials/head.php");
    include("app/views/partials/navbar.php");
    include("app/views/users/pw-forgot.php");
    include("app/views/partials/end.php");
  }


  /**
   * Send password reset link
   *
   * @return void
   */
  public static function sendPassWordResetLink() {

    checkSession();
    checkLoggedOut();

    if (!is_post_request() || !is_csrf_safe()) {
      return redirect("/login");
    }

    // GET EMAIL & USER
    // ----------------
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $user = User::findByEmail($email);

    if (count($user) == 0) {
      flash("Sorry, je staat niet geregistreerd.", "warning");
      $_SESSION["inputs"]["email"] = $email;
      session_write_close();
      return redirect("/pw-forgot");
    } else {
      $user = $user[0];
    }

    // CHECK PREVIOUS REQUEST (PREVENT SPAM)
    // -------------------------------------
    $previous = PasswordReset::getLastPasswordResetRequest($email);
    $now = strtotime("now");
    if (count($previous) != 0) {
      $previous = $previous[0];
      $expire = strtotime($previous["reset_time"]) + 300; // Password reset is valid for 300 seconds
      if ($now < $expire) {
        flash("Probeer over 5 minuten opnieuw.", "warning", 5000);
        $_SESSION["inputs"]["email"] = $email;
        session_write_close();
        return redirect("/pw-forgot");
      }
    }

    // CREATE NEW REQUEST
    // ------------------
    $hash = md5($user["email"] . $now); // random hash
    $values = array(
      "email"       => $email,
      "token"       => $hash,
      "created_at"  => date("Y-m-d H:i:s")
    );

    // UPDATE OR INSERT REQUEST
    // ------------------------
    if (count($previous) == 0) {
      $updateResetRequest = PasswordReset::insertResetRequest($values);
    } else {
      $updateResetRequest = PasswordReset::updateResetRequest($email, $values);
    }

    // SEND EMAIL - PHP MAILER + mailtrap
    //-----------------------------------
    $phpmailer = new PHPMailer();
    $phpmailer->isSMTP();
    $phpmailer->Host = 'smtp.mailtrap.io';
    $phpmailer->SMTPAuth = true;
    $phpmailer->Port = 2525;
    $phpmailer->Username = MAILTRAP_USERNAME;
    $phpmailer->Password = MAILTRAP_PASSWORD;

    $phpmailer->setFrom('info@farmstand.com', APP_NAME);
    $phpmailer->addReplyTo('info@farmstand.com', APP_NAME);
    $phpmailer->addAddress($user["email"], $user["username"]);
    $phpmailer->Subject = 'Password Reset';
    $phpmailer->isHTML(true);
    if (is_secure()) {
      $resetlink = "https:" . URL . "/pw-reset?i=" . $user["id"] . "&h=" . $hash;
    } else {
      $resetlink = "http:" . URL . "/pw-reset?i=" . $user["id"] . "&h=" . $hash;
    }
    $htmlBody = getResetPasswordHtml($resetlink);
    $phpmailer->Body = $htmlBody;

    if ($phpmailer->send()) {
      flash("Als je bij ons bent geregistreerd, ontvang je een e-mail.", "success", 5000);
      return redirect("/pw-forgot");
    } else {
      flash($phpmailer->ErrorInfo, "danger");
      return redirect("/pw-forgot");
    }
  }


  /**
   * Show password reset form
   *
   * @return void
   */
  public static function showPasswordResetForm($errors = null) {

    checkSession();
    checkLoggedOut();

    // Set page title
    // $title = "Reset Password";

    if (!$_GET["i"] || !$_GET["h"]) {
      return redirect("/");
    }

    if (isset($_SESSION["errors"])) {
      $errors = $_SESSION["errors"];
      unset($_SESSION["errors"]);
      session_write_close();
    }

    // Check reset request
    self::checkResetRequest();

    // Show content
    include("app/views/partials/head.php");
    include("app/views/partials/navbar.php");
    include("app/views/users/pw-reset.php");
    include("app/views/partials/end.php");
  }


  /**
   * Check reset request
   *
   * @return void
   */
  public static function checkResetRequest() {

    if (!is_get_request()) {
      flash("Sorry, ongeldig verzoek.", "danger");
      return redirect("/");
    }

    // Get request in DB
    $hash = $_GET["h"];
    $email = self::getEmailFromRequest();
    $requestInDB = PasswordReset::getLastPasswordResetRequest($email);
    if (count($requestInDB) == 0) {
      flash("Ongeldig verzoek", "danger");
      return redirect("/");
    }
    $requestInDB = $requestInDB[0];

    // Check validity
    if ($requestInDB["token"] != $hash) {
      flash("Ongeldig verzoek", "danger");
      return redirect("/");
    }

    // Check expired
    $now = strtotime("now");
    $expire = strtotime($requestInDB["created_at"]) + 300; // Password reset is valid for 300 seconds
    if ($now >= $expire) {
      flash("Verzoek verlopen. Je kan een nieuwe aanvraag versturen.", "warning");
      return redirect("/pw-forgot");
    }
  }


  /**
   * Get email from request
   *
   * @return void
   */
  public static function getEmailFromRequest() {
    $userId = filter_var($_GET["i"], FILTER_SANITIZE_NUMBER_INT);
    $user = User::findById($userId);
    if (count($user) > 0) {
      $user = $user[0];
      return $user['email'];
    }
  }


  /**
   * Reset password
   *
   * @return void
   */
  public static function resetPassword() {

    checkSession();

    if (!is_post_request() || !is_csrf_safe()) {
      flash("Sorry, ongeldig verzoek.", "danger");
      return redirect("/");
    }

    // Confirm password match
    // -------------------------
    $password = $_POST["password"];
    $password_confirm = $_POST["password_confirm"];

    if ($password != $password_confirm) {
      flash("Wachtwoorden komen niet overeen", "danger");
      if (!isset_user()) {
        $i = filter_var($_POST["i"], FILTER_SANITIZE_NUMBER_INT);
        $h = filter_var($_POST["h"], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        return redirect("/pw-reset?i=$i&h=$h");
      } elseif (isset_user()) {
        return redirect("/account");
      }
    }

    // Set minimum password length
    // ---------------------------
    if (strlen($password) < 8) {
      flash("Het wachtwoord moet uit minimum 8 tekens bestaan.", "danger");
      if (!isset_user()) {
        $i = filter_var($_POST["i"], FILTER_SANITIZE_NUMBER_INT);
        $h = filter_var($_POST["h"], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        return redirect("/pw-reset?i=$i&h=$h");
      } elseif (isset_user()) {
        return redirect("/account");
      }
    }

    // Get user from database
    // ----------------------
    if (!isset_user()) {
      $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
      $user = User::findByEmail($email);
      if (count($user) == 0) {
        flash("Sorry, er ging iets fout.", "danger");
        $i = filter_var($_POST["i"], FILTER_SANITIZE_NUMBER_INT);
        $h = filter_var($_POST["h"], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        return redirect("/pw-reset?i=$i&h=$h");
      }
      $user = $user[0];
    } else {
      $user = $_SESSION['user'];
    }
    // dd($user);

    // Write new password to database
    //-------------------------------
    $values = array(
      "password"  => password_hash($password, PASSWORD_DEFAULT)
    );
    $update = User::update($user["id"], $values);

    // Flash error
    // -----------
    if ($update->rowCount() == 0) {
      flash("Sorry, er ging iets fout.", "danger");
      if (!isset_user()) {
        $i = filter_var($_POST["i"], FILTER_SANITIZE_NUMBER_INT);
        $h = filter_var($_POST["h"], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        return redirect("/pw-reset?i=$i&h=$h");
      } elseif (isset_user()) {
        return redirect("/account");
      }
    }

    // Flash success & redirect
    // ------------------------
    if (!isset_user()) {
      flash("Je wachtwoord werd gewijzigd. Je kan nu inloggen.", "success");
      redirect("/login");
    } elseif (isset_user()) {
      flash("Je wachtwoord werd gewijzigd.", "success");
      redirect("/account");
    }
  }
}

PasswordController::__constructStatic();
