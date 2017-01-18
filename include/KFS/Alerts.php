<?php
namespace KFS;
class Alerts {
  private static $info = array();
  private static $success = array();
  private static $warnings = array();
  private static $errors = array();

  public static function hasInfo() {
    return count(static::$info) > 0;
  }

  public static function hasSuccess() {
    return count(static::$success) > 0;
  }

  public static function hasWarnings() {
    return count(static::$warnings) > 0;
  }

  public static function hasErrors() {
    return count(static::$errors) > 0;
  }

  public static function hasAlerts() {
    return static::hasInfo() || static::hasSuccess() || static::hasWarnings() || static::hasErrors();
  }

  public static function addInfo($message) {
    static::$info[] = $message;
  }

  public static function addSuccess($message) {
    static::$success[] = $message;
  }

  public static function addWarning($message) {
    static::$warnings[] = $message;
  }

  public static function addError($message) {
    static::$errors[] = $message;
  }

  public static function printInfos() {
    foreach (static::$info as $message)
      static::printInfo($message);
    static::$info = array();
  }

  public static function printSuccesses() {
    foreach (static::$success as $message)
      static::printSuccess($message);
    static::$success = array();
  }

  public static function printWarnings() {
    foreach (static::$warnings as $message)
      static::printWarning($message);
    static::$warnings = array();
  }

  public static function printErrors() {
    foreach (static::$errors as $message)
      static::printError($message);
    static::$errors = array();
  }

  public static function printAll() {
    static::printInfos();
    static::printSuccesses();
    static::printWarnings();
    static::printErrors();
  }

  public static function printInfo($message) {
    static::printType($message, 'info');
  }

  public static function printSuccess($message) {
    static::printType($message, 'success');
  }

  public static function printWarning($message) {
    static::printType($message, 'warning');
  }

  public static function printError($message) {
    static::printType($message, 'danger');
  }

  private static function printType($message, $type) {
    ?>
    <div class="alert alert-<?= $type ?> alert-dismissable" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
      <?= $message ?>
    </div>
    <?php
  }
}
