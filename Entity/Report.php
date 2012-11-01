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
   * @var integer $id
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @var Review
   *
   * @ORM\ManyToOne(targetEntity="Review", inversedBy="review", cascade={"persist"})
   * @ORM\JoinColumn(name="review_id", referencedColumnName="id")
   */
  private $review;

  /**
   * @var File
   *
   * @ORM\ManyToOne(targetEntity="File", inversedBy="file", cascade={"persist"})
   * @ORM\JoinColumn(name="file_id", referencedColumnName="id")
   */
  private $file;

  /**
   * @var Collection
   *
   * @ORM\OneToMany(targetEntity="Violation", mappedBy="id", cascade={"persist"})
   */
  private $violations;


  public function __construct(File $file)
  {
    $this->file = $file;
    $this->violations = new \Doctrine\Common\Collections\ArrayCollection();
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
   * @return Collection
   */
  public function getViolations()
  {
    return $this->violations;
  }

  /**
   * Get the diff file violations
   *
   * @return array
   */
  public function getDiffViolations()
  {
    $diff_violations = array();
    foreach($this->violations as $violation) {
      if($violation->isOriginatedFromDiff()) {
        $diff_violations[] = $violation;
      }
    }

    return $diff_violations;
  }

  /**
   * Get the original file violations
   *
   * @return array
   */
  public function getOriginalViolations()
  {
    $original_violations = array();
    foreach($this->violations as $violation) {
      if(!$violation->isOriginatedFromDiff()) {
        $original_violations[] = $violation;
      }
    }

    return $original_violations;
  }

  /**
   * Checks if the report contains violations
   *
   * @return boolean
   */
  public function hasViolations()
  {
    return (count($this->violations) > 0) ? true : false;
  }
  /**
   * Returns the contents of the Report
   *
   * @return string
   */
  public function __toString()
  {
    $output = $this->getFile();
    if(count($this->getViolations()) == 0) {
      $output .= "No violations for this file! \nKeep up the good work and make the dancing monkey proud!\n";
      $output .= file_get_contents(__DIR__ . '/../Resources/public/images/ascii/dancing_monkey.txt');
    } else {
      $output .= implode("\n", $this->getViolations()->toArray());
    }

    return $output;
  }
}
