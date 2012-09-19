<?php

namespace Hostnet\HostnetCodeQualityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityReviewUser
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class CodeQualityReviewUser
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
     * @var string $username
     *
     * @ORM\Column(name="username", type="string", length=255)
     */
    private $username;

    /**
     * @var \Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityReviewUser
     *
     * @ORM\ManyToOne(targetEntity="CodeQualityReview", inversedBy="codequalityreview")
     */
    private $code_quality_reviews;


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
     * Set username
     *
     * @param string $username
     * @return CodeQualityReviewUser
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set code_quality_reviews
     *
     * @param Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityReview $codeQualityReviews
     * @return CodeQualityReviewUser
     */
    public function setCodeQualityReviews(\Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityReview $codeQualityReviews = null)
    {
        $this->code_quality_reviews = $codeQualityReviews;
    
        return $this;
    }

    /**
     * Get code_quality_reviews
     *
     * @return Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityReview 
     */
    public function getCodeQualityReviews()
    {
        return $this->code_quality_reviews;
    }
}