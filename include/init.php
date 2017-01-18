<?php
require_once('Config.php');

set_include_path(get_include_path() . PATH_SEPARATOR . Config::INCLUDE_DIR);

spl_autoload_register(function ($name) {
  $file = stream_resolve_include_path("{$name}.php");
  if (file_exists($file))
    require_once($file);
});

$db = Database::getInstance();
