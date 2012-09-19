<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser\ToolOutputParser;

use Hostnet\HostnetCodeQualityBundle\Entity\CodeFile;

use Hostnet\HostnetCodeQualityBundle\Parser\ParserInterface;

interface ToolOutputParserInterface extends ParserInterface
{
  public function parseToolOutput($tool_output, CodeFile $code_file);
}
