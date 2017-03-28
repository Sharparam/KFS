<?php
namespace KFS;

class Data {
  private static $initialized = false;
  private static $year;
  private static $season;
  private static $seasonText;
  private static $seasonStart;

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

  public static function getSeasonStart() {
    self::init();
    return self::$seasonStart;
  }

  private static function init() {
    if (self::$initialized)
      return;

    try {
      $query = "SELECT `key`, `value` FROM `data` WHERE `key`='year' OR `key`='season';";

      $stmt = Database::getInstance()->query($query);

      while ($row = $stmt->fetch()) {
        switch ($row->key) {
          case 'year':
            self::$year = $row->value;
            break;

          case 'season':
            self::$season = $row->value;
            break;

          case 'season_start':
            self::$seasonStart = $row->value;
            break;
        }
      }

      self::$seasonText = self::$season . ' ' . self::$year;
      self::$initialized = true;
    } catch (\PDOException $e) {
      self::$initialized = false;
      throw $e;
    }
  }
}
