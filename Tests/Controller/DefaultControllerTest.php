<?php

namespace Hostnet\HostnetCodeQualityBundle\Tests\Controller;

use Hostnet\HostnetCodeQualityBundle\Controller\DefaultController,
    Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityTool,
    Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityReview,
    Hostnet\HostnetCodeQualityBundle\Rest\RestRequestClient;

class DefaultControllerTest extends \PHPUnit_Framework_TestCase
{
  public function testSendDiffToAPI()
  {
    $url = 'http://codequality.rickp.ontw.hostnetbv.nl/app_dev.php/performCodeQualityReview';
    $post = array(
        'diff' => file_get_contents(__DIR__ . '/../test_git_patch.patch'),
        'register' => false
    );

    // TODO Research if authentication is required
    $rest_request = new RestRequestClient($url, 'POST', $post);
    $rest_request->execute();

    $code_quality_reviews = json_decode($rest_request->getResponseBody(), true);
    $this->assertEquals('http-fetch', $code_quality_reviews[0]['file_name']);
  }

  /**
   * Code Quality Tools can be added through the Web-UI with their corresponding
   * path_to_tool, command and output format.
   * TODO: Factory pattern weghalen, zelfde impl voor alle tools.
   */

  /**
   * TODO: Log van tool-management activiteit bijhouden (insert / update / delete).
   */
  /*public function testCQToolOutputParsing()
  {

  }*/
}
