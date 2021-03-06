<?php

namespace Hostnet\Bundle\HostnetCodeQualityBundle\Parser;

use Symfony\Component\Filesystem\Exception\IOException;

/**
 * The Command Line Utility class handles command line calls
 * like creating the temp code quality dir.
 *
 * @author rprent
 */
class CommandLineUtility
{

  /**
   * The path to the temp code quality dir
   *
   * @var string
   */
  private $temp_code_quality_dir_path = '';

  public function __construct($temp_cq_dir_name)
  {
    $this->temp_code_quality_dir_path = $temp_cq_dir_name;
    if(realpath($this->temp_code_quality_dir_path) === false) {
      $this->createTempDir();
    }
  }

  /**
   * Gets the temp_code_quality_dir_path
   *
   * @return string
   */
  public function getTempCodeQualityDirPath()
  {
    return $this->temp_code_quality_dir_path;
  }

  /**
   * Creates the temp code quality directory so temp code files can be inserted
   * for the code quality tool processing of the code.
   *
   * @throws Exception
   */
  private function createTempDir()
  {
    clearstatcache();
    if(!is_file($this->temp_code_quality_dir_path)) {
      if(@mkdir($this->temp_code_quality_dir_path, 0777, true) === false) {
        throw new IOException('Failed to create the Code Quality Temp directory at '
          . $this->temp_code_quality_dir_path);
      }
    } else {
      throw new IOException("The Code Quality Temp directory at " . $this->temp_code_quality_dir_path
        . " couldn't be created because a file already exists at the given path.");
    }
  }
}
