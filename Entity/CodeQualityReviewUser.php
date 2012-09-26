<?php

namespace Hostnet\HostnetCodeQualityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityReviewUser;

/**
 * @ORM\Table
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
   * @var CodeQualityReviewUser
   *
   * @ORM\OneToMany(targetEntity="CodeQualityReview", mappedBy="code_quality_review_user_id")
   */
  private $code_quality_reviews;


  public function __construct()
  {
    $this->code_quality_reviews = new \Doctrine\Common\Collections\ArrayCollection();
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
   * Set username
   *
   * @param string $username
   */
  public function setUsername($username)
  {
      $this->username = $username;
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
   * Get code_quality_reviews
   *
   * @return CodeQualityReview
   */
  public function getCodeQualityReviews()
  {
      return $this->code_quality_reviews;
  }
}