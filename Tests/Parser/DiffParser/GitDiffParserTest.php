<?php

namespace Hostnet\HostnetCodeQualityBundle\Tests\Parser\DiffParser;

use Hostnet\HostnetCodeQualityBundle\Parser\DiffParser\GitDiffParser;

class GitDiffParserTest extends \PHPUnit_Framework_TestCase
{
  /**
   * Check if the svn diff is correctly parsed into CodeFile objects containing CodeBlock objects
   */
  public function testParseDiff()
  {
    //Load test patch file
    $diff_location = __DIR__ . '/../../test_git_patch.patch';
    $diff = file_get_contents($diff_location);
    $gitDiffParser = new GitDiffParser();
    $code_files = $gitDiffParser->parseDiff($diff);

    //first code file
    $code_file = $code_files[0];
    $this->assertEquals('http-fetch', $code_file->getName());
    $this->assertEquals('php', $code_file->getExtension());
    $this->assertEquals('test1/test2/builtin-http-fetch.php', $code_file->getSource());
    $this->assertEquals('f3e63d7', $code_file->getSourceRevision());
    $this->assertEquals('test1/test2/http-fetch.php', $code_file->getDestination());
    $this->assertEquals('e8f44ba', $code_file->getDestinationRevision());
    $code_blocks = $code_file->getCodeBlocks();
    //first code block of first code file
    $code_block = $code_blocks[0];
    $this->assertEquals('1,8', $code_block->getBeginLine());
    $this->assertEquals('1,9', $code_block->getEndLine());
    //second code block of first code file
    $code_block = $code_blocks[1];
    $this->assertEquals('18,6', $code_block->getBeginLine());
    $this->assertEquals('19,8', $code_block->getEndLine());

    //second code file
    $code_file = $code_files[1];
    $this->assertEquals('een_test_bestand', $code_file->getName());
    $this->assertEquals('php', $code_file->getExtension());
    $this->assertEquals('test1/test2/een_test_bestand.php', $code_file->getSource());
    $this->assertEquals('a3e63d9', $code_file->getSourceRevision());
    $this->assertEquals('test1/test2/een_test_bestand.php', $code_file->getDestination());
    $this->assertEquals('a8f44b9', $code_file->getDestinationRevision());
    $code_blocks = $code_file->getCodeBlocks();
    //first code block of first code file
    $code_block = $code_blocks[0];
    $this->assertEquals('10,8', $code_block->getBeginLine());
    $this->assertEquals('10,9', $code_block->getEndLine());
    //second code block of first code file
    $code_block = $code_blocks[1];
    $this->assertEquals('180,6', $code_block->getBeginLine());
    $this->assertEquals('190,8', $code_block->getEndLine());
  }
}
