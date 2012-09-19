<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser\ToolOutputParser;

use Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityTool,
    Hostnet\HostnetCodeQualityBundle\Parser\ToolOutputParser\PMDXMLParser;

class ToolOutputParserFactory
{
  private static $instance;
  private static $parsers_in_dir = array();
  private $available_parsers = array();

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
      self::$instance = new ToolOutputParserFactory();
      self::retrieveListOfParsersInDir();
    }
    return self::$instance;
  }

  public function getToolOutputParser(CodeQualityTool $tool)
  {
    $tool_class = strtoupper($tool->getName() . $tool->getFormat()) . 'Parser';
    //if(in_array($tool_class, self::$parsers_in_dir)) {
      if(!in_array($tool_class, $this->available_parsers)) {
        $tool_class_path = __NAMESPACE__ . '\\' . $tool_class;
        $this->available_parsers[$tool_class] = $tool_class_path::getInstance();
        //$this->available_parsers[$tool_class] = PMDXMLParser::getInstance();
      }
      return $this->available_parsers[$tool_class];
    //} else {
      // Implementation of tool + format combo doesn't exist yet, write an implementation for it
      // TODO Return error
    //}
  }

  private static function retrieveListOfParsersInDir()
  {
    if(!self::$parsers_in_dir) {
      $files_in_directory = scandir(__DIR__);
      foreach($files_in_directory as $file_in_directory) {
        if($file_in_directory != '.' && $file_in_directory != '..'
          && !(strpos($file_in_directory, 'Parser.php') === false)
          && (strpos($file_in_directory, 'Base') !== 0)) {
          array_push(self::$parsers_in_dir, $file_in_directory);
        }
      }
    }
    return self::$parsers_in_dir;
  }
}
