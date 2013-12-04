<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser;

/**
 * An abstract parser class which
 * is extended by abstract parser classes.
 *
 * @author rprent
 */
abstract class AbstractParser
{
  /**
   * The resource name of which the output has to be parsed
   *
   * @var string
   */
  protected $resource;
}
