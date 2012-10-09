<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser;

abstract class AbstractParser
{
  /**
   * The resource name of which the output has to be parsed
   *
   * @var String
   */
  protected $resource;

  /**
   * Checks if the parser supports the resource
   *
   * @param String $resource
   * @return boolean
   */
  public function supports($resource, $additional_properties = array())
  {
    return ($this->resource == $resource) ? true : false;
  }
}
