<?php

namespace Hostnet\CodeQualityBundle\Tests\Parser\ToolOutputParser;

use Hostnet\CodeQualityBundle\Entity\File,
    Hostnet\CodeQualityBundle\Parser\ParserFactory,
    Hostnet\CodeQualityBundle\Parser\ToolOutputParser\XML\PMDXMLParser,
    Hostnet\CodeQualityBundle\Tests\Mock\MockEntityFactory;

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
    $diff_file = $this->getMock('Hostnet\CodeQualityBundle\Parser\Diff\DiffFile');
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
    $file = new File('php', 'test_pmd_xml_output.txt', 'test_source', 'test_destination');
    $diff_file
      ->expects($this->any())
      ->method('createFile')
      ->will($this->returnValue($file))
    ;

    // Initialize the pmd xml parser and parse the test tool output
    $pmd_xml_parser = new PMDXMLParser($this->ef);
    $report = $pmd_xml_parser->parseToolOutput($diff_file);

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