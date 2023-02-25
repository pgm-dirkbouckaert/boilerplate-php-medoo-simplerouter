<?php

class PasswordReset {

  private static $db;
  private static $table = "password_resets";

  public static function __constructStatic() {
    include(__DIR__ . "/../config/config.php");
    self::$db = $db;
  }

  public static function getLastPasswordResetRequest($email) {
    return self::$db->select(self::$table, "*", ["email" => $email]); //$db->select($table, $columns, $where)
  }

  public static function updateResetRequest($email, $values) {
    return self::$db->update(self::$table, $values, ["email" => $email]); //$db->update($table, $values, $where);
  }

  public static function insertResetRequest($values) {
    return self::$db->insert(self::$table, $values); //$db->insert($table, $values);
  }
}

PasswordReset::__constructStatic();
