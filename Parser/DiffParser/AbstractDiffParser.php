<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser\DiffParser;

use Hostnet\HostnetCodeQualityBundle\Parser\AbstractParser;

use InvalidArgumentException;

/**
 * The abstract diff parser, all the diff parsers
 * should extend this class
 *
 * @author rprent
 */
abstract class AbstractDiffParser extends AbstractParser
{
  const T_SPACE_LENGTH = 1;
  const T_DOT = '.';
  const T_FORWARD_SLASH = '/';
  const T_OPEN_PARENTHESIS = '(';
  const T_CLOSE_PARENTHESIS = ')';
  const T_PLUS = '+';
  const T_MINUS = '-';
  const FILE_RANGE_PATTERN = '/@@ -[0-9]*,[0-9]* \+[0-9]*,[0-9]* @@/';
  const SOURCE_START = '--- ';
  const DESTINATION_START = '+++ ';
  const FILE_RANGE_BRACKETS = '@@';
  const FILE_TYPE_PART_LENGTH = 1;
  const FILE_TYPE_PATTERN = '/^(a|b)\//';

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

  /**
   * Parses the file type part if it exists
   *
   * @param string $file_path
   * @return string
   */
  protected function parseFileTypePart($file_path)
  {
    if($this->hasFileTypePart($file_path)) {
      $file_path = substr($file_path, self::FILE_TYPE_PART_LENGTH);
    }

    return $file_path;
  }

  /**
   * Checks if the file type part exists
   *
   * @param string $file_path
   * @return boolean
   */
  private function hasFileTypePart($file_path)
  {
    return (bool)preg_match(self::FILE_TYPE_PATTERN, $file_path);
  }
}
