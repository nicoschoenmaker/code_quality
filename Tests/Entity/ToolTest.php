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
    $this->assertEquals('0', $whitelisted_exit_codes[0]);
    $this->assertEquals('2', $whitelisted_exit_codes[1]);
    $this->assertEquals('4', $whitelisted_exit_codes[2]);
    $this->assertEquals('5', $whitelisted_exit_codes[3]);
    $this->assertEquals('6', $whitelisted_exit_codes[4]);
  }
}

