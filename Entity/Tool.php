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

use Symfony\Component\Process\Process;

use Hostnet\HostnetCodeQualityBundle\Lib\CodeFile;

/**
 * @ORM\Table(name="tool")
 * @ORM\Entity
 */
class Tool
{
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
   * @ORM\Column(name="name", type="string", length=50)
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
   * @ORM\Column(name="format", type="string", length=30)
   */
  private $format;

  /**
   * @var string
   *
   * @ORM\Column(name="rulesets", type="string", length=255)
   */
  private $rulesets;

  /**
   * @var Rule array
   *
   * @ORM\OneToMany(targetEntity="Rule", mappedBy="id")
   */
  private $rules;

  /**
   * The languages that the tool supports to scan
   *
   * @var CodeLanguage array
   *
   * @ORM\ManyToMany(targetEntity="CodeLanguage", inversedBy="tools")
   * @ORM\JoinTable(name="tool_code_language",
   *   joinColumns={@ORM\JoinColumn(name="tool_id", referencedColumnName="id")},
   *   inverseJoinColumns={@ORM\JoinColumn(name="code_language_id", referencedColumnName="id")}
   * )
   */
  private $supported_languages;


  public function __construct($name, $path_to_tool, $call_command, $format, $rulesets)
  {
    $this->name = $name;
    $this->path_to_tool = $path_to_tool;
    $this->call_command = $call_command;
    $this->format = $format;
    $this->rulesets = $rulesets;
    $this->rules = new \Doctrine\Common\Collections\ArrayCollection();
    $this->supported_languages = new \Doctrine\Common\Collections\ArrayCollection();
  }

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
   * Get an array of Rule objects
   *
   * @return Rule array
   */
  public function getRules()
  {
    return $this->rules;
  }

  /**
   * Get an array of CodeLanguage objects
   *
   * @return CodeLanguage array
   */
  public function getSupportedLanguages()
  {
    return $this->supported_languages;
  }

  /**
   * Process both the diff file as the original file through the scan process
   *
   * @param CodeFile $diff_code_file
   * @return array
   */
  public function processFile(CodeFile $diff_code_file,
    $original_file, $temp_code_quality_dir_path)
  {
    $diff_output = $this->scanCode(
      $diff_code_file->getEntireCode(),
      $temp_code_quality_dir_path
    );
    $original_output = $this->scanCode(
      $original_file,
      $temp_code_quality_dir_path
    );

    return array('diff_output' => $diff_output,
      'original_diff_output' => $original_output);
  }

  /**
   * Writes the code into a temp file to be able
   * to scan it with the code quality tool
   *
   * @param String $code
   * @return String $code_output
   */
  private function scanCode($code, $temp_code_quality_dir_path)
  {
    // Creates the temp file
    $temp_code_file_path =
    tempnam($temp_code_quality_dir_path, self::TEMP_CODE_FILE_PREFIX);
    // Changes the permissions of the temp file to 777
    chmod($temp_code_file_path, 0777);
    // Opens a file stream reader with write permissions and
    // writes the code into the temp file
    file_put_contents($temp_code_file_path, $code);
    // Let the temp file go through the Code Quality Tool scan process by
    // executing the following command line command
    $command_line_string =
    $this->getCallCommand() .          ' '
      . $temp_code_file_path .           ' '
      . strtolower($this->getFormat()) . ' '
      . $this->getRulesets()
    ;
    $process = new Process(escapeshellcmd($command_line_string));
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
  * Checks if the CodeQualityTool supports the given CodeFile.
  *
  * @param CodeFile $code_file
  * @return boolean
  */
  public function supports(CodeFile $code_file)
  {
    $result = false;
    foreach($this->getSupportedLanguages() as $code_language) {
      if($code_file->getExtension() == $code_language) {
        $result = true;
      }
    }

    return $result;
  }
}
