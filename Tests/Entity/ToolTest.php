<?php

namespace Hostnet\HostnetCodeQualityBundle\Tests\Entity;

use Hostnet\HostnetCodeQualityBundle\Entity\Tool;

class ToolTest extends \PHPUnit_Framework_TestCase
{
  /**
   * @var Tool
   */
  private $tool;

  public function setUp()
  {
    $this->tool = new Tool(
      'pmd',
      '~/projects/code_quality_tools/phpmd',
      '/usr/local/zend/bin/phpmd',
      'xml'
    );
    $this->tool->setWhitelistedExitCodes('0, 2 ,4 , 5, 6');
  }

  public function testGetWhitelistedExitCode()
  {
    $whitelisted_exit_codes = $this->tool->getWhitelistedExitCodes();
    $expected_whitelisted_exit_codes = array(0, 2, 4, 5, 6);
    $this->assertEquals($expected_whitelisted_exit_codes, $whitelisted_exit_codes);
  }
}

