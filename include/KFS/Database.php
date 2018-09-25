<?php
namespace KFS;

use \PDO;

class Database {
  private static $db;

  private $pdo;

  public function __construct($host, $database, $username, $password) {
    try {
      $this->pdo = new PDO(
        "mysql:host=$host;dbname=$database;charset=utf8",
        $username, $password,
        array(
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
          PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION sql_mode = 'STRICT_TRANS_TABLES'"
        )
      );
    } catch (PDOException $e) {
      $this->pdo->rollBack();
      Alerts::addError("Failed to connect to database: {$e->getMessage()}");
    }
  }

  public static function init() {
    if (static::$db !== NULL)
      return;

    static::$db = new self(Config::DB_HOST, Config::DB_NAME, Config::DB_USER, Config::DB_PASS);
  }

  public static function getInstance() {
    static::init();
    return static::$db;
  }

  public function query($query) {
    return $this->pdo->query($query);
  }

  public function prepare($query) {
    return $this->pdo->prepare($query);
  }

  public function beginTransaction() {
    $this->pdo->beginTransaction();
  }

  public function commit() {
    $this->pdo->commit();
  }

  public function rollBack() {
    $this->pdo->rollBack();
  }

  public function lastInsertId() {
    return $this->pdo->lastInsertId();
  }

  public function getPdo() {
    return $this->pdo;
  }
}
