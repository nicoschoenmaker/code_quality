<?php

namespace Hostnet\HostnetCodeQualityBundle\Controller;

use Hostnet\HostnetCodeQualityBundle\Rest\RestRequest,
    Hostnet\HostnetCodeQualityBundle\Rest\RestUtils,
    Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityTool,
    Hostnet\HostnetCodeQualityBundle\Entity\SettingsManager,
    Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityReview,
    Hostnet\HostnetCodeQualityBundle\Parser\ToolOutputParser\ToolOutputParserFactory;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use FOS\RestBundle\Controller\FOSRestController,
    FOS\RestBundle\View\View,
    FOS\RestBundle\View\ViewHandler,
    FOS\RestBundle\View\RouteRedirectView;

class DefaultController extends FOSRestController
{
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

  /*
   * @Route("/sendDiff")
   */
  /*public function sendDiff()
  {
    //$request = Request::createFromGlobals();
    //$diff = $request->files->get('diff');
    //$register = $request->request->get('register');
  }*/

  /**
   * @Route("/performCodeQualityReview")
   */
  public function performCodeQualityReview()
  {
    // Retrieve the Diff file and the Registration bool out of the Request object.
    $rest_request = RestUtils::processRequest();
    $diff = $rest_request->getRequestVars('diff');
    $register = $rest_request->getRequestVars('register');
    // TODO Display a loading bar as long as the system is processing the diff, Ajax?

    // Parse the diff into CodeFile objects
    $diff_parser = SettingsManager::getInstance()->getDiffParser();
    $code_files = $diff_parser->parseDiff($diff);

    // Call the ToolOutputParserFactory to create the ToolOutputParser
    $tool_output_parser_factory = ToolOutputParserFactory::getInstance();
    $code_quality_tools = SettingsManager::getInstance()->getCodeQualityTools();

    // Save the reviews if they should be registered
    //$em = $this->getDoctrine()->getManager();

    $code_quality_reviews = array();
    // Send each code file to a code quality review tool
    foreach($code_files as $code_file) {
      foreach($code_quality_tools as $code_language => $code_quality_tool) {
        if($code_file->getExtension() == $code_language) {
          // Let the appropriate static code quality tool process the file and return the output
          $tool_output = $code_quality_tool->processFile($code_file);
          // Parse tool output into CodeQualityReview objects
          // TODO FIX
          $code_quality_tool->setName('PMD');
          $tool_output_parser =
            $tool_output_parser_factory->getToolOutputParser($code_quality_tool);
          $code_quality_review =
            $tool_output_parser->parseToolOutput($tool_output[1], $code_file);
          //$em->persist($code_quality_review);
          //$em->flush();
          $code_quality_reviews[] = $code_quality_review;
        }
      }
    }
    // Data has to contain the feedback that the client will visualize.
    return $code_quality_reviews;
  }
}
