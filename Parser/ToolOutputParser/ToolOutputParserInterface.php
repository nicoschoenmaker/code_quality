<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser\ToolOutputParser;

use Hostnet\HostnetCodeQualityBundle\lib\CodeFile;

interface ToolOutputParserInterface
{
  /**
   * Parse the output of a static code quality tool and fill the CodeQualityReview object with the extracted data
   *
   * @param String $tool_output
   * @param CodeFile $code_file
   * @return Review
   */
  public function parseToolOutput($tool_output, CodeFile $code_file);
}
