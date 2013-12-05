<?php

namespace Hostnet\Bundle\HostnetCodeQualityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Hostnet\Bundle\HostnetCodeQualityBundle\Entity\Tool;

/**
 * @ORM\Table(name="rule")
 * @ORM\Entity
 */
class Rule
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
   * @ORM\Column(name="name", type="string", length=255)
   */
  private $name;

  /**
   * @var integer $priority
   *
   * @ORM\Column(name="priority", type="integer")
   */
  private $priority;

  /**
   * @var boolean $enabled
   *
   * @ORM\Column(name="enabled", type="boolean")
   */
  private $enabled;

  /**
   * @param string $name
   * @param integer $priority
   * @param boolean $enabled
   */
  public function __construct($name, $priority = 3, $enabled = true)
  {
    $this->name = $name;
    $this->priority = $priority;
    $this->enabled = $enabled;
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
   * Set name
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }

  /**
   * Get name
   *
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * Set priority
   *
   * @param integer $priority
   */
  public function setPriority($priority)
  {
    $this->priority = $priority;
  }

  /**
   * Get priority
   *
   * @return integer
   */
  public function getPriority()
  {
    return $this->priority;
  }

  /**
   * Set enabled
   *
   * @param boolean $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = (bool) $enabled;
  }

  /**
   * Get enabled
   *
   * @return boolean
   */
  public function getEnabled()
  {
    return $this->enabled;
  }

  /**
   * Returns the contents of the Rule
   *
   * @return string
   */
  public function __toString()
  {
    return $this->getName() . PHP_EOL;
  }
}
