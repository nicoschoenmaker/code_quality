<?php

namespace Hostnet\HostnetCodeQualityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Hostnet\HostnetCodeQualityBundle\Entity\Tool;

/**
 * The Default Controller
 *
 * @author rprent
 */
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
   * Performs the Code Quality Review process.
   *
   * @Route("/perform-code-quality-review")
   * @param Request $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function performCodeQualityReviewAction(Request $request)
  {
    // Retrieve the Diff file and the Registration bool out of the Request object.
    $diff = $request->get('diff');
    $register = $request->get('register');

    $processor = $this->get('review_processor');
    $review = $processor->processReview(
      $diff,
      $register
    );

    $response = new Response(json_encode($review));
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }
}
