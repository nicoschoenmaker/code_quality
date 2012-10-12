<?php

namespace Hostnet\HostnetCodeQualityBundle\Tests\Parser\DiffParser;

use Hostnet\HostnetCodeQualityBundle\Parser\DiffParser\SVNDiffParser;

class SVNDiffParserTest extends \PHPUnit_Framework_TestCase
{
  /**
   * Check if the svn diff is correctly parsed into DiffFile objects containing DiffCodeBlock objects
   */
  public function testParseDiff()
  {
    //Load test patch file
    $diff_location = __DIR__ . '/../../test_svn_patch.patch';
    $diff = file_get_contents($diff_location);
    $svn_diff_parser = new SVNDiffParser('svn');
    $diff_files = $svn_diff_parser->parseDiff($diff);

    //first code file
    $diff_file = $diff_files[0];
    $this->assertEquals('rules.txt', $diff_file->getIndex());
    $this->assertEquals('rules.txt', $diff_file->getName());
    $this->assertEquals('txt', $diff_file->getExtension());
    $this->assertEquals('rules.txt', $diff_file->getSource());
    $this->assertEquals('revision 2', $diff_file->getSourceRevision());
    $diff_code_blocks = $diff_file->getDiffCodeBlocks();
    //first code block of first code file
    $diff_code_block = $diff_code_blocks[0];
    $this->assertEquals('1,4', $diff_code_block->getBeginLine());
    $this->assertEquals('1,4', $diff_code_block->getEndLine());
    //second code block of first code file
    $diff_code_block = $diff_code_blocks[1];
    $this->assertEquals('100,45', $diff_code_block->getBeginLine());
    $this->assertEquals('105,55', $diff_code_block->getEndLine());

    //second code file
    $diff_file = $diff_files[1];
    $this->assertEquals('Test/TestBundle/Tests/test.php', $diff_file->getIndex());
    $this->assertEquals('test.php', $diff_file->getName());
    $this->assertEquals('php', $diff_file->getExtension());
    $this->assertEquals('Test/TestBundle/Tests/test.php', $diff_file->getSource());
    $this->assertEquals('revision 200', $diff_file->getSourceRevision());
    $diff_code_blocks = $diff_file->getDiffCodeBlocks();
    //first code block of second code file
    $diff_code_block = $diff_code_blocks[0];
    $this->assertEquals('50,6', $diff_code_block->getBeginLine());
    $this->assertEquals('50,9', $diff_code_block->getEndLine());
  }
}
