<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser\ToolOutputParser;

use Hostnet\HostnetCodeQualityBundle\Parser\Diff\DiffFile;

interface ToolOutputParserInterface
{
  /**
   * Parse the output of a static code quality tool and
   * fill the Review object with the extracted data
   *
   * @param String $tool_output
   * @param DiffFile $diff_file
   * @return Review
   */
  public function parseToolOutput($tool_output, DiffFile $diff_file);
}
