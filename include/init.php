<?php
require_once('KFS/Config.php');

set_include_path(get_include_path() . PATH_SEPARATOR . KFS\Config::INCLUDE_DIR);

spl_autoload_register(function ($name) {
  $parts = explode('\\', $name);
  $path = implode('/', $parts);
  $file = stream_resolve_include_path("{$path}.php");
  if (file_exists($file))
    require_once($file);
});

function requireAdmin() {
  if (KFS\User::isLoggedIn() && KFS\User::getLoggedInUser()->isAdmin())
    return;

  header('Location: /');
  exit();
}

$db = KFS\Database::getInstance();
