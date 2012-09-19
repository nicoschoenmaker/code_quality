<?php

namespace Hostnet\HostnetCodeQualityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityMetricRule
 *
 * @ORM\Table()
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
     * @var \Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityMetricRule
     *
     * @ORM\ManyToOne(targetEntity="CodeQualityMetricRule", inversedBy="codequalitymetricruleset")
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
     * @return CodeQualityMetricRule
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
     * Set message
     *
     * @param string $message
     * @return CodeQualityMetricRule
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
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
     * @param Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityMetricRule $codeQualityMetricRuleset
     * @return CodeQualityMetricRule
     */
    public function setCodeQualityMetricRuleset(\Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityMetricRule $codeQualityMetricRuleset = null)
    {
        $this->code_quality_metric_ruleset = $codeQualityMetricRuleset;
    
        return $this;
    }

    /**
     * Get code_quality_metric_ruleset
     *
     * @return Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityMetricRule 
     */
    public function getCodeQualityMetricRuleset()
    {
        return $this->code_quality_metric_ruleset;
    }
}