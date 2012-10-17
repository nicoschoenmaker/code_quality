<?php

namespace Hostnet\HostnetCodeQualityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="review")
 * @ORM\Entity
 */
class Review
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
   * @var \DateTime $created_at
   *
   * @ORM\Column(name="created_at", type="date")
   */
  private $created_at;

  /**
   * @var Collection
   *
   * @ORM\OneToMany(targetEntity="Report", mappedBy="id", cascade={"persist"})
   */
  private $reports;


  public function __construct()
  {
    $this->created_at = new \DateTime();
    $this->reports = new \Doctrine\Common\Collections\ArrayCollection();
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
   * Get created at
   *
   * @return date
   */
  public function getCreatedAt()
  {
    return $this->created_at;
  }

  /**
   * Get an array of Report objects
   *
   * @return Collection
   */
  public function getReports()
  {
    return $this->reports;
  }

  /**
   * Returns the contents of the Review
   *
   * @return string
   */
  public function __toString(){
    $output = "\n";
    $output .= implode("\n", $this->getReports()->toArray());
    return $output;
  }
}
