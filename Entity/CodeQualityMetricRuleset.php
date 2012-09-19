<?php

namespace Hostnet\HostnetCodeQualityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityMetricRuleset
 *
 * @ORM\Table()
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
     * @var \Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityMetric
     *
     * @ORM\OneToMany(targetEntity="CodeQualityMetric", mappedBy="codequalitymetricruleset")
     */
    private $code_quality_metrics;

    /**
     * @var \Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityMetricRule
     *
     * @ORM\OneToMany(targetEntity="CodeQualityMetricRule", mappedBy="codequalitymetricruleset")
     */
    private $code_quality_metric_rules;


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
     * @return CodeQualityMetricRuleset
     */
    public function setName($name)
    {
      $this->name = $name;

      return $this;
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
     * Add code_quality_metrics
     *
     * @param Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityMetric $codeQualityMetrics
     * @return CodeQualityMetricRuleset
     */
    public function addCodeQualityMetric(\Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityMetric $codeQualityMetrics)
    {
        $this->code_quality_metrics[] = $codeQualityMetrics;

        return $this;
    }

    /**
     * Remove code_quality_metrics
     *
     * @param Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityMetric $codeQualityMetrics
     */
    public function removeCodeQualityMetric(\Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityMetric $codeQualityMetrics)
    {
        $this->code_quality_metrics->removeElement($codeQualityMetrics);
    }

    /**
     * Get code_quality_metrics
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getCodeQualityMetrics()
    {
        return $this->code_quality_metrics;
    }

    /**
     * Add code_quality_metric_rules
     *
     * @param Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityMetricRule $codeQualityMetricRules
     * @return CodeQualityMetricRuleset
     */
    public function addCodeQualityMetricRule(\Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityMetricRule $codeQualityMetricRules)
    {
        $this->code_quality_metric_rules[] = $codeQualityMetricRules;

        return $this;
    }

    /**
     * Remove code_quality_metric_rules
     *
     * @param Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityMetricRule $codeQualityMetricRules
     */
    public function removeCodeQualityMetricRule(\Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityMetricRule $codeQualityMetricRules)
    {
        $this->code_quality_metric_rules->removeElement($codeQualityMetricRules);
    }

    /**
     * Get code_quality_metric_rules
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getCodeQualityMetricRules()
    {
        return $this->code_quality_metric_rules;
    }
}