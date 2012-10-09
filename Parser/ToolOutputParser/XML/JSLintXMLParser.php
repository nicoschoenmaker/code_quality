<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser\ToolOutputParser\XML;

use Hostnet\HostnetCodeQualityBundle\Parser\ToolOutputParser\ToolOutputParserInterface,
    Hostnet\HostnetCodeQualityBundle\Entity\Rule,
    Hostnet\HostnetCodeQualityBundle\Entity\CodeLanguage,
    Hostnet\HostnetCodeQualityBundle\Entity\Review,
    Hostnet\HostnetCodeQualityBundle\Entity\Violation,
    Hostnet\HostnetCodeQualityBundle\Entity\Report,
    Hostnet\HostnetCodeQualityBundle\Lib\CodeFile,
    Hostnet\HostnetCodeQualityBundle\Parser\ToolOutputParser\AbstractToolOutputParser;

class JSLintXMLParser extends AbstractToolOutputParser implements ToolOutputParserInterface
{
  CONST BEGINLINE = 'beginline';
  CONST CHAR = 'char';
  CONST RULE = 'rule';
  CONST RULESET = 'ruleset';
  CONST PRIORITY = 'priority';
  CONST VIOLATION_TAG_NAME = 'issue';
  CONST EVIDENCE = 'evidence';

  protected $resource;
  protected $format;

  public function __construct()
  {
    $this->resource = 'jslint';
    $this->format = 'xml';
  }

  public function parseToolOutput($tool_output, CodeFile $code_file)
  {
    $report = new Report();
    $this->ef->retrieveEntities();

    // Fill the report with the File and CodeLanguage
    $file = $this->ef->getFile($code_file->getName());
    $code_language = $this->ef->getCodeLanguage(
      $code_file->getExtension()
    );
    $file->setCodeLanguage($code_language);
    $report->setFile($file);

    // Retrieve the violations array in advance as
    // it's only required to add all the violations
    $violations_array = $report->getViolations();

    $xml = new \DomDocument();
    // Load the tool output string in the xml format as xml
    $xml->loadXML($tool_output);
    // Extract all the violation nodes out of the tool output
    $output_violations = $xml->getElementsByTagName(self::VIOLATION_TAG_NAME);
    foreach($output_violations as $output_violation) {

      // Fill the Rule
      $rule = $this->ef->getRule(
        $output_violation->getAttribute(self::RULE),
        $output_violation->getAttribute(self::PRIORITY)
      );

      // Fill the Violation
      $message = trim($output_violation->firstChild->nodeValue);
      $violation = $this->ef->getViolation(
        $rule,
        $output_violation->getAttribute(self::EVIDENCE),
        $output_violation->getAttribute(self::BEGINLINE),
        $output_violation->getAttribute(self::ENDLINE)
      );

      $violations_array->add($violation);
    }

    return $report;
  }
}
