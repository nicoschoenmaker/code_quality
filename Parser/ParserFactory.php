<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser;

class ParserFactory
{
  CONST DIFF_PARSER_DIR = 'DiffParser';
  CONST TOOL_OUTPUT_PARSER_DIR = 'ToolOutputParser';

  private static $instance;
  private $instantiated_parsers = array();

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
      self::$instance = new ParserFactory();
    }
    return self::$instance;
  }

  /**
   * Returns the Parser based on the given parser class name
   *
   * @return Parser object
   */
  public function getParserInstance($parser_class_name, $parser_dir)
  {
    if(!in_array($parser_class_name, $this->instantiated_parsers)) {
      $path_to_parser = __NAMESPACE__ . '\\' . $parser_dir . '\\' . $parser_class_name;
      $this->instantiated_parsers[$parser_class_name] = new $path_to_parser();
    }
    return $this->instantiated_parsers[$parser_class_name];
  }
}
