<?php

namespace Hostnet\Bundle\HostnetCodeQualityBundle\Parser\ToolOutputParser;

use Hostnet\Bundle\HostnetCodeQualityBundle\Parser\AbstractParser;

/**
 * An abstract tool output parser class which
 * is extended by tool output parsers.
 *
 * @author rprent
 */
abstract class AbstractToolOutputParser extends AbstractParser
{
  /**
   * The format the parser is supposed to parse
   *
   * @var string
   */
  protected $format;

  /**
   * Checks if the tool output parser supports the resource
   *
   * @param string $resource
   * @param array $additional_properties
   * @return boolean
   */
  public function supports($resource, $additional_properties = array())
  {
    return ((strcasecmp($this->resource, $resource) == 0)
      && (strcasecmp($this->format, $additional_properties['format']) == 0)) ? true : false;
  }
}
