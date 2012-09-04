<?php

namespace Hostnet\HostnetCodeQualityBundle\Controller;

use Hostnet\HostnetCodeQualityBundle\Parser\SVNDiffParser;
use Hostnet\HostnetCodeQualityBundle\Parser\GitDiffParser;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Hostnet\HostnetCodeQualityBundle\Parser;

class DefaultController extends Controller
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

  public function parseDiff($diff)
  {
    //If SVN
    //SVNDiffParser::parseDiff($diff);
    //If Git
    GitDiffParser::parseDiff($diff);
  }
}
