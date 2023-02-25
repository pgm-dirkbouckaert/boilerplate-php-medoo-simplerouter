<?php

require_once "app/config/config.php";
require_once 'app/router/Router.php';
require_once "app/controllers/HomeController.php";
require_once "app/controllers/AuthController.php";
require_once "app/controllers/PasswordController.php";

// HOME ROUTES
// -----------
Router::add("get", "",       fn () => HomeController::home());
Router::add("get", "/",      fn () => HomeController::home());
Router::add("get", "/home",  fn () => HomeController::home());

// AUTH ROUTES  
// -----------
Router::add("get", "/account",                fn () => AuthController::showAccount());
Router::add("get", "/login",                  fn () => AuthController::showLoginForm());
Router::add("post", "/login",                 fn () => AuthController::handleLogin());
Router::add("get", "/register",               fn () => AuthController::showRegisterForm());
Router::add("post", "/register",              fn () => AuthController::handleRegistration());
Router::add("get", "/session-expired",        fn () => AuthController::showSessionExpired());
Router::add("get", "/logout",                 fn () => AuthController::logout());

Router::add("get", "/pw-forgot",              fn () => PasswordController::showForgotPasswordForm());
Router::add("post", "/pw-forgot",             fn () => PasswordController::sendPassWordResetLink());
Router::add("get", "/pw-reset",               fn () => PasswordController::showPasswordResetForm());
Router::add("post", "/pw-reset",              fn () => PasswordController::resetPassword());

// 404 ROUTE (not found)
// ---------------------
Router::pathNotFound(function ($path) {
  header('HTTP/1.0 404 Not Found');
  include("app/views/404.php");
});

// 405 ROUTE (method not allowed)
// ------------------------------
Router::methodNotAllowed(function ($path, $method) {
  header('HTTP/1.0 405 Method Not Allowed');
  include("app/views/405.php");
});

// RUN THE ROUTER
// --------------
Router::run(APP_BASEPATH);
