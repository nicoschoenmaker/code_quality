<?php

/*             .---. .---.
              :     : o   :    me want quality code!
          _..-:   o :     :-.._    /
      .-''  '  `---' `---' "   ``-.
    .'   "   '  "  .    "  . '  "  `.
    :   '.---.,,.,...,.,.,.,..---.  ' ;
    `. " `.                     .' " .'
     `.  '`.                   .' ' .'
       `.    `-._           _.-' "  .'  .----.
         `. "    '"--...--"'  . ' .'  .'  o   `.
         .'`-._'    " .     " _.-'`. :       o  :
       .'      ```--.....--'''    ' `:_ o       :
     .'    "     '         "     "   ; `.;";";";'
    ;         '       "       '     . ; .' ; ; ;
   ;     '         '       '   "    .'      .-'
   '  "     "   '      "           "     _*/

namespace Hostnet\HostnetCodeQualityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Hostnet\HostnetCodeQualityBundle\Entity\SettingsManager,
    Hostnet\HostnetCodeQualityBundle\lib\CodeFile;

/**
 * @ORM\Table
 * @ORM\Entity
 */
class CodeQualityTool
{
  private $temp_code_quality_dir_path = '';
  CONST TEMP_CQ_DIR_NAME = '/codequality';
  CONST TEMP_CODE_FILE_PREFIX = 'cq';

  /**
   * @var integer $id
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @var string $name
   *
   * @ORM\Column(name="name", type="string", length=30)
   */
  private $name;

  /**
   * @var string $path_to_tool
   *
   * @ORM\Column(name="path_to_tool", type="string", length=255)
   */
  private $path_to_tool;

  /**
   * @var string $call_command
   *
   * @ORM\Column(name="call_command", type="string", length=255)
   */
  private $call_command;

  /**
   * @var string $format
   *
   * @ORM\Column(name="format", type="string", length=20)
   */
  private $format;

  /**
   * @var string $rulesets
   *
   * @ORM\Column(name="rulesets", type="string", length=50)
   */
  private $rulesets;


  /**
   * Get id
   *
   * @return integer
   */
  public function getId()
  {
      return $this->id;
  }

  /**
   * Set name
   *
   * @param string $name
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
   * Set path_to_tool
   *
   * @param string $path_to_tool
   */
  public function setPathToTool($path_to_tool)
  {
      $this->path_to_tool = $path_to_tool;
  }

  /**
   * Get path_to_tool
   *
   * @return string
   */
  public function getPathToTool()
  {
      return $this->path_to_tool;
  }

  /**
   * Set call command
   *
   * @param string call_command
   */
  public function setCallCommand($call_command)
  {
      $this->call_command = $call_command;
  }

  /**
   * Get call command
   *
   * @return string
   */
  public function getCallCommand()
  {
      return $this->call_command;
  }

  /**
   * Set format
   *
   * @param string $format
   */
  public function setFormat($format)
  {
      $this->format = $format;
  }

  /**
   * Get format
   *
   * @return string
   */
  public function getFormat()
  {
      return $this->format;
  }

  /**
   * Set rulesets
   *
   * @param string $rulesets
   */
  public function setRulesets($rulesets)
  {
    $this->rulesets = $rulesets;
  }

  /**
   * Get rulesets
   *
   * @return string
   */
  public function getRulesets()
  {
    return $this->rulesets;
  }

  /**
   * @param String $temp_dir_name
   */
  public function __construct($temp_dir_name = self::TEMP_CQ_DIR_NAME)
  {
    $this->createTempDir($temp_dir_name);
  }

  /**
   * Creates the temp code quality directory so temp code files can be inserted
   * for the code quality tool processing of the code.
   *
   * @param String $temp_dir_name
   * @throws Exception
   */
  private function createTempDir($temp_dir_name)
  {
    $this->temp_code_quality_dir_path = sys_get_temp_dir() . $temp_dir_name;
    if(!(is_dir($this->temp_code_quality_dir_path))) {
      if(!is_file($this->temp_code_quality_dir_path)) {
        try {
          mkdir($this->temp_code_quality_dir_path);
        } catch(\Exception $e) {
          throw $e;
        }
      } else {
        throw new \Exception("The Code Quality Temp directory at " . $this->temp_code_quality_dir_path
            . " couldn't be created because a file already exists at the given path.");
      }
    }
    clearstatcache();
  }

  /**
   * Process both the diff file as the original file through the scan process
   *
   * @param CodeFile $diff_code_file
   *
   * @return String
   */
  public function processFile(CodeFile $diff_code_file, $original_file)
  {
    $diff_output = $this->scanCode($diff_code_file->getEntireCode());
    $original_output = $this->scanCode($original_file);

    return array('diff_output' => $diff_output,
      'original_diff_output' => $original_output);
  }

  /**
   * Writes the code into a
   *
   * @param String $code
   * @return String
   */
  private function scanCode($code)
  {
    // Creates the temp file
    $temp_code_file_path =
      tempnam($this->temp_code_quality_dir_path, self::TEMP_CODE_FILE_PREFIX);
    // Changes the permissions of the temp file to 777
    chmod($temp_code_file_path, 0777);
    // Opens a file stream reader with write permissions
    $temp_code_file = fopen($temp_code_file_path, 'w');
    // Write the code into the temp file
    fwrite($temp_code_file, $code);
    fclose($temp_code_file);
    // Let the temp file go through the Code Quality Tool scan process by
    // executing the following command line command
    $code_output = shell_exec(
      $this->getCallCommand() .          ' '
      . $temp_code_file_path .           ' '
      . strtolower($this->getFormat()) . ' '
      . $this->getRulesets());
    // Remove the temp file
    unlink($temp_code_file_path);

    return $code_output;
  }
}