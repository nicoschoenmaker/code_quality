<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser\ToolOutputParser\XML;

use JMS\SerializerBundle\Exception\XmlErrorException;

use Hostnet\HostnetCodeQualityBundle\Entity\Report,
    Hostnet\HostnetCodeQualityBundle\Entity\Violation,
    Hostnet\HostnetCodeQualityBundle\Parser\Diff\DiffFile,
    Hostnet\HostnetCodeQualityBundle\Parser\ToolOutputParser\ToolOutputParserInterface,
    Hostnet\HostnetCodeQualityBundle\Parser\ToolOutputParser\AbstractToolOutputParser,
    Hostnet\HostnetCodeQualityBundle\Parser\EntityProviderInterface;

use DomDocument;

/**
 * The PMD XML Parser parses PMD xml format output.
 *
 * @author rprent
 */
class PMDXMLParser extends AbstractToolOutputParser implements ToolOutputParserInterface
{
  CONST BEGINLINE = 'beginline';
  CONST ENDLINE = 'endline';
  CONST RULE = 'rule';
  CONST RULESET = 'ruleset';
  CONST PRIORITY = 'priority';
  CONST VIOLATION_TAG_NAME = 'violation';

  /**
   * @var EntityProviderInterface
   */
  protected $efi;

  public function __construct(EntityProviderInterface $efi)
  {
    $this->resource = 'pmd';
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
    // Fill the report with the File and CodeLanguage
    $code_language = $this->efi->getCodeLanguage($diff_file->getExtension());
    $file = $this->efi->retrieveFile(
      $code_language,
      $diff_file->getName(),
      $diff_file->getSource()
    );
    $report = new Report($file);

    // Retrieve the violations array in advance as
    // it's only required to add all the violations
    $violations_array = $report->getViolations();
    // Parse the diff file violations
    $this->parseViolations(
      $diff_file->getDiffOutput(),
      $violations_array,
      true
    );
    // Parse the original file violations
    // if the original file exists
    if($diff_file->hasParent()) {
      $this->parseViolations(
        $diff_file->getOriginalOutput(),
        $violations_array,
        false
      );
    }

    return $report;
  }

  /**
   * Parse all the tool output violations
   *
   * @param string $output
   * @param array $violations_array
   * @param boolean $originated_from_diff
   * @throws XmlErrorException
   */
  private function parseViolations($output, $violations_array, $originated_from_diff)
  {
    $xml = new DomDocument();
    // Load the tool output string in the xml format as xml
    if(!$xml->loadXML($output)) {
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
      $violation = new Violation(
        $rule,
        $message,
        $output_violation->getAttribute(self::BEGINLINE),
        $output_violation->getAttribute(self::ENDLINE),
        $originated_from_diff
      );

      $violations_array->add($violation);
    }
  }
}
