<?php

namespace Hostnet\HostnetCodeQualityBundle\Tests\Lib;

use Hostnet\HostnetCodeQualityBundle\Lib\ReviewProcessor,
    Hostnet\HostnetCodeQualityBundle\Parser\ParserFactory,
    Hostnet\HostnetCodeQualityBundle\Parser\DiffParser\GITDiffParser,
    Hostnet\HostnetCodeQualityBundle\Parser\ToolOutputParser\XML\PMDXMLParser,
    Hostnet\HostnetCodeQualityBundle\Entity\Tool,
    Hostnet\HostnetCodeQualityBundle\Entity\File,
    Hostnet\HostnetCodeQualityBundle\Entity\Rule,
    Hostnet\HostnetCodeQualityBundle\Entity\Violation,
    Hostnet\HostnetCodeQualityBundle\Entity\CodeLanguage;

use Hostnet\HostnetCodeQualityBundle\Lib\EntityFactory;

class ReviewProcessorTest extends \PHPUnit_Framework_TestCase
{
  /**
   * @var Hostnet\HostnetCodeQualityBundle\Lib\CodeQualityReviewProcessor
   */
  private $processor;

  private $em;

  private $ef;

  // Constructs the Processor with the required construct params
  public function setUp()
  {
    // Mock the EntityManager without calling the constructor, (the constructor is private)
    $path_to_em = 'Doctrine\ORM\EntityManager';
    $this->em = $this->getMock($path_to_em, array(), array(), '', false);
    // CommandLineUtility
    $path_to_clu = 'Hostnet\HostnetCodeQualityBundle\Lib\CommandLineUtility';
    $clu = $this->getMock($path_to_clu);
    // ParserFactory
    $parser_factory = new ParserFactory();
    // These parser objects can't be abstract / mocked as the private class variables are set manually.
    $git_diff_parser = new GITDiffParser();
    $path_to_ef = 'Hostnet\HostnetCodeQualityBundle\Lib\EntityFactory';
    $this->ef = $this->getMock($path_to_ef, array(), array($this->em));

    $rule1 = new Rule('LongVariable');
    $violation1_message = 'Classes should not have a constructor method with the same name as the class';
    $violation1 = new Violation($rule1, $violation1_message, 5, 8);
    $rule2 = new Rule('LongVariable', 3);
    $violation2_message = 'a message';
    $violation2 = new Violation($rule2, $violation2_message, 7, 7);
    $file = new File('http-fetch');
    $code_language = new CodeLanguage('php');

    $this->ef
      ->expects($this->any())
      ->method('getFile')
      ->will($this->returnValue($file))
    ;
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
      ->method('getCodeLanguage')
      ->will($this->returnValue($code_language))
    ;

    $pmd_xml_parser = new PMDXMLParser($this->ef);
    $parser_factory->addParserInstance($git_diff_parser);
    $parser_factory->addParserInstance($pmd_xml_parser);

    // config vars
    $scm = 'git';
    // TODO Fix url
    $raw_file_url_mask = 'http://cgit.hostnetbv.nl/cgit/aurora/www.git/plain/apps/aurora/modules/uml/actions/actions.class.php';

    $this->processor = new ReviewProcessor(
      $clu, $parser_factory, $scm, $raw_file_url_mask
    );
  }

  public function testProcessCodeQualityReview()
  {
    // Required method params
    $diff = file_get_contents(__DIR__ . '/../test_git_patch.patch');
    $register = false;

    $PHPMD = new Tool(
      'pmd',
      '~/projects/code_quality_tools/phpmd',
      '/usr/local/zend/bin/phpmd',
      'xml',
      'codesize,unusedcode,naming'
    );
    $PHPMD->getSupportedLanguages()->add('php');
    $tools = array($PHPMD);

    $review = $this->processor->processReview(
      $diff, $register, $this->em, $tools
    );

    //$this->assertEquals('test', var_dump($review));
    // Test the 1st review
    $reports = $review->getReports();
    $first_file = $reports[0]->getFile();
    $this->assertEquals('http-fetch', $first_file->getName());
    $violations = $reports[0]->getViolations();
    $violation = $violations[0];
    $this->assertEquals('8', $violation->getEndLine());

    // Test the 1st rule of the 2nd violation of the 1st review
    $violation = $violations[2];
    $rule = $violation->getRule();
    $this->assertEquals('LongVariable', $rule->getName());
  }
}
