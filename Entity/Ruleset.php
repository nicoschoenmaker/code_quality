<?php

namespace Hostnet\HostnetCodeQualityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ruleset")
 * @ORM\Entity
 */
class Ruleset
{
  /**
   * @var integer $id
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @var string $name
   *
   * @ORM\Column(name="name", type="string", length=50)
   */
  private $name;


  public function __construct($name)
  {
    $this->name = $name;
  }

  /**
   * Gets the name
   *
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * Sets the name
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
}
