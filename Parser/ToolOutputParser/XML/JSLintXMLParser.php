<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser\ToolOutputParser\XML;

use JMS\SerializerBundle\Exception\XmlErrorException;

use Hostnet\HostnetCodeQualityBundle\Entity\Report,
    Hostnet\HostnetCodeQualityBundle\Parser\Diff\DiffFile,
    Hostnet\HostnetCodeQualityBundle\Parser\ToolOutputParser\AbstractToolOutputParser,
    Hostnet\HostnetCodeQualityBundle\Parser\ToolOutputParser\ToolOutputParserInterface,
    Hostnet\HostnetCodeQualityBundle\Parser\EntityProviderInterface;

/**
 * The JSLint XML Parser parses JSLint xml format output.
 *
 * @author rprent
 */
class JSLintXMLParser extends AbstractToolOutputParser implements ToolOutputParserInterface
{
  CONST BEGINLINE = 'beginline';
  CONST CHAR = 'char';
  CONST RULE = 'rule';
  CONST RULESET = 'ruleset';
  CONST PRIORITY = 'priority';
  CONST VIOLATION_TAG_NAME = 'issue';
  CONST EVIDENCE = 'evidence';

  /**
   * @var EntityProviderInterface
   */
  protected $efi;

  public function __construct(EntityProviderInterface $efi)
  {
    $this->resource = 'jslint';
    $this->format = 'xml';
    $this->efi = $efi;
  }

  /**
   * Parse the output of a static code quality tool and
   * fill the Review object with the extracted data
   *
   * @param DiffFile $diff_file
   * @return Review
   * @see \Hostnet\HostnetCodeQualityBundle\Parser\ToolOutputParser\ToolOutputParserInterface::parseToolOutput()
   */
  public function parseToolOutput(DiffFile $diff_file)
  {
    $report = new Report();
    $this->efi->retrieveEntities();

    // Fill the report with the File and CodeLanguage
    $file = $this->efi->getFile($diff_file->getName());
    $code_language = $this->efi->getCodeLanguage(
      $diff_file->getExtension()
    );
    $file->setCodeLanguage($code_language);
    $report->setFile($file);

    // Retrieve the violations array in advance as
    // it's only required to add all the violations
    $violations_array = $report->getViolations();

    $xml = new \DomDocument();
    // Load the tool output string in the xml format as xml
    if(!$xml->loadXML($diff_file->getDiffOutput())) {
      throw new XmlErrorException('Error while parsing XML, invalid XML supplied');
    }
    // Extract all the violation nodes out of the tool output
    $output_violations = $xml->getElementsByTagName(self::VIOLATION_TAG_NAME);
    foreach($output_violations as $output_violation) {

      // Fill the Rule
      $rule = $this->efi->getRule(
        $output_violation->getAttribute(self::RULE),
        $output_violation->getAttribute(self::PRIORITY)
      );

      // Fill the Violation
      $message = trim($output_violation->firstChild->nodeValue);
      $violation = $this->efi->getViolation(
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
