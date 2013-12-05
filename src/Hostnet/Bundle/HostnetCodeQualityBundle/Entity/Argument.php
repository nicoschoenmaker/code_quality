<?php

namespace Hostnet\Bundle\HostnetCodeQualityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="argument")
 * @ORM\Entity
 */
class Argument
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

  /**
   *
   * @param string $name
   */
  public function __construct($name)
  {
    $this->name = $name;
  }

  /**
   * Get id
   *
   * @return integer
   */
  public function getId()
  {
    return $this->id;
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
