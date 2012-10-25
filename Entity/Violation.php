<?php

namespace Hostnet\HostnetCodeQualityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Hostnet\HostnetCodeQualityBundle\Entity\Rule,
    Hostnet\HostnetCodeQualityBundle\Entity\Report;

/**
 * @ORM\Table(name="violation")
 * @ORM\Entity
 */
class Violation
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
   * @var string $message
   *
   * @ORM\Column(name="message", type="string")
   */
  private $message;

  /**
   * @var integer $begin_line
   *
   * @ORM\Column(name="begin_line", type="integer")
   */
  private $begin_line;

  /**
   * @var integer $end_line
   *
   * @ORM\Column(name="end_line", type="integer")
   */
  private $end_line;

  /**
   * @var Rule
   *
   * @ORM\ManyToOne(targetEntity="Rule", cascade={"persist"})
   * @ORM\JoinColumn(name="rule_id", referencedColumnName="id")
   */
  private $rule;


  /**
   * @param Rule $rule
   * @param string $message
   * @param string $begin_line
   * @param string $end_line
   * @param string $status
   */
  public function __construct(Rule $rule, $message, $begin_line, $end_line)
  {
    $this->rule = $rule;
    $this->message = $message;
    $this->begin_line = $begin_line;
    $this->end_line = $end_line;
    $this->reports = new \Doctrine\Common\Collections\ArrayCollection();
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
   * Set message
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }

  /**
   * Get message
   *
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }

  /**
   * Set begin line
   *
   * @param integer $begin_line
   */
  public function setBeginLine($begin_line)
  {
    $this->begin_line = $begin_line;
  }

  /**
   * Get begin line
   *
   * @return integer
   */
  public function getBeginLine()
  {
    return $this->begin_line;
  }

  /**
   * Set end line
   *
   * @param integer $end_line
   */
  public function setEndLine($end_line)
  {
    $this->end_line = $end_line;
  }

  /**
   * Get end line
   *
   * @return integer
   */
  public function getEndLine()
  {
    return $this->end_line;
  }

  /**
   * Set rule
   *
   * @param Rule $rule
   */
  public function setRule(Rule $rule)
  {
    $this->rule = $rule;
  }

  /**
   * Get rule
   *
   * @return Rule
   */
  public function getRule()
  {
    return $this->rule;
  }

  /**
   * Returns the contents of the Violation
   *
   * @return string
   */
  public function __toString()
  {
    $output = $this->rule;
    if($this->begin_line == $this->end_line) {
      $output .= 'At line ' . $this->begin_line . "\n";
    } else {
      $output .= 'From lines ' . $this->begin_line . ' to ' . $this->end_line . "\n";
    }
    $output .= $this->message . "\n";

    return $output;
  }
}
