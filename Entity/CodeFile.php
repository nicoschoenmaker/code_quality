<?php

namespace Hostnet\HostnetCodeQualityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Hostnet\HostnetCodeQualityBundle\Entity\CodeFile
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class CodeFile
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
   * @ORM\Column(name="name", type="string", length=50)
   */
  private $name;

  /**
   * @var string $extension
   *
   * @ORM\Column(name="extension", type="string", length=20)
   */
  private $extension;

  /**
   * @var string $index
   *
   * @ORM\Column(name="index", type="string", length=255)
   */
  private $index;

  /**
   * @var string $source
   *
   * @ORM\Column(name="source", type="string", length=255)
   */
  private $source;

  /**
   * @var string $source_revision
   *
   * @ORM\Column(name="source_revision", type="string", length=20)
   */
  private $source_revision;

  /**
   * @var string $destination
   *
   * @ORM\Column(name="destination", type="string", length=255)
   */
  private $destination;

  /**
   * @var string $destination_revision
   *
   * @ORM\Column(name="destination_revision", type="string", length=20)
   */
  private $destination_revision;



  /**
   * @var \Hostnet\HostnetCodeQualityBundle\Entity\CodeBlock
   *
   * @ORM\OneToMany(targetEntity="CodeBlock", mappedBy="path", cascade={"all"})
   */
  private $code_blocks;


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
   * @return CodeFile
   */
  public function setName($name)
  {
      $this->name = $name;

      return $this;
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
   * Set extension
   *
   * @param string $extension
   * @return CodeFile
   */
  public function setExtension($extension)
  {
    $this->extension = $extension;

    return $this;
  }

  /**
   * Get extension
   *
   * @return string
   */
  public function getExtension()
  {
    return $this->extension;
  }

  /**
   * Set index
   *
   * @param string $index
   * @return CodeFile
   */
  public function setIndex($index)
  {
    $this->index = $index;

    return $this;
  }

  /**
   * Get index
   *
   * @return string
   */
  public function getIndex()
  {
    return $this->index;
  }

  /**
   * Set source
   *
   * @param string $source
   * @return CodeFile
   */
  public function setSource($source)
  {
    $this->source = $source;

    return $this;
  }

  /**
   * Get source
   *
   * @return string
   */
  public function getSource()
  {
    return $this->source;
  }

  /**
   * Set source_revision
   *
   * @param string $source_revision
   * @return CodeFile
   */
  public function setSourceRevision($source_revision)
  {
    $this->source_revision = $source_revision;

    return $this;
  }

  /**
   * Get source_revision
   *
   * @return string
   */
  public function getSourceRevision()
  {
    return $this->source_revision;
  }

  /**
   * Set destination
   *
   * @param string $destination
   * @return CodeFile
   */
  public function setDestination($destination)
  {
    $this->destination = $destination;

    return $this;
  }

  /**
   * Get destination
   *
   * @return string
   */
  public function getDestination()
  {
    return $this->destination;
  }

  /**
   * Set destination_revision
   *
   * @param string $destination_revision
   * @return CodeFile
   */
  public function setDestinationRevision($destination_revision)
  {
    $this->destination_revision = $destination_revision;

    return $this;
  }

  /**
   * Get destination_revision
   *
   * @return string
   */
  public function getDestinationRevision()
  {
    return $this->destination_revision;
  }

  /**
   * Set code blocks
   *
   * @param string $code_blocks
   * @return CodeFile
   */
  public function setCodeBlocks($code_blocks)
  {
      $this->code_blocks = $code_blocks;

      return $this;
  }

  /**
   * Get code blocks
   *
   * @return array
   */
  public function getCodeBlocks()
  {
      return $this->code_blocks;
  }
}
