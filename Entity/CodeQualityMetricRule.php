<?php

namespace Hostnet\HostnetCodeQualityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityMetricRule;

/**
 * @ORM\Table
 * @ORM\Entity
 */
class CodeQualityMetricRule
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
   * @var string $message
   *
   * @ORM\Column(name="message", type="string", length=255)
   */
  private $message;

  /**
   * @var CodeQualityMetricRule
   *
   * @ORM\ManyToOne(targetEntity="CodeQualityMetricRule", inversedBy="code_quality_metric_ruleset_id")
   * @ORM\JoinColumn(name="code_quality_metric_ruleset_id", referencedColumnName="id")
   */
  private $code_quality_metric_ruleset;


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
   * Set code_quality_metric_ruleset
   *
   * @param CodeQualityMetricRule $code_quality_metric_ruleset
   */
  public function setCodeQualityMetricRuleset(CodeQualityMetricRule $code_quality_metric_ruleset)
  {
      $this->code_quality_metric_ruleset = $code_quality_metric_ruleset;
  }

  /**
   * Get code_quality_metric_ruleset
   *
   * @return CodeQualityMetricRuleset
   */
  public function getCodeQualityMetricRuleset()
  {
      return $this->code_quality_metric_ruleset;
  }
}