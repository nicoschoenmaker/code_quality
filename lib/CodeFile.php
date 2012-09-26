<?php

namespace Hostnet\HostnetCodeQualityBundle\lib;

class CodeFile
{
  /**
   * @var string $name
   */
  private $name;

  /**
   * @var string $extension
   */
  private $extension;

  /**
   * @var string $index
   */
  private $index;

  /**
   * The full path of the original file
   *
   * @var string $source
   */
  private $source;

  /**
   * The revision index of the original file
   *
   * @var string $source_revision
   */
  private $source_revision;

  /**
   * The full path of the file after the changes, the file could be moved
   *
   * @var string $destination
   */
  private $destination;

  /**
   * The revision index of the file after the changes
   *
   * @var string $destination_revision
   */
  private $destination_revision;

  /**
   * @var array $code_blocks
   */
  private $code_blocks;


  /**
   * Set name
   *
   * @param string $name
   * @return CodeFile
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
   * Set extension
   *
   * @param string $extension
   * @return CodeFile
   */
  public function setExtension($extension)
  {
    $this->extension = $extension;
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

  /**
   * Returns all the code in a code file
   *
   * @return string
   */
  public function getEntireCode()
  {
    $entire_code = '';
    foreach($this->code_blocks as $code_block) {
      $entire_code .= $code_block->getCode();
    }
    return $entire_code;
  }
}
