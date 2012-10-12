<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser\DiffParser;

interface DiffParserInterface
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
