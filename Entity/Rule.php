<?php

namespace Hostnet\HostnetCodeQualityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Hostnet\HostnetCodeQualityBundle\Entity\Tool,
    Hostnet\HostnetCodeQualityBundle\Entity\Violation,
    Hostnet\HostnetCodeQualityBundle\Entity\LookUpInterface;

/**
 * @ORM\Table(name="rule")
 * @ORM\Entity
 */
class Rule implements LookUpInterface
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
   * @var Tool
   *
   * @ORM\ManyToOne(targetEntity="Tool", inversedBy="tool")
   * @ORM\JoinColumn(name="tool_id", referencedColumnName="id")
   */
  private $tool;

  /**
   * @var Violation
   *
   * @ORM\ManyToOne(targetEntity="Violation", inversedBy="violation")
   * @ORM\JoinColumn(name="violation_id", referencedColumnName="id")
   */
  private $violation;


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
   * Set tool
   *
   * @param Tool $tool
   */
  public function setTool(Tool $tool)
  {
    $this->tool = $tool;
  }

  /**
   * Get tool
   *
   * @return Tool
   */
  public function getTool()
  {
    return $this->tool;
  }

  /**
   * Set violation
   *
   * @param Violation $violation
   */
  public function setViolation(Violation $violation)
  {
    $this->violation = $violation;
  }

  /**
   * Get violation
   *
   * @return Violation
   */
  public function getViolation()
  {
    return $this->violation;
  }

  /**
   * Checks if the Rule has the given name
   *
   * @see \Hostnet\HostnetCodeQualityBundle\Entity\LookUpInterface::hasPropertyValue()
   */
  public function hasPropertyValue($name)
  {
    return $this->name = $name ? true : false;
  }
}
