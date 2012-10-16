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
   * @var ReviewProcessor
   */
  private $processor;

  // Constructs the Processor with the required construct params
  public function setUp()
  {
    // config vars
    $scm = 'git';
    // TODO Fix url
    $raw_file_url_mask = 'http://cgit.hostnetbv.nl/cgit/aurora/www.git/plain/apps/aurora/modules/uml/actions/actions.class.php';
    $temp_cq_dir_name = 'codequality';

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

    $pmd_xml_parser = new PMDXMLParser($this->ef);
    $this->pf->addParserInstance($git_diff_parser);
    $this->pf->addParserInstance($pmd_xml_parser);

    $this->processor = new ReviewProcessor(
      $this->em, $this->ef, $this->clu, $this->pf, $raw_file_url_mask
    );
  }

  public function testProcessCodeQualityReview()
  {
    // Required method params
    $diff = file_get_contents(__DIR__ . '/../test_git_patch.patch');
    $register = false;

    $review = $this->processor->processReview(
      $diff, $register
    );

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
