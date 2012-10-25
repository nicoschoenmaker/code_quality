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

  /**
   * Parse the diff header data
   *
   * @param DiffFile $diff_file
   * @param String $header_string
   * @return CodeFile
   */
  public function parseDiffHead(DiffFile $diff_file, $header_string);

  /**
   * Parse the diff body data, the actual modified code
   *
   * @param String $file_string
   * @param String $body_string
   * @return CodeBlock
   */
  public function parseDiffBody($file_string, $body_string);
}
