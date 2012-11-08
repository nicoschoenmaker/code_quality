<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser\Diff;

use Symfony\Component\Process\Process;

use Hostnet\HostnetCodeQualityBundle\Entity\Tool;

use RuntimeException;

/**
 * This object contains all the parsed Diff data.
 *
 * @author rprent
 */
class DiffFile
{
  CONST DEV_NULL = '/dev/null';
  CONST ORIGINAL_EXTENSION = '.orig';
  CONST TEMP_DIFF_FILE_PREFIX = 'cq';

  /**
   * An array of the properties that are required
   * to be extracted in the diff parsing process.
   *
   * @var array
   */
  private $required_diff_parsing_properties =
    array('name', 'extension', 'source', 'source_revision');

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
  private $diff_file;

  /**
   * @var string
   */
  private $original_file;

  /**
   * @var string
   */
  private $temp_diff_file_path;

  /**
   * @var string
   */
  private $temp_original_file_path;

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
   * Get the diff file
   *
   * @return string
   */
  public function getDiffFile()
  {
    return $this->diff_file;
  }

  /**
   * Set the diff file
   *
   * @param string $diff_file
   */
  public function setDiffFile($diff_file)
  {
    $this->diff_file = $diff_file;
  }

  /**
   * Creates a new diff file, which means it
   * has no original file. Also removes all the
   * '+' diff characters
   */
  public function createTempDiffFile($temp_code_quality_dir_path)
  {
    $lines_of_code = explode(PHP_EOL, $this->diff_file);
    foreach($lines_of_code as $key => $line_of_code) {
      $lines_of_code[$key] = substr($line_of_code, 1);
    }
    $code = implode("\n", $lines_of_code);

    $this->temp_diff_file_path = $this->createTempFile($temp_code_quality_dir_path, $code);
  }

  /**
   * Get the original file
   *
   * @return string
   */
  public function getOriginalFile()
  {
    return $this->original_file;
  }

  /**
   * Set the original file
   *
   * @param string $original_file
   */
  public function setOriginalFile($original_file)
  {
    $this->original_file = $original_file;
  }

  /**
   * Get the Diff output
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
  public function getEntireDiff()
  {
    $entire_diff = '';
    foreach($this->diff_code_blocks as $diff_code_block) {
      $entire_diff .= $diff_code_block->getCode();
    }
    return $entire_diff;
  }

  /**
   * Returns an array with all the diff properties that haven't been parsed properly
   *
   * @return array
   */
  public function returnEmptyDiffParsingProperties()
  {
    $empty_diff_parsing_properties = array();
    foreach($this->required_diff_parsing_properties as $property) {
      if($this->$property == '') {
        $empty_diff_parsing_properties[$property] = $property;
      }
    }
    foreach($this->diff_code_blocks as $diff_code_block) {
      $empty_diff_parsing_properties =
        array_merge(
          $empty_diff_parsing_properties,
          $diff_code_block->returnEmptyDiffParsingProperties()
        );
    }

    return $empty_diff_parsing_properties;
  }

  /**
   * If the diff file has a parent source
   *
   * @return boolean
   */
  public function hasParent()
  {
    return $this->source != self::DEV_NULL;
  }

  /**
   * Merges the diff changes and the original file and
   * puts the result in the diff file property.
   * It saves the original file and the diff file in
   * order to perform the merge.
   */
  public function mergeDiffWithOriginal($temp_code_quality_dir_path, $scm)
  {
    $this->temp_diff_file_path = $this->createTempFile($temp_code_quality_dir_path, $this->original_file);
    $temp_diff_file = $this->createTempFile($temp_code_quality_dir_path, $this->diff_file);

    // Different SCM systems use different path structure
    // solutions, so in case of some SCM systems we have to
    // strip a specific amount of the path
    $strip = '';
    switch($scm) {
      case 'git':
        // 'b/path/to/file'  so strip b/ and
        // keep the rest of the path
        $strip = escapeshellarg('-p1');
        break;
      // SVN uses 'path/to/file' so we strip nothing
      // if scm is set to 'svn'
    }
    $patch_command =
      escapeshellarg('patch') .                        ' ' .
      $strip .                                         ' ' .
      // The -b option creates a backup which is used to
      // throw the original file through a specified tool
      escapeshellarg('-b') .                           ' ' .
      escapeshellarg($this->temp_diff_file_path) . ' ' .
      escapeshellarg($temp_diff_file)
    ;

    $process = new Process($patch_command);
    $process->run();
    // Remove the temp file
    unlink($temp_diff_file);
    if(!$process->isSuccessful()) {
      unlink($this->temp_diff_file_path);
      unlink($this->temp_diff_file_path . self::ORIGINAL_EXTENSION);
      if($process->getErrorOutput() != '') {
        throw new RuntimeException($process->getErrorOutput());
      } else {
        throw new RuntimeException($process->getExitCode() . ': ' . $process->getExitCodeText());
      }
    }
  }

  /**
   * Process both the diff file as the original file through the scan process
   *
   * @param Tool $tool
   */
  public function processFile(Tool $tool)
  {
    $this->diff_output = $this->scanCode(
      $tool,
      $this->temp_diff_file_path
    );
    if($this->hasParent()) {
      $this->original_output = $this->scanCode(
        $tool,
        $this->temp_diff_file_path . self::ORIGINAL_EXTENSION
      );
    }
  }

  /**
   * Writes the code into a temp file to be able
   * to scan it with the code quality tool
   *
   * @param Tool $tool
   * @param string $temp_file_path
   * @return string
   */
  private function scanCode(Tool $tool, $temp_file_path)
  {
    // Let the temp file go through the Code Quality Tool scan process by
    // executing the following command line command
    $command =
      $tool->getCallCommand() .                        ' ' .
      escapeshellarg($temp_file_path) .                ' ' .
      escapeshellarg(strtolower($tool->getFormat())) . ' '
    ;
    // Each argument gets added as a single argument to check it separately.
    // Some tools have just one string arg and some have multiple.
    foreach($tool->getArguments() as $argument) {
      $command .= escapeshellarg($argument->getName());
    }
    // Execute the command
    $process = new Process($command);
    $process->run();
    // Validate the returned exit code with the whitelist for the tool
    // as some tools give different error codes for example violations.
    // Remove the temp file
    unlink($temp_file_path);
    if(!in_array($process->getExitCode(), $tool->getWhitelistedExitCodes())) {
      if($process->getErrorOutput() != '') {
        throw new RuntimeException($process->getErrorOutput());
      } else {
        throw new RuntimeException($process->getExitCode() . ': ' . $process->getExitCodeText());
      }
    }


    return $process->getOutput();
  }

  /**
   * Creates a temp file and returns the path to the temp file
   *
   * @param string $temp_code_quality_dir_path
   * @param string $code
   * @return string
   */
  public function createTempFile($temp_code_quality_dir_path, $code)
  {
    $temp_file_path =
      $this->tempnam($temp_code_quality_dir_path, self::TEMP_DIFF_FILE_PREFIX);

    // Opens a file stream reader with write permissions and
    // writes the code into the temp file
    file_put_contents($temp_file_path, $code);

    return $temp_file_path;
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
