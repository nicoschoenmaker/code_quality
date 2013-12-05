<?php

namespace Hostnet\Bundle\HostnetCodeQualityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="code_language")
 * @ORM\Entity
 */
class CodeLanguage
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
   * @param String $name
   */
  public function __construct($name)
  {
    $this->name = $name;
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
   * Get an array of File objects
   *
   * @return Collection
   */
  public function getFiles()
  {
    return $this->files;
  }
}
