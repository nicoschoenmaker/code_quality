<?php

namespace Hostnet\HostnetCodeQualityBundle\Tests\Parser\ToolOutputParser;

use Hostnet\HostnetCodeQualityBundle\Parser\ParserFactory,
    Hostnet\HostnetCodeQualityBundle\Lib\CodeFile,
    Hostnet\HostnetCodeQualityBundle\Parser\ToolOutputParser\XML\PMDXMLParser,
    Hostnet\HostnetCodeQualityBundle\Entity\Violation,
    Hostnet\HostnetCodeQualityBundle\Entity\Rule,
    Hostnet\HostnetCodeQualityBundle\Entity\File,
    Hostnet\HostnetCodeQualityBundle\Entity\CodeLanguage;

class PMDXMLParserTest extends \PHPUnit_Framework_TestCase
{
  /**
   * @var Doctrine\ORM\EntityManager
   */
  private $em;

  public function setUp()
  {
    // Mock the EntityManager without calling the constructor, (the constructor is private)
    $path_to_em = 'Doctrine\ORM\EntityManager';
    $this->em = $this->getMock($path_to_em, array(), array(), '', false);
    $path_to_ef = 'Hostnet\HostnetCodeQualityBundle\Lib\EntityFactory';
    $this->ef = $this->getMock($path_to_ef, array(), array($this->em));

    $rule1 = new Rule('ConstructorWithNameAsEnclosingClass');
    $violation1_message = 'Classes should not have a constructor method with the same name as the class';
    $violation1 = new Violation($rule1, $violation1_message, 5, 8);
    $rule2 = new Rule('UnusedLocalVariable', 3);
    $violation2_message = 'a message';
    $violation2 = new Violation($rule2, $violation2_message, 7, 7);
    $file = new File('test_pmd_xml_output.txt');
    $code_language = new CodeLanguage('php');

    $this->ef
      ->expects($this->any())
      ->method('getRule')
      ->will($this->returnValue($rule1))
    ;
    $this->ef
      ->expects($this->any())
      ->method('getViolation')
      ->will($this->returnValue($violation1))
    ;
    $this->ef
      ->expects($this->any())
      ->method('getFile')
      ->will($this->returnValue($file))
    ;
    $this->ef
      ->expects($this->any())
      ->method('getCodeLanguage')
      ->will($this->returnValue($code_language))
    ;
  }

  public function testToolOutputParser()
  {
    // Retrieve the test xml output
    $test_file_name = 'test_pmd_xml_output.txt';
    $tool_output_path = __DIR__ . '/' . $test_file_name;
    $tool_output = file_get_contents($tool_output_path);

    $code_file = new CodeFile();
    $code_file->setName($test_file_name);
    $code_file->setExtension('php');

    $empty_array_of_rules = array();

    // Initialize the pmd xml parser and parse the test tool output
    $pmd_xml_parser = new PMDXMLParser($this->ef);
    $report = $pmd_xml_parser->parseToolOutput($tool_output, $code_file, $empty_array_of_rules);

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