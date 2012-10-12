<?php

namespace Hostnet\HostnetCodeQualityBundle\Tests\Parser;

use Hostnet\HostnetCodeQualityBundle\Parser\ParserFactory,
    Hostnet\HostnetCodeQualityBundle\Parser\DiffParser\GITDiffParser,
    Hostnet\HostnetCodeQualityBundle\Parser\ToolOutputParser\XML\PMDXMLParser;

class ParserFactoryTest extends \PHPUnit_Framework_TestCase
{
  /**
   * The configured Source Control Management system setting
   *
   * @var string
   */
  private $scm;

  /**
   * @var EntityManager
   */
  private $em;

  /**
   * @var ParserFactory
   */
  private $pf;

  /**
   * @var EntityFactory
   */
  private $ef;

  /**
   * @var GITDiffParser
   */
  private $diff_parser;

  /**
   * @var PMDXMLParser
   */
  private $tool_output_parser;

  public function setUp()
  {
    $this->scm = 'git';
    // Mock the EntityManager without calling the constructor, (the constructor is private)
    $path_to_em = 'Doctrine\ORM\EntityManager';
    $this->em = $this->getMock($path_to_em, array(), array(), '', false);
    $this->pf = new ParserFactory($this->scm);
    $path_to_ef = 'Hostnet\HostnetCodeQualityBundle\Lib\EntityFactory';
    $this->ef = $this->getMock($path_to_ef, array(), array($this->em));

    // Instantiate the Parsers, non-abstract classes had to be used as the
    // $resource and $additional_properties properties are filled at construction
    $this->diff_parser = new GITDiffParser();
    $this->tool_output_parser = new PMDXMLParser($this->ef);
  }

  public function testParserSupportsFunctionality()
  {
    // Test if the ParserFactory accepts and processes a (Diff)Parser
    $this->pf->addParserInstance($this->diff_parser);
    $parser = $this->pf->getDiffParserInstance($this->scm);
    $this->assertTrue($parser->supports($this->scm));

    // Test if the ParserFactory accepts and processes a ToolOutputParser
    $this->pf->addParserInstance($this->tool_output_parser);
    $resource = 'pmd';
    $additional_properties = array('format' => 'xml');
    $parser = $this->pf->getToolOutputParserInstance($resource, $additional_properties);
    $this->assertTrue($parser->supports($resource, $additional_properties));
  }
}
