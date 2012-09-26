<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser\DiffParser;

interface DiffParserInterface
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
   * Parse the diff into an array of CodeFile objects
   *
   * @param String $diff
   * @return CodeFile array:
   */
  public function parseDiff($diff);

  /**
   * Parse the diff header data
   *
   * @param String $header_string
   * @return CodeFile
   */
  public function parseDiffHead($header_string);

  /**
   * Parse the diff body data, the actual modified code
   *
   * @param String $file_string
   * @param String $body_string
   * @return CodeBlock
   */
  public function parseDiffBody($file_string, $body_string);
}
