<?php

namespace Hostnet\HostnetCodeQualityBundle\Entity;

interface LookUpInterface
{
  /**
   * Checks if the Entity has the given property value
   *
   * @param string $name
   * @return boolean
   */
  public function hasPropertyValue($name);
}
