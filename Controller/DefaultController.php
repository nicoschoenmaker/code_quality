<?php

namespace Hostnet\HostnetCodeQualityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Hostnet\HostnetCodeQualityBundle\Entity\Tool;

class DefaultController extends Controller
{
  /**
   * @Route("/index", name="index")
   * @Template
   */
  public function indexAction()
  {
  }

  /**
   * @Route("/view-diffs", name="view_diffs")
   * @Template
   */
  public function viewDiffsAction()
  {
  }

  /**
   * @Route("/overview", name="overview")
   * @Template
   */
  public function overviewAction()
  {
  }

  /**
   * @Route("/company-profile", name="company_profile")
   * @Template
   */
  public function companyProfileAction()
  {
  }

  /**
   * @Route("/tool-management", name="tool_management")
   * @Template
   */
  public function toolManagementAction()
  {
  }

  /**
   * @Route("/perform-code-quality-review")
   */
   public function performCodeQualityReviewAction(Request $request)
  {
    // Retrieve the Diff file and the Registration bool out of the Request object.
    $diff = $request->get('diff');
    $register = $request->get('register');

    // Get the Doctrine Entity Manager
    $em = $this->getDoctrine()->getManager();

    $processor = $this->get('review_processor');
    $code_quality_reviews = $processor->processReview(
      $diff,
      $register,
      $em,
      $this->retrieveTools()
    );

    return $code_quality_reviews;
  }

  /**
   * Returns a list of code quality tools
   *
   * @return \Doctrine\Common\Collection
   */
  private function retrieveTools()
  {
    return $this->getDoctrine()
      ->getRepository('HostnetCodeQualityBundle:Tool')
      ->findAll()
    ;
  }
}
