<?php

namespace Hostnet\HostnetCodeQualityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityReviewViolation,
    Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityMetricRuleset;

/**
 * @ORM\Table
 * @ORM\Entity
 */
class CodeQualityMetric
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
   * @var CodeQualityReviewViolation
   *
   * @ORM\OneToMany(targetEntity="CodeQualityReviewViolation", mappedBy="code_quality_metric_id")
   *
   */
  private $code_quality_review_violations;

  /**
   * @var CodeQualityMetricRuleset
   *
   * @ORM\ManyToOne(targetEntity="CodeQualityMetricRuleset", inversedBy="code_quality_metric_ruleset_id")
   * @ORM\JoinColumn(name="code_quality_metric_ruleset_id", referencedColumnName="id")
   */
  private $code_quality_metric_ruleset;

  /**
   * @var CodeQualityMetricCodeLanguage
   *
   * @ORM\ManyToOne(targetEntity="CodeQualityMetricCodeLanguage", inversedBy="code_quality_metric_code_language_id")
   * @ORM\JoinColumn(name="code_quality_metric_code_language_id", referencedColumnName="id")
   */
  private $code_quality_metric_code_language;

  /**
   * @var boolean $enabled
   *
   * @ORM\Column(name="enabled", type="boolean")
   */
  private $enabled;


  public function __construct()
  {
    $this->code_quality_review_violations = new \Doctrine\Common\Collections\ArrayCollection();
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
   * Set code_quality_metric_code_language
   *
   * @param CodeQualityMetricCodelanguage $code_quality_metric_code_language
   */
  public function setCodeQualityMetricCodeLanguage(CodeQualityMetricCodelanguage $code_quality_metric_code_language)
  {
    $this->code_quality_metric_code_language = $code_quality_metric_code_language;
  }

  /**
   * Get code_quality_metric_code_language
   *
   * @return CodeQualityMetricCodelanguage
   */
  public function getCodeQualityMetricCodeLanguage()
  {
    return $this->code_quality_metric_code_language;
  }

  /**
   * Get code_quality_review_violations
   *
   * @return CodeQualityReviewViolation array
   */
  public function getCodeQualityReviewViolations()
  {
    return $this->code_quality_review_violations;
  }

  /**
   * Set code_quality_metric_ruleset
   *
   * @param CodeQualityMetricRuleset $codeQualityMetricRuleset
   */
  public function setCodeQualityMetricRuleset(CodeQualityMetricRuleset $code_quality_metric_ruleset)
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

  /**
   * Set enabled
   *
   * @param boolean $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
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
}