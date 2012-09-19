<?php

namespace Hostnet\HostnetCodeQualityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityMetricCodeLanguage
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class CodeQualityMetricCodeLanguage
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
     * @ORM\OneToMany(targetEntity="CodeQualityMetric", mappedBy="codequalitymetriccodelanguage")
     */
    private $code_quality_metrics;


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
     * @return CodeQualityMetricCodeLanguage
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
     * @return CodeQualityMetricCodeLanguage
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
}