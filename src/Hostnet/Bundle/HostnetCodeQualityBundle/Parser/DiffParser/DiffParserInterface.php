<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser\DiffParser;

use Hostnet\HostnetCodeQualityBundle\Parser\ParserInterface;

use Hostnet\HostnetCodeQualityBundle\Parser\Diff\DiffFile;

/**
 * The DiffParserInterface is implemented
 * by diff parsers
 *
 * @author rprent
 */
interface DiffParserInterface extends ParserInterface
{
  /**
   * Parse the diff into an array of CodeFile objects
   *
   * @param String $diff
   * @return Codefile array
   */
  public function parseDiff($diff);
}
