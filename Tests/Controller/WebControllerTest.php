<?php

namespace Hostnet\HostnetCodeQualityBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WebControllerTest extends WebTestCase
{

  public function testIndex()
  {
    $client = static::createClient();

    $crawler = $client->request('GET', '/index');

    $this->assertTrue($crawler->filter('html:contains("Home")')->count() > 0);
  }
}
