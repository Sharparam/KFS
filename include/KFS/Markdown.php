<?php
namespace KFS;

class Markdown {
  private static $parser;

  public static function parse($content) {
    if ($parser === NULL)
      $parser = new \Parsedown();

    return $parser->text($content);
  }
}
