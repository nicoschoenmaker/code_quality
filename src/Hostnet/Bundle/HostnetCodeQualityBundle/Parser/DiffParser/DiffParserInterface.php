<?php

namespace Hostnet\Bundle\HostnetCodeQualityBundle\Parser\DiffParser;

use Hostnet\Bundle\HostnetCodeQualityBundle\Parser\ParserInterface;

use Hostnet\Bundle\HostnetCodeQualityBundle\Parser\Diff\DiffFile;

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
