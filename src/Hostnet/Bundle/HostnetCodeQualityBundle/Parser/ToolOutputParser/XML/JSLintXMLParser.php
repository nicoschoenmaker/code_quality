<?php

namespace Hostnet\Bundle\HostnetCodeQualityBundle\Parser\ToolOutputParser\XML;

use Doctrine\Common\Collections\Collection;

use JMS\SerializerBundle\Exception\XmlErrorException;

use Hostnet\Bundle\HostnetCodeQualityBundle\Entity\Report,
    Hostnet\Bundle\HostnetCodeQualityBundle\Entity\Violation,
    Hostnet\Bundle\HostnetCodeQualityBundle\Parser\Diff\DiffFile,
    Hostnet\Bundle\HostnetCodeQualityBundle\Parser\ToolOutputParser\AbstractToolOutputParser,
    Hostnet\Bundle\HostnetCodeQualityBundle\Parser\ToolOutputParser\ToolOutputParserInterface,
    Hostnet\Bundle\HostnetCodeQualityBundle\Parser\EntityProviderInterface;

use DomDocument;

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
  protected $epi;

  /**
   * @param EntityProviderInterface $epi
   */
  public function __construct(EntityProviderInterface $epi)
  {
    $this->resource = 'jslint';
    $this->format = 'xml';
    $this->epi = $epi;
  }

  /**
   * Parse the output of a static code quality tool and
   * fill the Review object with the extracted data
   *
   * @param DiffFile $diff_file
   * @return Review
   * @see \Hostnet\Bundle\HostnetCodeQualityBundle\Parser\ToolOutputParser\ToolOutputParserInterface::parseToolOutput()
   */
  public function parseToolOutput(DiffFile $diff_file)
  {
    // Fill the report with the File
    $file = $this->epi->retrieveFile(
      $diff_file
    );
    $report = new Report($file);

    // Retrieve the violations array in advance as
    // it's only required to add all the violations
    $violations = $report->getViolations();
    // Parse the diff file violations
    $this->parseViolations(
      $diff_file->getDiffOutput(),
      $violations,
      true
    );
    // Parse the original file violations
    // if the original file exists
    if($diff_file->hasParent()) {
      $this->parseViolations(
        $diff_file->getOriginalOutput(),
        $violations,
        false
      );
    }

    return $report;
  }

  /**
   * Parse all the tool output violations
   *
   * @param string $output
   * @param array $violations
   * @param boolean $originated_from_diff
   * @throws XmlErrorException
   */
  private function parseViolations($output, Collection $violations, $originated_from_diff)
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
      $rule = $this->epi->getRule(
        $output_violation->getAttribute(self::RULE),
        $output_violation->getAttribute(self::PRIORITY)
      );

      // Fill the Violation
      $violation = new Violation(
        $rule,
        $output_violation->getAttribute(self::EVIDENCE),
        $output_violation->getAttribute(self::BEGINLINE),
        $output_violation->getAttribute(self::ENDLINE),
        $originated_from_diff
      );

      $violations->add($violation);
    }
  }
}
