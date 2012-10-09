<?php

namespace Hostnet\HostnetCodeQualityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Hostnet\HostnetCodeQualityBundle\Entity\CodeLanguage,
    Hostnet\HostnetCodeQualityBundle\Entity\LookUpInterface;

/**
 * @ORM\Table(name="file")
 * @ORM\Entity
 */
class File implements LookUpInterface
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
   * @var CodeLanguage
   *
   * @ORM\ManyToOne(targetEntity="CodeLanguage", inversedBy="code_language")
   * @ORM\JoinColumn(name="code_language_id", referencedColumnName="id")
   */
  private $code_language;

  /**
   * @var Report
   *
   * @ORM\OneToMany(targetEntity="Report", mappedBy="id")
   */
  private $reports;

  public function __construct($name)
  {
    $this->name = $name;
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
   * Get name
   *
   * @return string
   */

  public function getName()
  {
    return $this->name;
  }

  /**
   * Set name
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }

  /**
   * Get an array of Report objects
   *
   * @return Report array
   */

  public function getReports()
  {
    return $this->reports;
  }

  /**
   * Get the CodeLanguage
   *
   * @return CodeLanguage
   */
  public function getCodeLanguage()
  {
    return $this->code_language;
  }

  /**
   * Set the CodeLanguage object
   *
   * @param CodeLanguage $code_language
   */
  public function setCodeLanguage(CodeLanguage $code_language)
  {
    $this->code_language = $code_language;
  }

  /**
   * Checks if the File has the given name
   *
   * @see \Hostnet\HostnetCodeQualityBundle\Entity\LookUpInterface::hasPropertyValue()
   */
  public function hasPropertyValue($name)
  {
    return $this->name = $name ? true : false;
  }
}