<?php

namespace Hostnet\HostnetCodeQualityBundle\Tests\Parser\ToolOutputParser;

use Hostnet\HostnetCodeQualityBundle\Parser\ParserFactory,
    Hostnet\HostnetCodeQualityBundle\Parser\ToolOutputParser\XML\PMDXMLParser,
    Hostnet\HostnetCodeQualityBundle\Tests\Mock\MockEntityFactory;

class PMDXMLParserTest extends \PHPUnit_Framework_TestCase
{
  /**
   * @var Doctrine\ORM\EntityManager
   */
  private $em;

  /**
   * @var MockEntityFactory
   */
  private $ef;

  public function setUp()
  {
    // Mock the EntityManager without calling the constructor, (the constructor is private)
    $path_to_em = 'Doctrine\ORM\EntityManager';
    $this->em = $this->getMock($path_to_em, array(), array(), '', false);
    $this->ef = new MockEntityFactory();
  }

  public function testToolOutputParser()
  {
    // Retrieve the test xml output
    $test_file_name = 'test_pmd_xml_output.txt';
    $tool_output_path = __DIR__ . '/' . $test_file_name;
    $tool_output = file_get_contents($tool_output_path);

    // Using a mock object for the diff file as the diff_output property gets set in a dependent class.
    $diff_file = $this->getMock('Hostnet\HostnetCodeQualityBundle\Parser\Diff\DiffFile');
    $diff_file
      ->expects($this->any())
      ->method('getName')
      ->will($this->returnValue($test_file_name))
    ;
    $diff_file
      ->expects($this->any())
      ->method('getExtension')
      ->will($this->returnValue('php'))
    ;
    $diff_file
      ->expects($this->any())
      ->method('getDiffOutput')
      ->will($this->returnValue($tool_output))
    ;

    // Initialize the pmd xml parser and parse the test tool output
    $pmd_xml_parser = new PMDXMLParser($this->ef);
    $report = $pmd_xml_parser->parseToolOutput($diff_file, $diff_file->getDiffOutput());

    $this->assertEquals($test_file_name, $report->getFile()->getName());

    // Test Violation #1
    $violations = $report->getViolations();
    $violation = $violations[0];
    $this->assertEquals('5', $violation->getBeginline());
    $this->assertEquals('8', $violation->getEndline());
    $this->assertEquals('Classes should not have a constructor method with the same name as the class',
      $violation->getMessage());

    $rule = $violation->getRule();
    $this->assertEquals('3', $rule->getPriority());
    $this->assertEquals('ConstructorWithNameAsEnclosingClass', $rule->getName());
  }
}