<?php

namespace Hostnet\HostnetCodeQualityBundle\Tests\Parser\DiffParser;

use Hostnet\HostnetCodeQualityBundle\Parser\DiffParser\GITDiffParser;

class GITDiffParserTest extends \PHPUnit_Framework_TestCase
{
  /**
   * Check if the svn diff is correctly parsed into DiffFile objects containing DiffCodeBlock objects
   */
  public function testParseDiff()
  {
    //Load test patch file
    $diff_location = __DIR__ . '/../../test_git_patch.patch';
    $diff = file_get_contents($diff_location);
    $git_diff_parser = new GITDiffParser('git');
    $diff_files = $git_diff_parser->parseDiff($diff);

    //first code file
    $diff_file = $diff_files[0];
    $this->assertEquals('http-fetch', $diff_file->getName());
    $this->assertEquals('php', $diff_file->getExtension());
    $this->assertEquals('test1/test2/builtin-http-fetch.php', $diff_file->getSource());
    $this->assertEquals('f3e63d7', $diff_file->getSourceRevision());
    $diff_code_blocks = $diff_file->getDiffCodeBlocks();
    //first code block of first code file
    $diff_code_block = $diff_code_blocks[0];
    $this->assertEquals('1', $diff_code_block->getBeginLine());
    $this->assertEquals('1,9', $diff_code_block->getEndLine());
    //second code block of first code file
    $diff_code_block = $diff_code_blocks[1];
    $this->assertEquals('18', $diff_code_block->getBeginLine());
    $this->assertEquals('19,8', $diff_code_block->getEndLine());

    //second code file
    $diff_file = $diff_files[1];
    $this->assertEquals('een_test_bestand', $diff_file->getName());
    $this->assertEquals('php', $diff_file->getExtension());
    $this->assertEquals('test1/test2/een_test_bestand.php', $diff_file->getSource());
    $this->assertEquals('a3e63d9', $diff_file->getSourceRevision());
    $diff_code_blocks = $diff_file->getDiffCodeBlocks();
    //first code block of first code file
    $diff_code_block = $diff_code_blocks[0];
    $this->assertEquals('10', $diff_code_block->getBeginLine());
    $this->assertEquals('10,9', $diff_code_block->getEndLine());
    //second code block of first code file
    $diff_code_block = $diff_code_blocks[1];
    $this->assertEquals('180', $diff_code_block->getBeginLine());
    $this->assertEquals('190,8', $diff_code_block->getEndLine());
  }
}
