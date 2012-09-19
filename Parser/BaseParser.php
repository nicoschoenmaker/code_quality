<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser;

class BaseParser
{
  private static $instance;

  private function __construct(){}
  private function __clone(){}

  /**
   * Returns the Parser singleton class
   *
   * @return Parser
   */
  public static function getInstance()
  {
    if(!self::$instance) {
      $class = get_called_class();
      self::$instance = new $class();
    }
    return self::$instance;
  }
}
