<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser\Diff;

class DiffCodeBlock
{
  /**
   * @var integer $begin_line
   */
  private $begin_line;

  /**
   * @var integer $end_line
   */
  private $end_line;

  /**
   * @var string $code
   */
  private $code;


  /**
   * Set begin_line
   *
   * @param integer $beginLine
   */
  public function setBeginLine($begin_line)
  {
      $this->begin_line = $begin_line;
  }

  /**
   * Get begin_line
   *
   * @return integer
   */
  public function getBeginLine()
  {
      return $this->begin_line;
  }

  /**
   * Set end_line
   *
   * @param integer $endLine
   */
  public function setEndLine($end_line)
  {
    $this->end_line = $end_line;
  }

  /**
   * Get end_line
   *
   * @return integer
   */
  public function getEndLine()
  {
    return $this->end_line;
  }

  /**
   * Set code
   *
   * @param string $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }

  /**
   * Get code
   *
   * @return string
   */
  public function getCode()
  {
    return $this->code;
  }
}
