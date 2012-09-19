<?php

namespace Hostnet\HostnetCodeQualityBundle\Tests\Parser\ToolOutputParser;

use Hostnet\HostnetCodeQualityBundle\Parser\ToolOutputParser\PMDXMLParser,
    Hostnet\HostnetCodeQualityBundle\Entity\CodeFile;

class PMDXMLParserTest extends \PHPUnit_Framework_TestCase
{
  public function testToolOutputParser()
  {
    // Retrieve the test xml output
    $test_file_name = 'test_pmd_xml_output.txt';
    $tool_output_path = __DIR__ . '/' . $test_file_name;
    $tool_output = file_get_contents($tool_output_path);

    // Initialize the required code_file data
    $code_file = new CodeFile;
    $code_file->setName($test_file_name);
    $code_file->setExtension('php');

    // Initialize the pmd xml parser and parse the test tool output
    $pmd_xml_parser = PMDXMLParser::getInstance();
    $code_quality_review = $pmd_xml_parser->parseToolOutput($tool_output, $code_file);
    $code_quality_review_violations = $code_quality_review->getCodeQualityReviewViolations();

    $this->assertEquals($test_file_name, $code_quality_review->getFileName());

    // Test Violation #1
    $this->assertEquals('5', $code_quality_review_violations[0]->getBeginline());
    $this->assertEquals('8', $code_quality_review_violations[0]->getEndline());
    $this->assertEquals('3', $code_quality_review_violations[0]->getPriority());

    $metric = $code_quality_review_violations[0]->getCodeQualityMetric();
    $this->assertEquals('php', $metric->getCodeQualityMetricCodeLanguage()->getName());
    $this->assertEquals('Naming Rules', $metric->getCodeQualityMetricRuleset()->getName());

    $rules = $metric->getCodeQualityMetricRuleset()->getCodeQualityMetricRules();
    $this->assertEquals('ConstructorWithNameAsEnclosingClass', $rules[0]->getName());
    $this->assertEquals('Classes should not have a constructor method with the same name as the class',
        $rules[0]->getMessage());

    // Test Violation #2
    $this->assertEquals('7', $code_quality_review_violations[1]->getBeginline());
    $this->assertEquals('7', $code_quality_review_violations[1]->getEndline());
    $this->assertEquals('3', $code_quality_review_violations[1]->getPriority());

    $metric = $code_quality_review_violations[1]->getCodeQualityMetric();
    $this->assertEquals('php', $metric->getCodeQualityMetricCodeLanguage()->getName());
    $this->assertEquals('Unused Code Rules', $metric->getCodeQualityMetricRuleset()->getName());

    $rules = $metric->getCodeQualityMetricRuleset()->getCodeQualityMetricRules();
    $this->assertEquals('UnusedLocalVariable', $rules[0]->getName());
  }
}