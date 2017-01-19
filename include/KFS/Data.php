<?php
namespace KFS;

class Data {
  private static $initialized = false;
  private static $year;
  private static $season;
  private static $seasonText;

  public static function getYear() {
    self::init();
    return self::$year;
  }

  public static function getSeason() {
    self::init();
    return self::$season;
  }

  public static function getSeasonText() {
    self::init();
    return self::$seasonText;
  }

  private static function init() {
    if (self::$initialized)
      return;

    try {
      $query = "SELECT `key`, `value` FROM `data` WHERE `key`='year' OR `key`='season';";

      $stmt = Database::getInstance()->query($query);

      while ($row = $stmt->fetch()) {
        if ($row->key === 'year')
          self::$year = $row->value;
        elseif ($row->key === 'season')
          self::$season = $row->value;
      }

      self::$seasonText = self::$season . ' ' . self::$year;
      self::$initialized = true;
    } catch (\PDOException $e) {
      self::$initialized = false;
      throw $e;
    }
  }
}
