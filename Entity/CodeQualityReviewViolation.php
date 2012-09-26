<?php

namespace Hostnet\HostnetCodeQualityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityReview,
    Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityMetric;

/**
 * @ORM\Table
 * @ORM\Entity
 */
class CodeQualityReviewViolation
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
   * @var integer $priority
   *
   * @ORM\Column(name="priority", type="integer")
   */
  private $priority;

  /**
   * @var CodeQualityReview
   *
   * @ORM\ManyToOne(targetEntity="CodeQualityReview", inversedBy="code_quality_review_id")
   * @ORM\JoinColumn(name="code_quality_review_id", referencedColumnName="id")
   */
  private $code_quality_review;

  /**
   * @var CodeQualityMetric
   *
   * @ORM\ManyToOne(targetEntity="CodeQualityMetric", inversedBy="code_quality_metric_id")
   * @ORM\JoinColumn(name="code_quality_metric_id", referencedColumnName="id")
   */
  private $code_quality_metric;


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
   * Set begin_line
   *
   * @param integer $begin_line
   */
  public function setBeginLine($begin_line)
  {
      $this->begin_line = $begin_line;
  }

  /**
   * Get begin_line
   *
   * @return integer
   */
  public function getBeginLine()
  {
      return $this->begin_line;
  }

  /**
   * Set end_line
   *
   * @param integer $end_line
   */
  public function setEndLine($end_line)
  {
      $this->end_line = $end_line;
  }

  /**
   * Get end_line
   *
   * @return integer
   */
  public function getEndLine()
  {
      return $this->end_line;
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
   * Set code_quality_metric
   *
   * @param CodeQualityMetric $code_quality_metric
   */
  public function setCodeQualityMetric($code_quality_metric)
  {
    $this->code_quality_metric = $code_quality_metric;
  }

  /**
   * Get code_quality_metric
   *
   * @return CodeQualityMetric
   */
  public function getCodeQualityMetric()
  {
    return $this->code_quality_metric;
  }

  /**
   * Set code_quality_review
   *
   * @param CodeQualityReview $code_quality_review
   */
  public function setCodeQualityReview(CodeQualityReview $code_quality_review)
  {
      $this->code_quality_review = $code_quality_review;
  }

  /**
   * Get code_quality_review
   *
   * @return CodeQualityReview
   */
  public function getCodeQualityReview()
  {
      return $this->code_quality_review;
  }
}