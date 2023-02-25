<?php

class User {

  private static $db;
  private static $table = "users";

  public static function __constructStatic() {
    include(__DIR__ . "/../config/config.php");
    self::$db = $db;
  }

  public static function all() {
    return self::$db->select(self::$table, "*"); //select($table, $columns)
  }

  public static function findById($id) {
    return self::$db->select(self::$table, "*", ["id" => $id]); //select($table, $columns, $where)
  }

  public static function findByEmail($email) {
    return self::$db->select(self::$table, "*", ["email" => $email]); //select($table, $columns, $where)
  }

  public static function update($id, $values) {
    $where = array(
      "id" => $id
    );
    return self::$db->update(self::$table, $values, $where);
  }

  public static function insert($values) {
    self::$db->insert(self::$table, $values);
    if (self::$db->error) {
      unset($_SESSION['csrfToken']);
      echo ("Error: " . var_dump(self::$db->errorInfo));
    } else {
      return self::$db->id();
    }
  }

  public static function delete($id) {
    return self::$db->delete(self::$table, ["id" => $id]); //delete($table, $where);
  }
}

User::__constructStatic();