<?php

namespace Hostnet\HostnetCodeQualityBundle\Lib;

class CommandLineUtility
{
  CONST TEMP_CQ_DIR_NAME = '/codequality';

  private $temp_code_quality_dir_path = '';

  public function __construct()
  {
    $this->temp_code_quality_dir_path = sys_get_temp_dir() . self::TEMP_CQ_DIR_NAME;
    $this->createTempDir();
  }

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
    if(!(is_dir($this->temp_code_quality_dir_path))) {
      if(!is_file($this->temp_code_quality_dir_path)) {
        $result = mkdir($this->temp_code_quality_dir_path);
        if(!$result) {
          throw new \Exception('Failed to create the Code Quality Temp directory at '
            . $this->temp_code_quality_dir_path);
        }
      } else {
        throw new \Exception("The Code Quality Temp directory at " . $this->temp_code_quality_dir_path
            . " couldn't be created because a file already exists at the given path.");
      }
    }
  }
}
