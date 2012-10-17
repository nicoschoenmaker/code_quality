<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser;

use Hostnet\HostnetCodeQualityBundle\Parser\AbstractParser;

/**
 * The Parser Factory holds and creates parsers.
 *
 * @author rprent
 */
class ParserFactory
{
  /**
   * The collection of parsers which can be requested
   * through the ParserFactory
   *
   * @var array
   */
  private $parsers = array();

  /**
   * The configured Source Control Management setting
   *
   * @var string
   */
  private $scm = '';

  public function __construct($scm)
  {
    $this->scm = $scm;
  }
  /**
   * Adds a parser to the collection of parsers
   *
   * @param AbstractParser $parser_instance
   */
  public function addParserInstance(AbstractParser $parser_instance)
  {
    $this->parsers[] = $parser_instance;
  }

  /**
   * Returns the DiffParser based on the configured $scm setting
   *
   * @throws \Exception
   * @return AbstractParser
   */
  public function getDiffParserInstance()
  {
    foreach($this->parsers as $parser) {
      if($parser->supports($this->scm)) {
        return $parser;
      }
    }

    throw new \Exception('No diff parser found for the diff parsing request, please '
      . 'make sure the $scm setting is configured properly.');
  }

  /**
   * Returns the ToolOutputParser based on the given parser resource and if required format
   *
   * @param string $resource
   * @param array $additional_properties
   * @throws \Exception
   * @return AbstractParser
   */
  public function getToolOutputParserInstance($resource, $additional_properties = array())
  {
    foreach($this->parsers as $parser) {
      if($parser->supports($resource, $additional_properties)) {
        return $parser;
      }
    }

    throw new \Exception('No tool output parser found for the tool output parsing request'
      . ', please make sure the parser classes are configured correctly e.g. $resource.');
  }
}
