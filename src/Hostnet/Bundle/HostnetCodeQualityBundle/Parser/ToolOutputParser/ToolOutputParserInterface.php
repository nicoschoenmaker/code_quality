<?php

namespace Hostnet\Bundle\HostnetCodeQualityBundle\Parser\ToolOutputParser;

use Hostnet\Bundle\HostnetCodeQualityBundle\Parser\ParserInterface,
    Hostnet\Bundle\HostnetCodeQualityBundle\Parser\Diff\DiffFile;

/**
 * A tool output parser interface which
 * is implemented by tool output parsers.
 *
 * @author rprent
 */
interface ToolOutputParserInterface extends ParserInterface
{
  /**
   * Parse the output of a static code quality tool and
   * fill the Review object with the extracted data
   *
   * @param DiffFile $diff_file
   * @return Review
   */
  public function parseToolOutput(DiffFile $diff_file);
}
