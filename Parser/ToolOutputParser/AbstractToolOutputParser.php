<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser\ToolOutputParser;

use Hostnet\HostnetCodeQualityBundle\Parser\AbstractParser;

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
