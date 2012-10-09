<?php

namespace Hostnet\HostnetCodeQualityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Hostnet\HostnetCodeQualityBundle\Entity\LookUpInterface;

/**
 * @ORM\Table(name="code_language")
 * @ORM\Entity
 */
class CodeLanguage implements LookUpInterface
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
   * @ORM\Column(name="name", type="string", length=30)
   */
  private $name;

  /**
   * @var Tool array
   *
   * @ORM\ManyToMany(targetEntity="Tool", mappedBy="supported_languages")
   */
  private $tools;

  /**
   * @var File array
   *
   * @ORM\OneToMany(targetEntity="File", mappedBy="id")
   */
  private $files;

  /**
   * @param String $name
   */
  public function __construct($name)
  {
    $this->name = $name;
    $this->tools = new \Doctrine\Common\Collections\ArrayCollection();
    $this->files = new \Doctrine\Common\Collections\ArrayCollection();
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
   * Set name
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
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
   * Get an array of Tool objects
   *
   * @return Tool array
   */
  public function getTools()
  {
    return $this->tools;
  }

  /**
   * Get an array of File objects
   *
   * @return File array
   */
  public function getFiles()
  {
    return $this->files;
  }

  /**
   * Checks if the CodeLanguage has the given name
   *
   * @see \Hostnet\HostnetCodeQualityBundle\Entity\LookUpInterface::hasPropertyValue()
   */
  public function hasPropertyValue($name)
  {
    return $this->name = $name ? true : false;
  }
}
