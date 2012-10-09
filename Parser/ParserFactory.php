<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser;

use Hostnet\HostnetCodeQualityBundle\Parser\AbstractParser;

class ParserFactory
{
  private $parsers = array();

  public function addParserInstance(AbstractParser $parser_instance)
  {
    $this->parsers[] = $parser_instance;
  }

  /**
   * Returns the Parser based on the given parser resource and if required format
   *
   * @return Parser object
   */
  public function getParserInstance($resource, $additional_properties = array())
  {
    foreach($this->parsers as $parser) {
      if($parser->supports(strtolower($resource), $additional_properties)) {
        return $parser;
      }
    }

    throw new \Exception('No parser found for the parsing request, please '
      . 'make sure the parser classes are configured correctly e.g. $resource.');
  }
}
