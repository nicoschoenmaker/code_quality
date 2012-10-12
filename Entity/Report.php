<?php

namespace Hostnet\HostnetCodeQualityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Hostnet\HostnetCodeQualityBundle\Entity\Review,
    Hostnet\HostnetCodeQualityBundle\Entity\File;

/**
 * @ORM\Table(name="report")
 * @ORM\Entity
 */
class Report
{
  /**
   * @var Review
   *
   * @ORM\ManyToOne(targetEntity="Rule", inversedBy="review")
   * @ORM\JoinColumn(name="review_id", referencedColumnName="id")
   * @ORM\Id
   */
  private $review;

  /**
   * @var File
   *
   * @ORM\ManyToOne(targetEntity="File", inversedBy="file")
   * @ORM\JoinColumn(name="file_id", referencedColumnName="id")
   * @ORM\Id
   */
  private $file;

  /**
   * @var ArrayCollection
   *
   * @ORM\ManyToMany(targetEntity="Violation", inversedBy="reports")
   * @ORM\JoinTable(name="report_violation",
   *   joinColumns={@ORM\JoinColumn(name="review_id", referencedColumnName="review_id"),
   *     @ORM\JoinColumn(name="file_id", referencedColumnName="file_id")},
   *   inverseJoinColumns={@ORM\JoinColumn(name="violation_id", referencedColumnName="id")}
   * )
   */
  private $violations;


  public function __construct(File $file)
  {
    $this->file = $file;
    $this->violations = new \Doctrine\Common\Collections\ArrayCollection();
  }

  /**
   * Get review
   *
   * @return Review
   */
  public function getReview()
  {
    return $this->review;
  }

  /**
   * Sets the Review object
   *
   * @param Review $review
   */
  public function setReview(Review $review)
  {
    $this->review = $review;
  }

  /**
   * Get file
   *
   * @return File
   */
  public function getFile()
  {
    return $this->file;
  }

  /**
   * Sets the File object
   *
   * @param File $file
   */
  public function setFile(File $file)
  {
    $this->file = $file;
  }

  /**
   * Get an array of Violation objects
   *
   * @return ArrayCollection
   */
  public function getViolations()
  {
    return $this->violations;
  }
}
