<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser\ToolOutputParser;

use Hostnet\HostnetCodeQualityBundle\Parser\AbstractParser;

abstract class AbstractToolOutputParser extends AbstractParser
{
  /**
   * The format the parser is supposed to parse
   *
   * @var String
   */
  protected $format;

  public function supports($resource, $additional_properties = array())
  {
    return ($this->resource == $resource
      && $this->format == $additional_properties['format']) ? true : false;
  }
}
