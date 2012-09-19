<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser\DiffParser;

use Hostnet\HostnetCodeQualityBundle\Parser\ParserInterface;

interface DiffParserInterface extends ParserInterface
{
  CONST FILE_RANGE_PATTERN = '/@@ -[0-9]*,[0-9]* \+[0-9]*,[0-9]* @@/';
  CONST SOURCE_START = '--- ';
  CONST DESTINATION_START = '+++ ';
  CONST FILE_RANGE_BRACKETS = '@@';

  public function parseDiff($diff);
}

?>