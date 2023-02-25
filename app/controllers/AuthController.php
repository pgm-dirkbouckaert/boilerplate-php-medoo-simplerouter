<?php

class AuthController {

  public static function __constructStatic() {
    require_once __DIR__ . "/../config/config.php";
    require_once __DIR__ . "/../models/User.php";
  }

  /**
   * Show login form
   *
   * @return void
   */
  public static function showLoginForm() {

    checkSession();
    checkLoggedOut();

    // Set page title
    // $title = "Sign in";

    // Show content
    include("app/views/partials/head.php");
    include("app/views/partials/navbar.php");
    include("app/views/users/login.php");
    include("app/views/partials/end.php");
  }


  /**
   * Show session expired form
   *
   * @return void
   */
  public static function showSessionExpired() {

    checkSession();
    checkLoggedOut();

    // Set page title
    // $title = "Sign in";

    // Show content
    include("app/views/partials/head.php");
    include("app/views/partials/navbar.php");
    include("app/views/users/session-expired.php");
    include("app/views/partials/end.php");
  }


  /**
   * Handle login
   *
   * @return void
   */
  public static function handleLogin() {

    checkSession();
    checkLoggedOut();

    if (!is_post_request() || !is_csrf_safe()) {
      flash("Sorry, ongeldig verzoek.", "danger");
      return redirect("/login");
    }

    // Sanitize & Validate
    // -------------------
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST["password"];

    if ($email == false || $email == NULL || $email == "") {
      flash("Fout e-mailadres of wachtwoord!", "danger");
      return redirect("/login");
    }

    // Proceed if sanitized and validated
    // ----------------------------------
    $user = User::findByEmail($email);

    if (count($user) > 0) {
      $user = $user[0];
      $hash = $user['password'];
      if (password_verify($password, $hash)) {
        unset($_SESSION['csrfToken']);
        $_SESSION['user'] = $user;
        $_SESSION['last_activity'] = time(); // set last activity timestamp
        session_write_close();
        return redirect("/");
      } else {
        flash("Fout e-mailadres of wachtwoord!", "danger");
        $_SESSION["inputs"]["email"] = $email;
        session_write_close();
        return redirect("/login");
      }
    } else {
      flash("Fout e-mailadres of wachtwoord!", "danger");
      $_SESSION["inputs"]["email"] = $email;
      session_write_close();
      return redirect("/login");
    }
  }


  /**
   * Show register form
   *
   * @return void
   */
  public static function showRegisterForm() {

    checkSession();
    checkLoggedOut();

    // Set page title
    // $title = "Register";

    // Show content
    include("app/views/partials/head.php");
    include("app/views/partials/navbar.php");
    include("app/views/users/register.php");
    include("app/views/partials/end.php");
  }


  /**
   * Handle registration
   *
   * @return void
   */
  public static function handleRegistration() {

    checkSession();
    checkLoggedOut();

    if (!is_post_request() || !is_csrf_safe()) {
      flash("Sorry, ongeldig verzoek.", "danger");
      return redirect("/register");
    }

    // Sanitize & Validate
    // -------------------
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST["password"];
    $passwordConfirm = $_POST["passwordConfirm"];

    if ($username == false || $username == NULL || $username == "" || $email == false || $email == NULL || $email == "") {
      flash("Foute gegevens!", "danger");
      return redirect("/");
    }

    // Check password confirmation
    // ---------------------------
    if ($password !== $passwordConfirm) {
      flash("Wachtwoorden komen niet overeen", "danger");
      $_SESSION["inputs"]["username"] = $username;
      $_SESSION["inputs"]["email"] = $email;
      session_write_close();
      return redirect("/register");
    }

    // Set minimum password length
    // ---------------------------
    if (strlen($password) < 8) {
      flash("Het wachtwoord moet uit minimum 8 tekens bestaan.", "danger");
      $_SESSION["inputs"]["username"] = $username;
      $_SESSION["inputs"]["email"] = $email;
      session_write_close();
      return redirect("/register");
    }

    // Proceed if sanitized and validated
    // ----------------------------------
    $user = User::findByEmail($email);
    if (count($user) > 0) {
      flash("Sorry, er ging iets fout. Probeer opnieuw.", "danger");
      return redirect("/register");
    } else {
      $values = array(
        "username"  => $username,
        "email"     => $email,
        "password"  => password_hash($password, PASSWORD_DEFAULT)
      );
      $userId = User::insert($values);

      if ($userId) {
        $user = User::findById($userId)[0];
        unset($_SESSION['csrfToken']);
        $_SESSION['user'] = $user;
        $_SESSION['last_activity'] = time(); // set last activity timestamp
        session_write_close();
        return redirect("/");
      } else {
        flash("Database fout", "danger");
        return redirect("/register");
      }
    }
  }


  /**
   * Show account page
   *
   * @return void
   */
  public static function showAccount() {

    checkSession();
    checkLoggedIn();

    // Set page title
    // $title = "Account";

    // Show content
    include("app/views/partials/head.php");
    include("app/views/partials/navbar.php");
    include("app/views/users/account.php");
    include("app/views/partials/end.php");
  }


  /**
   * Log out
   *
   * @return void
   */
  public static function logout() {

    checkSession();
    checkLoggedIn();

    // Destroy session
    destroy_session();

    // Redirect
    return redirect("/");
  }
}

AuthController::__constructStatic();
