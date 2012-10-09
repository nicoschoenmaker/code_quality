<?php

namespace Hostnet\HostnetCodeQualityBundle\Tests\Parser\DiffParser;

use Hostnet\HostnetCodeQualityBundle\Parser\DiffParser\SVNDiffParser;

class SVNDiffParserTest extends \PHPUnit_Framework_TestCase
{
  /**
   * Check if the svn diff is correctly parsed into CodeFile objects containing CodeBlock objects
   */
  public function testParseDiff()
  {
    //Load test patch file
    $diff_location = __DIR__ . '/../../test_svn_patch.patch';
    $diff = file_get_contents($diff_location);
    $svn_diff_parser = new SVNDiffParser('svn');
    $code_files = $svn_diff_parser->parseDiff($diff);

    //first code file
    $code_file = $code_files[0];
    $this->assertEquals('rules.txt', $code_file->getIndex());
    $this->assertEquals('rules.txt', $code_file->getName());
    $this->assertEquals('txt', $code_file->getExtension());
    $this->assertEquals('rules.txt', $code_file->getSource());
    $this->assertEquals('revision 2', $code_file->getSourceRevision());
    $code_blocks = $code_file->getCodeBlocks();
    //first code block of first code file
    $code_block = $code_blocks[0];
    $this->assertEquals('1,4', $code_block->getBeginLine());
    $this->assertEquals('1,4', $code_block->getEndLine());
    //second code block of first code file
    $code_block = $code_blocks[1];
    $this->assertEquals('100,45', $code_block->getBeginLine());
    $this->assertEquals('105,55', $code_block->getEndLine());

    //second code file
    $code_file = $code_files[1];
    $this->assertEquals('Test/TestBundle/Tests/test.php', $code_file->getIndex());
    $this->assertEquals('test.php', $code_file->getName());
    $this->assertEquals('php', $code_file->getExtension());
    $this->assertEquals('Test/TestBundle/Tests/test.php', $code_file->getSource());
    $this->assertEquals('revision 200', $code_file->getSourceRevision());
    $code_blocks = $code_file->getCodeBlocks();
    //first code block of second code file
    $code_block = $code_blocks[0];
    $this->assertEquals('50,6', $code_block->getBeginLine());
    $this->assertEquals('50,9', $code_block->getEndLine());
  }
}
