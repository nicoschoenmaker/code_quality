<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser\Diff;

use Symfony\Component\Process\Process;

use Hostnet\HostnetCodeQualityBundle\Entity\Tool;

/**
 * This object contains all the parsed Diff data.
 *
 * @author rprent
 */
class DiffFile
{
  CONST TEMP_DIFF_FILE_PREFIX = 'cq';

  /**
   * @var string $name
   */
  private $name;

  /**
   * @var string $extension
   */
  private $extension;

  /**
   * @var string $index
   */
  private $index;

  /**
   * The relative path of the original file
   *
   * @var string $source
   */
  private $source;

  /**
   * The revision index of the original file
   *
   * @var string $source_revision
   */
  private $source_revision;

  /**
   * @var array $diff_code_blocks
   */
  private $diff_code_blocks;

  /**
   * @var string
   */
  private $diff_output;

  /**
   * @var string
   */
  private $original_output;


  /**
   * Set name
   *
   * @param string $name
   * @return DiffFile
   */
  public function setName($name)
  {
      $this->name = $name;
  }

  /**
   * Get name
   *
   * @return string
   */
  public function getName()
  {
      return $this->name;
  }

  /**
   * Set extension
   *
   * @param string $extension
   * @return DiffFile
   */
  public function setExtension($extension)
  {
    $this->extension = $extension;
  }

  /**
   * Get extension
   *
   * @return string
   */
  public function getExtension()
  {
    return $this->extension;
  }

  /**
   * Set index
   *
   * @param string $index
   * @return DiffFile
   */
  public function setIndex($index)
  {
    $this->index = $index;
  }

  /**
   * Get index
   *
   * @return string
   */
  public function getIndex()
  {
    return $this->index;
  }

  /**
   * Set source
   *
   * @param string $source
   * @return DiffFile
   */
  public function setSource($source)
  {
    $this->source = $source;
  }

  /**
   * Get source
   *
   * @return string
   */
  public function getSource()
  {
    return $this->source;
  }

  /**
   * Set source_revision
   *
   * @param string $source_revision
   * @return DiffFile
   */
  public function setSourceRevision($source_revision)
  {
    $this->source_revision = $source_revision;
  }

  /**
   * Get source_revision
   *
   * @return string
   */
  public function getSourceRevision()
  {
    return $this->source_revision;
  }

  /**
   * Set diff code blocks
   *
   * @param string $diff_code_blocks
   * @return DiffFile
   */
  public function setDiffCodeBlocks($diff_code_blocks)
  {
      $this->diff_code_blocks = $diff_code_blocks;
  }

  /**
   * Get diff code blocks
   *
   * @return array
   */
  public function getDiffCodeBlocks()
  {
      return $this->diff_code_blocks;
  }

  /**
   * Get the diff output
   *
   * @return string
   */
  public function getDiffOutput()
  {
    return $this->diff_output;
  }

  /**
   * Get the original output
   *
   * @return string
   */
  public function getOriginalOutput()
  {
    return $this->original_output;
  }

  /**
   * Returns all the code in a diff file
   *
   * @return string
   */
  public function getEntireCode()
  {
    $entire_code = '';
    foreach($this->diff_code_blocks as $diff_code_block) {
      $entire_code .= $diff_code_block->getCode();
    }
    return $entire_code;
  }

  /**
   * Process both the diff file as the original file through the scan process
   *
   * @param Tool $tool
   * @param string $original_file
   * @param CommandLineUtility $clu
   * @return array
   */
  public function processFile(Tool $tool,
    $original_file, $temp_code_quality_dir_path)
  {
    $this->diff_output = $this->scanCode(
      $tool,
      $this->getEntireCode(),
      $temp_code_quality_dir_path
    );
    $this->original_output = $this->scanCode(
      $tool,
      $original_file,
      $temp_code_quality_dir_path
    );
  }

  /**
   * Writes the code into a temp file to be able
   * to scan it with the code quality tool
   *
   * @param Tool $tool
   * @param string $code
   * @param CommandLineUtility $clu
   * @return string $code_output
   */
  private function scanCode(Tool $tool, $code, $temp_code_quality_dir_path)
  {
    // Creates the temp file
    $temp_code_file_path =
      $this->tempnam($temp_code_quality_dir_path, self::TEMP_DIFF_FILE_PREFIX);

    // Opens a file stream reader with write permissions and
    // writes the code into the temp file
    file_put_contents($temp_code_file_path, $code);
    // Let the temp file go through the Code Quality Tool scan process by
    // executing the following command line command
    $command_line_string =
      escapeshellarg($tool->getCallCommand()) .          ' ' .
      escapeshellarg($temp_code_file_path) .             ' ' .
      escapeshellarg(strtolower($tool->getFormat())) .   ' '
    ;
    // Each argument gets added as a single argument to check it separately.
    // Some tools have just one string arg and some have multiple.
    foreach($tool->getArguments() as $argument) {
      $command_line_string .= escapeshellarg($argument->getName());
    }
    $process = new Process($command_line_string);
    $process->run();
    // TODO Remove comments as soon as the original file retrieval works,
    // at the moment it still throws an error as the test original file
    // is baaad mkay
    /*if(!$process->isSuccessful()) {
    throw new \RuntimeException($process->getErrorOutput());
    }*/
    // Remove the temp file
    unlink($temp_code_file_path);

    return $process->getOutput();
  }

  /**
   * Creates a file and returns the unique file name.
   * Added this method as the php tempnam() function
   * might be disabled as it is disabled by default.
   *
   * @param string $dir
   * @param string $prefix
   * @return string
   */
  public function tempnam($dir, $prefix) {

    if(function_exists('tempnam')) {
      return tempnam(
        $dir,
        $prefix
      );
    }

    $name = $prefix . md5(time() . rand());
    $handle = fopen($dir . '/' . $name, 'w');
    fclose($handle);

    return $dir . '/' . $name;
  }
}
