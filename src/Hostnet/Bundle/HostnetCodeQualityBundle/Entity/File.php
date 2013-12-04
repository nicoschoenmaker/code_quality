<?php

namespace Hostnet\HostnetCodeQualityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Hostnet\HostnetCodeQualityBundle\Entity\CodeLanguage;

/**
 * @ORM\Table(name="file")
 * @ORM\Entity
 */
class File
{
  CONST DEV_NULL = '/dev/null';

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
   * @var string $source
   *
   * @ORM\Column(name="source", type="string", length=255)
   */
  private $source;

  /**
   * @var string $destination
   *
   * @ORM\Column(name="destination", type="string", length=255)
   */
  private $destination;

  /**
   * @var CodeLanguage
   *
   * @ORM\ManyToOne(targetEntity="CodeLanguage", inversedBy="code_language")
   * @ORM\JoinColumn(name="code_language_id", referencedColumnName="id")
   */
  private $code_language;

  /**
   * @var Collection
   *
   * @ORM\OneToMany(targetEntity="Report", mappedBy="id", cascade={"persist"})
   */
  private $reports;

  /**
   * @param CodeLanguage $code_language
   * @param string $name
   * @param string $source
   * @param string $destination
   */
  public function __construct(CodeLanguage $code_language, $name, $source, $destination)
  {
    $this->code_language = $code_language;
    $this->name = $name;
    $this->source = $source;
    $this->destination = $destination;
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
   * Get the source
   *
   * @return string
   */
  public function getSource()
  {
    return $this->source;
  }

  /**
   * Set the source
   *
   * @param string $source
   */
  public function setSource($source)
  {
    $this->source = $source;
  }

  /**
   * Get the destination
   *
   * @return string
   */
  public function getDestination()
  {
    return $this->destination;
  }

  /**
   * Set the destination
   *
   * @param string $destination
   */
  public function setDestination($destination)
  {
    $this->destination = $destination;
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
   * Check if the diff file is new
   * / has a dev null source
   *
   * @return boolean
   */
  public function isNewFile()
  {
    return $this->source == self::DEV_NULL;
  }

  /**
   * Returns the contents of the File
   *
   * @return string
   */
  public function __toString()
  {
    $full_file_name = $this->getName() . '.' . $this->getCodeLanguage()->getName();
    $output = str_repeat('*', strlen($full_file_name) + 4) . PHP_EOL;
    $output .=  '* ' . $full_file_name . ' *' . PHP_EOL;
    $output .= str_repeat('*', strlen($full_file_name) + 4) . PHP_EOL;

    return $output;
  }
}
