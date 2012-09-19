<?php

namespace Hostnet\HostnetCodeQualityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityReview
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class CodeQualityReview
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
     * @var string $file_name
     *
     * @ORM\Column(name="file_name", type="string", length=50)
     */
    private $file_name;

    /**
     * @var \DateTime $created_at
     *
     * @ORM\Column(name="created_at", type="date")
     */
    private $created_at;

    /**
     * @var \Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityReviewUser
     *
     * @ORM\OneToMany(targetEntity="CodeQualityReviewUser", mappedBy="codequalityreview")
     */
    private $code_quality_review_user;

    /**
     * @var \Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityReviewViolation
     *
     * @ORM\OneToMany(targetEntity="CodeQualityReviewViolation", mappedBy="codequalityreview")
     */
    private $code_quality_review_violations;


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
     * Set file_name
     *
     * @param string $fileName
     * @return CodeQualityReview
     */
    public function setFileName($fileName)
    {
        $this->file_name = $fileName;

        return $this;
    }

    /**
     * Get file_name
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->file_name;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return CodeQualityReview
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set code_quality_review_user
     *
     * @param CodeQualityReviewUser $code_quality_review_user
     * @return CodeQualityReview
     */
    public function setCodeQualityReviewUser($code_quality_review_user)
    {
      $this->code_quality_review_user = $code_quality_review_user;

      return $this;
    }

    /**
     * Get code_quality_review_user
     *
     * @return CodeQualityReviewUser
     */
    public function getCodeQualityReviewUser()
    {
      return $this->code_quality_review_user;
    }

    /**
     * Set code quality review violations
     *
     * @param string $code_quality_review_violation
     * @return CodeQualityReview
     */
    public function setCodeQualityReviewViolations($code_quality_review_violations)
    {
      $this->code_quality_review_violations = $code_quality_review_violations;

      return $this;
    }

    /**
     * Get code quality review violations
     *
     * @return array
     */
    public function getCodeQualityReviewViolations()
    {
      return $this->code_quality_review_violations;
    }

    /**
     * Add code_quality_review_user
     *
     * @param Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityReviewUser $codeQualityReviewUser
     * @return CodeQualityReview
     */
    public function addCodeQualityReviewUser(\Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityReviewUser $codeQualityReviewUser)
    {
        $this->code_quality_review_user[] = $codeQualityReviewUser;

        return $this;
    }

    /**
     * Remove code_quality_review_user
     *
     * @param Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityReviewUser $codeQualityReviewUser
     */
    public function removeCodeQualityReviewUser(\Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityReviewUser $codeQualityReviewUser)
    {
        $this->code_quality_review_user->removeElement($codeQualityReviewUser);
    }

    /**
     * Add code_quality_review_violations
     *
     * @param Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityReviewViolation $codeQualityReviewViolations
     * @return CodeQualityReview
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
}