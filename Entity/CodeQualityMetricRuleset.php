<?php

namespace Hostnet\HostnetCodeQualityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityMetric,
    Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityMetricRule;

/**
 * @ORM\Table
 * @ORM\Entity
 */
class CodeQualityMetricRuleset
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
   * @var CodeQualityMetric
   *
   * @ORM\OneToMany(targetEntity="CodeQualityMetric", mappedBy="code_quality_metric_ruleset_id")
   */
  private $code_quality_metrics;

  /**
   * @var CodeQualityMetricRule
   *
   * @ORM\OneToMany(targetEntity="CodeQualityMetricRule", mappedBy="code_quality_metric_ruleset_id")
   */
  private $code_quality_metric_rules;


  public function __construct()
  {
    $this->code_quality_metrics = new \Doctrine\Common\Collections\ArrayCollection();
    $this->code_quality_metric_rules = new \Doctrine\Common\Collections\ArrayCollection();
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
   * Get code_quality_metrics
   *
   * @return CodeQualityMetric array
   */
  public function getCodeQualityMetrics()
  {
      return $this->code_quality_metrics;
  }

  /**
   * Get code_quality_metric_rules
   *
   * @return CodeQualityMetricRule array
   */
  public function getCodeQualityMetricRules()
  {
      return $this->code_quality_metric_rules;
  }
}