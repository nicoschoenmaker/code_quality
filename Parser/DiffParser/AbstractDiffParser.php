<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser\DiffParser;

use Hostnet\HostnetCodeQualityBundle\Parser\AbstractParser;

abstract class AbstractDiffParser extends AbstractParser
{
  CONST T_SPACE_LENGTH = 1;
  CONST T_DOT = '.';
  CONST T_FORWARD_SLASH = '/';
  CONST T_OPEN_PARENTHESIS = '(';
  CONST T_CLOSE_PARENTHESIS = ')';
  CONST T_PLUS = '+';
  CONST T_MINUS = '-';
  CONST FILE_RANGE_PATTERN = '/@@ -[0-9]*,[0-9]* \+[0-9]*,[0-9]* @@/';
  CONST SOURCE_START = '--- ';
  CONST DESTINATION_START = '+++ ';
  CONST FILE_RANGE_BRACKETS = '@@';

  /**
   * Checks if the diff parser supports the configured scm setting
   *
   * @param string $scm
   * @return boolean
   */
  public function supports($scm)
  {
    return (strcasecmp($this->resource, $scm) == 0) ? true : false;
  }
}
