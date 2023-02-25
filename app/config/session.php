<?php

/**
 * START SESSION
 *
 * @return void
 */
function startSession($sessionDir, $domain, $path) {

  // if (!session_id()) @session_start();

  if (!session_id()) {

    $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off';

    if ($https) {
      ini_set("session.save_path", $sessionDir);
    }
    // ini_set('session.gc_maxlifetime', 60 * 60 * 24 * 1); // 1 day
    // ini_set('session.cookie_lifetime', 60 * 60 * 24 * 1); // 1 day
    // ini_set("session.cookie_samesite", "Lax");
    // ini_set("session.cookie_samesite", "None");

    session_set_cookie_params([
      "domain"    => $domain,
      "path"      => $path,
      "secure"    => $https ? true : false,
      "httponly"  => true,
    ]);

    @session_start(); // @ => no warning message if session has already been started
  };

  if (empty($_SESSION['csrfToken'])) {
    $random_token = bin2hex(random_bytes(32));
    $_SESSION['csrfToken'] = $random_token;
    session_write_close();
  }
}
