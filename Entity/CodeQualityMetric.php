<?php

namespace Hostnet\HostnetCodeQualityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityMetric
 *
 * @ORM\Table()
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
     * @var \Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityReviewViolation
     *
     * @ORM\OneToMany(targetEntity="CodeQualityReviewViolation", mappedBy="codequalitymetric")
     */
    private $code_quality_review_violations;

    /**
     * @var \Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityMetricRuleset
     *
     * @ORM\ManyToOne(targetEntity="CodeQualityMetricRuleset", inversedBy="codequalitymetricruleset")
     */
    private $code_quality_metric_ruleset;

    /**
     * @var \Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityMetricCodeLanguage
     *
     * @ORM\ManyToOne(targetEntity="CodeQualityMetricCodeLanguage", inversedBy="codequalitymetriccodelanguage")
     */
    private $code_quality_metric_code_language;

    /**
     * @var boolean $enabled
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;


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
     * @param string $codeQualityMetricCodeLanguage
     * @return CodeQualityMetric
     */
    public function setCodeQualityMetricCodeLanguage($codeQualityMetricCodeLanguage)
    {
        $this->code_quality_metric_code_language = $codeQualityMetricCodeLanguage;

        return $this;
    }

    /**
     * Get code_quality_metric_code_language
     *
     * @return string
     */
    public function getCodeQualityMetricCodeLanguage()
    {
        return $this->code_quality_metric_code_language;
    }

    /**
     * Set code_quality_review_violation
     *
     * @param string $codeQualityReviewViolation
     * @return CodeQualityMetric
     */
    public function setCodeQualityReviewViolation($codeQualityReviewViolation)
    {
        $this->code_quality_review_violation = $codeQualityReviewViolation;

        return $this;
    }

    /**
     * Get code_quality_review_violation
     *
     * @return string
     */
    public function getCodeQualityReviewViolation()
    {
        return $this->code_quality_review_violation;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return CodeQualityMetric
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
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
     * Add code_quality_review_violations
     *
     * @param Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityReviewViolation $codeQualityReviewViolations
     * @return CodeQualityMetric
     */
    public function addCodeQualityReviewViolation(\Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityReviewViolation $codeQualityReviewViolations)
    {
        $this->code_quality_review_violations[] = $codeQualityReviewViolations;

        return $this;
    }

    /**
     * Remove code_quality_review_violations
     *
     * @param Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityReviewViolation $codeQualityReviewViolations
     */
    public function removeCodeQualityReviewViolation(\Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityReviewViolation $codeQualityReviewViolations)
    {
        $this->code_quality_review_violations->removeElement($codeQualityReviewViolations);
    }

    /**
     * Get code_quality_review_violations
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getCodeQualityReviewViolations()
    {
        return $this->code_quality_review_violations;
    }

    /**
     * Set code_quality_metric_ruleset
     *
     * @param Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityMetricRuleset $codeQualityMetricRuleset
     * @return CodeQualityMetric
     */
    public function setCodeQualityMetricRuleset(\Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityMetricRuleset $codeQualityMetricRuleset = null)
    {
        $this->code_quality_metric_ruleset = $codeQualityMetricRuleset;

        return $this;
    }

    /**
     * Get code_quality_metric_ruleset
     *
     * @return Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityMetricRuleset
     */
    public function getCodeQualityMetricRuleset()
    {
        return $this->code_quality_metric_ruleset;
    }
}