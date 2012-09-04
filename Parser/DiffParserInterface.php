<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser;

interface DiffParserInterface
{
  CONST SPACE_LENGTH = 1;
  CONST FILE_RANGE_PATTERN = '/@@ -[0-9]*,[0-9]* \+[0-9]*,[0-9]* @@/';
  CONST SOURCE_START = '--- ';
  CONST DESTINATION_START = '+++ ';
  CONST FILE_RANGE_BRACKETS = '@@';
  CONST DOT = '.';
  CONST FORWARD_SLASH = '/';
  CONST OPEN_PARENTHESIS = '(';
  CONST CLOSE_PARENTHESIS = ')';
  CONST PLUS = '+';
  CONST MINUS = '-';

  public function parseDiff($diff);
}

?>