<?php

/**
 * REQUIRE FILES
 */
require_once __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__ . "/helpers.php";
require_once __DIR__ . "/session.php";

/**
 * REQUIRE DOTENV
 */
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__, 2)); // Go 2 levels up to where .env is located
$dotenv->safeLoad();

/**
 * DEFINE APP NAME
 */
if (!defined('APP_NAME')) define('APP_NAME', $_ENV["APP_NAME"]);

/**
 * DEFINE URL
 */
if (!defined('APP_CONFIG_FOLDER')) define('APP_CONFIG_FOLDER', 'app/config');
if (!defined('APP_PROTOCOL')) define('APP_PROTOCOL', '//');
// if (!defined('APP_PROTOCOL')) define('APP_PROTOCOL', (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? "https://" : "http://");
if (!defined('APP_DOMAIN')) define('APP_DOMAIN', $_SERVER['HTTP_HOST']);
if (!defined('APP_BASEPATH')) define('APP_BASEPATH', str_replace(APP_CONFIG_FOLDER, '', dirname($_SERVER['SCRIPT_NAME'])));
if (!defined('APP_URL')) define('APP_URL', APP_PROTOCOL . APP_DOMAIN . APP_BASEPATH);
if (!defined('URL')) define('URL', APP_URL); // as a shortcut

/**
 * DEFINE MAILTRAP CREDENTIALS
 */
if (!defined('MAILTRAP_USERNAME')) define('MAILTRAP_USERNAME', $_ENV["MAILTRAP_USERNAME"]);
if (!defined('MAILTRAP_PASSWORD')) define('MAILTRAP_PASSWORD', $_ENV["MAILTRAP_PASSWORD"]);

/**
 * DEFINE STORAGE DIRECTORIES
 */
if (!defined('DIR_IMPORT')) define('DIR_IMPORT', 'app/storage/imports/');
if (!defined('DIR_UPLOAD')) define('DIR_UPLOAD', 'app/storage/uploads/');
if (!defined('DIR_SESSION')) define('DIR_SESSION', 'app/storage/session/');

/**
 * START SESSION
 */
startSession(DIR_SESSION, APP_DOMAIN, APP_BASEPATH);

/**
 * CONNECT TO DATABASE (using Medoo namespace)
 */

use Medoo\Medoo;

$db = new Medoo([
  // [required]
  'type'       => $_ENV["DB_TYPE"],
  'host'       => $_ENV["DB_HOST"],
  'database'   => $_ENV["DB_NAME"],
  'username'   => $_ENV["DB_USER"],
  'password'   => $_ENV["DB_PASS"],

  // [optional]
  'port'      => $_ENV["DB_PORT"],
  'charset'   => $_ENV["DB_CHARSET"],
  'collation' => $_ENV["DB_COLLATION"],

  // [optional] Table prefix, all table names will be prefixed as PREFIX_table.
  'prefix' => '',

  // [optional] Enable logging, it is disabled by default for better performance.
  'logging' => true,

  // [optional]
  // Error mode
  // Error handling strategies when error is occurred.
  // PDO::ERRMODE_SILENT (default) | PDO::ERRMODE_WARNING | PDO::ERRMODE_EXCEPTION
  // Read more from https://www.php.net/manual/en/pdo.error-handling.php.
  'error' => PDO::ERRMODE_SILENT,

  // [optional]
  // The driver_option for connection.
  // Read more from http://www.php.net/manual/en/pdo.setattribute.php.
  'option' => [
    PDO::ATTR_CASE => PDO::CASE_NATURAL
  ],

  // [optional] Medoo will execute those commands after connected to the database.
  'command' => [
    'SET SQL_MODE=ANSI_QUOTES'
  ]
]);
