<?php
class User {
  const ADMIN_LEVEL = 99;

  private static $current;

  private $id;
  private $username;
  private $password;
  private $access;

  public static function findById($id) {
    $query = 'SELECT id, username, password, access FROM users WHERE id = :id LIMIT 1;';
    $stmt = Database::getInstance()->prepare($query);
    $stmt->bindValue(':id', $id);
    $stmt->execute();

    if ($stmt->rowCount() !== 1)
      return NULL;

    return $stmt->fetchObject('User');
  }

  public static function findByName($name) {
    $query = 'SELECT id, username, password, access FROM users WHERE name = :name LIMIT 1;';
    $stmt = Database::getInstance()->prepare($query);
    $stmt->bindValue(':name', $name);
    $stmt->execute();

    if ($stmt->rowCount() !== 1)
      return NULL;

    return $stmt->fetchObject('User');
  }

  public static function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
  }

  public static function isLoggedIn() {
    return isset($_SESSION['user']);
  }

  public static function getLoggedInUser() {
    if (static::$current !== NULL)
      return static::$current;

    if (!static::isLoggedIn())
      return NULL;

    $user = static::findById($_SESSION['user']);

    if ($user !== NULL)
      static::$current = $user;

    return $user;
  }

  public static function login($username, $password) {
    if (static::isLoggedIn()) {
      Alerts::addWarning('User already logged in.');
      return false;
    }

    $user = static::findByName($username);

    if ($user === NULL || !$user->verify($password)) {
      Alerts::addError('Invalid username or password.');
      return false;
    }

    static::$current = $user;
    $_SESSION['user'] = static::$current->getId();
    return true;
  }

  public static function logout() {
    if (!static::isLoggedIn())
      return;

    static::$current = NULL;
    $_SESSION['user'] = NULL;
    session_unset();
    session_destroy();

    Alerts::addSuccess('Successfully logged out!');
  }

  public function getId() {
    return $this->id;
  }

  public function getUsername() {
    return $this->username;
  }

  public function getPassword() {
    return $this->password;
  }

  public function getAccess() {
    return $this->access;
  }

  public function setUsername($username) {
    $this->username = $username;
  }

  public function setPassword($password) {
    $this->password = static::hashPassword($password);
  }

  public function setAccess($access) {
    $this->access = $access;
  }

  public function verify($password) {
    return password_verify($password, $this->getPassword());
  }

  public function isAdmin() {
    return $this->access >= self::ADMIN_LEVEL;
  }

  public function save() {
    if ($this->getId() === NULL) {
      $this->insert();
      return;
    }

    $query = 'UPDATE users'
      . ' SET username=:username, password=:password, access=:access'
      . ' WHERE id=:id;';

    $db = Database::getInstance();

    try {
      $db->beginTransaction();
      $stmt = $db->prepare($query);
      $stmt->bindValue(':username', $this->getUsername);
      $stmt->bindValue(':password', $this->getPassword());
      $stmt->bindValue(':access', $this->getAccess());
      $stmt->bindValue(':id', $this->getId());
      $stmt->execute();
      $db->commit();
    } catch (PDOException $e) {
      $db->rollBack();
      Alerts::addError("Failed to update user: {$e->getMessage()}");
    }
  }

  public function __toString() {
    return $this->getUsername();
  }

  private function insert() {
    $query = 'INSERT INTO users(username, password, access)'
      . 'VALUES(:username, :password, :access);';

    $db = Database::getInstance();

    try {
      $db->beginTransaction();
      $stmt = $db->prepare($query);
      $stmt->bindValue(':username', $this->getUsername());
      $stmt->bindValue(':password', $this->getPassword());
      $stmt->execute();
      $this->id = $db->lastInsertId();
      $db->commit();
    } catch (PDOException $e) {
      $db->rollBack();
      Alerts::addError("Failed to insert new user: {$e->getMessage()}");
    }
  }
}
