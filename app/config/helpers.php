<?php

//---------------
//   ALGEMEEN
//---------------
function d($args = null) {
  foreach (func_get_args() as $data) {
    echo '<pre style="background: black; margin: 10px; padding: 10px; color: deepskyblue; font-family: Consolas, sans-serif;">';
    var_dump($data);
    echo '</pre>';
  }
}

function dd($args = null) {
  foreach (func_get_args() as $data) {
    echo '<pre style="background: black; margin: 10px; padding: 10px; color: deepskyblue; font-family: Consolas, sans-serif;">';
    var_dump($data);
    echo '</pre>';
  }
  die();
}

function is_secure() {
  return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
}

function flash($message, $class, $time = null) {
  $_SESSION["flash"] = ["message" => $message, "class" => $class, "time" => $time];
}

function redirect($path) {
  // header("Location: " . URL . $path);
  // echo "<script>location.href = '" . URL . $path . "'</script>";
  if (headers_sent()) {
    echo ("<script>location.href = '" . URL . $path . "'</script>");
  } else {
    header("Location: " . URL . $path);
  }
  exit;
}

function redirectWithTimeout($path, $time = 2000) {
  // echo '<script> setTimeout(function() { window.location = "' . $path . '.php" }, ' . $time . '); </script>';
  echo "<script>setTimeout(function() { location.href = '" . URL . $path . "'  }, " . $time . "); </script>";
  exit;
}


//---------------
//  FORMULIEREN
//---------------
function csrf() {
  echo '<input type="hidden" name="csrfToken" value="' . $_SESSION["csrfToken"] . '" />';
}

function is_csrf_safe(): bool {
  return  hash_equals($_SESSION['csrfToken'], $_POST['csrfToken']);
}

function is_post_request(): bool {
  return strtoupper($_SERVER['REQUEST_METHOD']) === 'POST' && !empty($_POST);
}

function is_get_request(): bool {
  return strtoupper($_SERVER['REQUEST_METHOD']) === 'GET' && !empty($_GET);
}

function repopulate($field) {
  if (isset($_POST[$field])) {
    echo htmlspecialchars($_POST[$field]);
  } else if (isset($_GET[$field])) {
    echo htmlspecialchars($_GET[$field]);
  } else if (isset($inputs[$field])) {
    echo htmlspecialchars($inputs[$field]);
  } else if (isset($_SESSION['inputs'][$field])) {
    echo htmlspecialchars($_SESSION['inputs'][$field]);
  }
}


//---------------
//    SESSION
//---------------
function isset_user() {
  return isset($_SESSION["user"]);
}

function is_admin() {
  return $_SESSION["user"]["is_admin"] == true;
}

function checkSession() {
  if (!session_id()) startSession(DIR_SESSION, APP_DOMAIN, APP_BASEPATH);
  clear_duplicate_cookies();
}

function checkLoggedIn() {
  if (!isset($_SESSION["user"])) {
    redirect("/login");
  }
  // Check if last activity was more than 60 minutes ago
  if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 3600)) {
    $email = $_SESSION["user"]["email"];
    destroy_session();
    return redirect("/session-expired?email=$email");
  }
  $_SESSION['last_activity'] = time(); // update last activity timestamp
}

function checkLoggedOut() {
  if (isset($_SESSION["user"])) {
    redirect("/");
  }
}

function destroy_session() {
  // Get name of session data file
  $filename = session_save_path() . "\sess_" . session_id();

  // Delete the session cookie (= set cookie expire-time to past time)
  if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), "", time() - 3600, $params['path'], $params['domain'], $params['secure'], isset($params['httponly']));
  }

  // Unset all of the session variables 
  // session_unset();
  $_SESSION = array();

  // Destroy the session
  // session_destroy();
  if (session_status() === 2) session_destroy(); //PHP_SESSION_DISABLED = 0 | PHP_SESSION_NONE = 1 | PHP_SESSION_ACTIVE = 2

  // Delete session data file
  if (file_exists($filename)) {
    unlink($filename);
  }
}

/**
 * Every time you call session_start(), PHP adds another identical session cookie to the response header. 
 * Do this enough times, and your response header becomes big enough to choke the web server.
 * This method clears out the duplicate session cookies. You can call it after each time you've called session_start(), 
 * or call it just before you send your headers.
 */
function clear_duplicate_cookies() {
  // If headers have already been sent, there's nothing we can do
  if (headers_sent()) {
    return;
  }
  $cookies = array();
  foreach (headers_list() as $header) {
    // Identify cookie headers
    if (strpos($header, 'Set-Cookie:') === 0) {
      $cookies[] = $header;
    }
  }
  // Removes all cookie headers, including duplicates
  header_remove('Set-Cookie');
  // Restore one copy of each cookie
  foreach (array_unique($cookies) as $cookie) {
    header($cookie, false);
  }
}


//---------------
//  REGISTRATIE
//---------------
function registrationAllowed($email) {

  // Set url
  $base_url = "https://script.google.com/macros/s/AKfycbyH0yYrQg6LOE9dp30EF7d-cmBRir6hNd2_5r765TKb1cw1W3WT/exec";
  $url = $base_url . "?email=" . $email;

  // Initialize a curl session
  $curl = curl_init();

  // Set options for CURLOPT_URL
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_HEADER, false);
  curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

  // Execute the curl session
  $data = curl_exec($curl);

  // Close the curl session
  curl_close($curl);

  // Get json
  $json = json_decode($data, true); // true = return an associative array

  // Return isMember (boolean)
  return $json["isMember"];
}

function getResetPasswordHtml($resetlink) {
  return '
    <html>
    <head>
      <style>
        body {
          font-size: 16px;
          font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }
      </style>
    </head>
    <body>
      <div>
        <a href="' . $resetlink . '" target="_blank">Klik hier om jouw wachtwoord te wijzigen.</a>
      </div>
    </body>
    </html>
  ';
}
