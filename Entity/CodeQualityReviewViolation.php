<?php

namespace Hostnet\HostnetCodeQualityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityReviewViolation
 *
 * @ORM\Table()
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
     * @var \Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityReview
     *
     * @ORM\ManyToOne(targetEntity="CodeQualityReview", inversedBy="codequalityreview")
     */
    private $code_quality_review;

    /**
     * @var \Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityMetric
     *
     * @ORM\ManyToOne(targetEntity="CodeQualityMetric", inversedBy="codequalitymetric")
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
     * @param integer $beginLine
     * @return CodeQualityReviewViolation
     */
    public function setBeginLine($beginLine)
    {
        $this->begin_line = $beginLine;

        return $this;
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
     * @param integer $endLine
     * @return CodeQualityReviewViolation
     */
    public function setEndLine($endLine)
    {
        $this->end_line = $endLine;

        return $this;
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
     * @return CodeQualityReviewViolation
     */
    public function setPriority($priority)
    {
      $this->priority = $priority;

      return $this;
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
     * @return CodeQualityReviewViolation
     */
    public function setCodeQualityMetric($code_quality_metric)
    {
      $this->code_quality_metric = $code_quality_metric;

      return $this;
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
     * @param Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityReview $codeQualityReview
     * @return CodeQualityReviewViolation
     */
    public function setCodeQualityReview(\Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityReview $codeQualityReview = null)
    {
        $this->code_quality_review = $codeQualityReview;
    
        return $this;
    }

    /**
     * Get code_quality_review
     *
     * @return Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityReview 
     */
    public function getCodeQualityReview()
    {
        return $this->code_quality_review;
    }
}