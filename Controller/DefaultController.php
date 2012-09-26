<?php

namespace Hostnet\HostnetCodeQualityBundle\Controller;

use Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityTool,
    Hostnet\HostnetCodeQualityBundle\Parser\ParserFactory;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
  private $code_quality_tools = array();
  private $scm = '';
  private $raw_file_url_mask = '';

  /**
   * @Route("/index")
   * @Template()
   */
  public function indexAction()
  {
    return $this->render('HostnetCodeQualityBundle:Default:index.html.twig');
  }

  /**
   * @Route("/viewDiffs")
   * @Template()
   */
  public function viewDiffsAction()
  {
    return $this->render('HostnetCodeQualityBundle:Default:viewDiffs.html.twig');
  }

  /**
   * @Route("/overview")
   * @Template()
   */
  public function overviewAction()
  {
    return $this->render('HostnetCodeQualityBundle:Default:overview.html.twig');
  }

  /**
   * @Route("/companyProfile")
   * @Template()
   */
  public function companyProfileAction()
  {
    return $this->render('HostnetCodeQualityBundle:Default:companyProfile.html.twig');
  }

  /**
   * @Route("/toolManagement")
   * @Template()
   */
  public function toolManagementAction()
  {
    return $this->render('HostnetCodeQualityBundle:Default:toolManagement.html.twig');
  }

  /**
   * @Route("/performCodeQualityReviewAction")
   */
  public function performCodeQualityReviewAction(Request $request)
  {
    $this->readConfigSettings();

    // Retrieve the Diff file and the Registration bool out of the Request object.
    $diff = $request->get('diff');
    $register = $request->get('register');

    // Call the ParserFactory to create the Parsers
    $parser_factory = ParserFactory::getInstance();

    // Parse the diff into CodeFile objects
    $diff_parser = $parser_factory->getParserInstance($this->scm . 'DiffParser', $parser_factory::DIFF_PARSER_DIR);
    $code_files = $diff_parser->parseDiff($diff);

    // Retrieve the Code Quality Tools
    $code_quality_tools = $this->getCodeQualityTools();
    // Get the Doctrine Entity Manager
    $em = $this->getDoctrine()->getManager();
    $code_quality_reviews = array();
    // Send each code file to their specific code quality review tool based on the extension
    foreach($code_files as $code_file) {
      foreach($code_quality_tools as $code_language => $code_quality_tool) {
        if($code_file->getExtension() == $code_language) {
          // Retrieve the original code file based on the repository raw file url mask
          // and the new diff file name + parent revision number
          $original_file = file_get_contents($this->raw_file_url_mask);
          //$original_file = file_get_contents($this->raw_file_url_mask
          //  . $code_file->getSource()
          //  . '?id2='
          //  . $code_file->getSourceRevision());
          // Let the appropriate static code quality tool process the file and return the output
          $tool_output = $code_quality_tool->processFile($code_file, $original_file);
          // Generate the Tool Output Parser with a Factory and
          // Parse tool output into CodeQualityReview objects
          $tool_output_parser_class_name =
            strtoupper($code_quality_tool->getName()
            . $code_quality_tool->getFormat())
            . 'Parser';
          $tool_output_parser = $parser_factory
            ->getParserInstance(
              $tool_output_parser_class_name,
              $parser_factory::TOOL_OUTPUT_PARSER_DIR
            );
          $code_quality_review = $tool_output_parser
            ->parseToolOutput($tool_output['diff_output'], $code_file);
          // Save the reviews if they should be registered
          if($register && $code_file->getExtension() != 'js') {
            $em->persist($code_quality_review);
          }
          $code_quality_reviews[] = $code_quality_review;
        }
      }
    }

    return $code_quality_reviews;
  }

  /**
   * Returns a list of code quality tools
   *
   * @return CodeQualityTool array
   */
  public function getCodeQualityTools()
  {
    if(!$this->code_quality_tools) {
      // TODO Remove when the Code Quality Tools are extracted from the DB
      $PHPMD = new CodeQualityTool();
      $PHPMD->setName('PMD');
      $PHPMD->setPathToTool('~/projects/code_quality_tools/phpmd');
      $PHPMD->setCallCommand('/usr/local/zend/bin/phpmd');
      $PHPMD->setFormat('xml');
      $PHPMD->setRulesets('codesize,unusedcode,naming');
      $this->code_quality_tools['php'] = $PHPMD;

      // TODO Retrieve the CodeQualityTools from the DB when tools can be added through the web-ui
      /*$this->code_quality_tools = $this->getDoctrine()
       ->getRepository('HostnetCodeQualityBundle:CodeQualityTool')
      ->findAll();*/
    }

    return $this->code_quality_tools;
  }

  /**
   * Reads the hostnet_code_quality config settings set in app/config/parameters.yml
   */
  public function readConfigSettings()
  {
    if(!$this->scm) {
      $this->scm = strtoupper($this->container->getParameter('hostnet_code_quality.scm'));
    }

    if(!$this->raw_file_url_mask) {
      $this->raw_file_url_mask = $this->container->getParameter('hostnet_code_quality.raw_file_url_mask');
    }
  }
}
