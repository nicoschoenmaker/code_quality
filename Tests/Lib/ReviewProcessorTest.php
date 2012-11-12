<?php

namespace Hostnet\HostnetCodeQualityBundle\Tests\Lib;

use Hostnet\HostnetCodeQualityBundle\Entity\Tool,
    Hostnet\HostnetCodeQualityBundle\Entity\Ruleset,
    Hostnet\HostnetCodeQualityBundle\Entity\CodeLanguage,
    Hostnet\HostnetCodeQualityBundle\Lib\ReviewProcessor,
    Hostnet\HostnetCodeQualityBundle\Parser\CommandLineUtility,
    Hostnet\HostnetCodeQualityBundle\Parser\ParserFactory,
    Hostnet\HostnetCodeQualityBundle\Parser\DiffParser\GITDiffParser,
    Hostnet\HostnetCodeQualityBundle\Parser\ToolOutputParser\XML\PMDXMLParser,
    Hostnet\HostnetCodeQualityBundle\Parser\OriginalFileRetriever\OriginalFileRetrievalFactory,
    Hostnet\HostnetCodeQualityBundle\Parser\OriginalFileRetriever\CGIT\CGITOriginalFileRetrieverParams,
    Hostnet\HostnetCodeQualityBundle\Tests\Mock\MockEntityFactory;

use Hostnet\HostnetCodeQualityBundle\Parser\EntityProviderInterface;

class ReviewProcessorTest extends \PHPUnit_Framework_TestCase
{
  /**
   * @var Doctrine\ORM\EntityManager
   */
  private $em;

  /**
   * @var CommandLineUtility
   */
  private $clu;

  /**
   * @var ParserFactory
   */
  private $pf;

  /**
   * @var MockEntityFactory
   */
  private $ef;

  /**
   * @var OriginalFileRetrievalFactory
   */
  private $ofrf;

  /**
   * @var ReviewProcessor
   */
  private $processor;

  // Constructs the Processor with the required construct params
  public function setUp()
  {
    // config vars
    $scm = 'git';
    $temp_cq_dir_name = '/tmp/codequality';

    // Mock the EntityManager without calling the constructor, (the constructor is private)
    $path_to_em = 'Doctrine\ORM\EntityManager';
    $this->em = $this->getMock($path_to_em, array(), array(), '', false);
    // CommandLineUtility
    $this->clu = new CommandLineUtility($temp_cq_dir_name);
    // ParserFactory
    $this->pf = new ParserFactory($scm);
    // These parser objects can't be abstract / mocked as the private class variables are set manually.
    $git_diff_parser = new GITDiffParser();
    // Entity Factory
    $this->ef = new MockEntityFactory();
    // OriginalFileRetrievalFactory
    $original_file_retrieval_method = 'cgit';
    $this->ofrf = new OriginalFileRetrievalFactory($original_file_retrieval_method);
    $retrieve_by_cgit_class_path =
      'Hostnet\HostnetCodeQualityBundle\Parser\OriginalFileRetriever\RetrieveByCGIT';
    $retrieve_by_cgit = $this->getMock($retrieve_by_cgit_class_path, array(), array('', ''));
    $path_to_original_file = __DIR__ . '/../ReviewProcessorOriginal.php';
    $retrieve_by_cgit
      ->expects($this->any())
      ->method('retrieveOriginalFile')
      ->will($this->returnValue(file_get_contents($path_to_original_file)))
    ;
    $retrieve_by_cgit
      ->expects($this->once())
      ->method('supports')
      ->will($this->returnValue(true))
    ;
    $this->ofrf->addOriginalFileRetrieverInstance($retrieve_by_cgit);

    $pmd_xml_parser = new PMDXMLParser($this->ef);
    $this->pf->addParserInstance($git_diff_parser);
    $this->pf->addParserInstance($pmd_xml_parser);

    $this->processor = new ReviewProcessor(
      $this->em, $this->ef, $this->ofrf, $this->clu, $this->pf
    );
  }

  public function testProcessCodeQualityReview()
  {
    // Required method params
    $diff = file_get_contents(__DIR__ . '/../review_processor.patch');
    $register = false;
    $repository = 'code_quality';

    $original_file_retrieval_params = new CGITOriginalFileRetrieverParams($repository);
    $review = $this->processor->processReview(
      $diff, $register, $original_file_retrieval_params
    );

    // Test the 1st review
    $reports = $review->getReports();
    $first_file = $reports[0]->getFile();
    $this->assertEquals('ReviewProcessor', $first_file->getName());
    $violations = $reports[0]->getViolations();
    $violation = $violations[0];
    $this->assertEquals('28', $violation->getEndLine());

    // Test the 1st rule of the 2nd violation of the 1st review
    $violation = $violations[2];
    $rule = $violation->getRule();
    $this->assertEquals('ShortVariable', $rule->getName());
  }
}
