<?php

namespace Hostnet\HostnetCodeQualityBundle\Tests\Controller;

use Hostnet\HostnetCodeQualityBundle\Tests\Controller\RestRequestClient;

class DefaultControllerTest extends \PHPUnit_Framework_TestCase
{
  public function testSendDiffToAPI()
  {
    $url = 'http://codequality.rickp.ontw.hostnetbv.nl/app_dev.php/performCodeQualityReviewAction';
    $post = array(
      'diff' => file_get_contents(__DIR__ . '/../test_git_patch.patch'),
      'register' => false
    );

    // TODO Research how authentication should be implemented
    $rest_request = new RestRequestClient($url, 'POST', $post);
    $rest_request->execute();

    $code_quality_reviews = json_decode($rest_request->getResponseBody(), true);
    $this->assertEquals('http-fetch', $code_quality_reviews[0]['file_name']);
    $this->assertEquals('8', $code_quality_reviews[0]['code_quality_review_violations'][0]['end_line']);
    $this->assertEquals('LongVariable', $code_quality_reviews[0]['code_quality_review_violations'][2]
      ['code_quality_metric']['code_quality_metric_ruleset']['code_quality_metric_rules'][0]['name']);
  }
}
