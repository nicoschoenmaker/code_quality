<?php

namespace Hostnet\HostnetCodeQualityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityReviewUser,
    Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityReviewViolation;

/**
 * @ORM\Table
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
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
   * @ORM\Column(name="file_name", type="string", length=100)
   */
  private $file_name;

  /**
   * @var \DateTime $created_at
   *
   * @ORM\Column(name="created_at", type="date")
   */
  private $created_at;

  /**
   * @var CodeQualityReviewUser
   *
   * @ORM\ManyToOne(targetEntity="CodeQualityReviewUser", inversedBy="code_quality_review_user_id")
   * @ORM\JoinColumn(name="code_quality_review_user_id", referencedColumnName="id")
   */
  private $code_quality_review_user;

  /**
   * @var CodeQualityReviewViolation
   *
   * @ORM\OneToMany(targetEntity="CodeQualityReviewViolation", mappedBy="code_quality_review_id")
   */
  private $code_quality_review_violations;


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
   * Set file_name
   *
   * @param string $fileName
   */
  public function setFileName($file_name)
  {
      $this->file_name = $file_name;
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
   * @ORM\PrePersist
   */
  public function setCreatedAt()
  {
      $this->created_at = new \DateTime();
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
   */
  public function setCodeQualityReviewUser(CodeQualityReviewUser $code_quality_review_user)
  {
    $this->code_quality_review_user = $code_quality_review_user;
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
   * Get code quality review violations
   *
   * @return CodeQualityReviewViolation array
   */
  public function getCodeQualityReviewViolations()
  {
    return $this->code_quality_review_violations;
  }
}