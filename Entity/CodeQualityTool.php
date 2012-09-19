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

use Hostnet\HostnetCodeQualityBundle\Entity\SettingsManager;

/**
 * Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityTool
 *
 * @ORM\Table()
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
   * @return CodeQualityTool
   */
  public function setName($name)
  {
      $this->name = $name;

      return $this;
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
   * @param string $pathToTool
   * @return CodeQualityTool
   */
  public function setPathToTool($pathToTool)
  {
      $this->path_to_tool = $pathToTool;

      return $this;
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
   * @return CodeQualityTool
   */
  public function setCallCommand($call_command)
  {
      $this->call_command = $call_command;

      return $this;
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
   * @return CodeQualityTool
   */
  public function setFormat($format)
  {
      $this->format = $format;

      return $this;
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

  public function __construct()
  {
    $this->temp_code_quality_dir_path = sys_get_temp_dir() . self::TEMP_CQ_DIR_NAME;
    if(!(is_dir($this->temp_code_quality_dir_path))) {
      mkdir($this->temp_code_quality_dir_path);
    }
    clearstatcache();
  }

  /**
   *
   * @param CodeFile $diff_code_file
   *
   * @return string
   */
  public function processFile(CodeFile $diff_code_file)
  {
    // Retrieve the original code file based on the repository raw file url mask and the new diff file name + parent revision number
    $original_code_file = file_get_contents('http://cgit.hostnetbv.nl/cgit/aurora/www.git/plain/apps/aurora/modules/uml/actions/actions.class.php');
    //$original_code_file = file_get_contents(SettingsManager::getInstance()->getRawFileUrlMask() . $diff_code_file->getSource() .
    //    '?id2=' . $diff_code_file->getSourceRevision());

    $diff_output = $this->scanCode($diff_code_file->getEntireCode());
    $original_output = $this->scanCode($original_code_file);

    return array($diff_output, $original_output);
  }

  private function scanCode($code)
  {
    $temp_code_file_path = tempnam($this->temp_code_quality_dir_path, self::TEMP_CODE_FILE_PREFIX);
    chmod($temp_code_file_path, 0777);
    $temp_code_file = fopen($temp_code_file_path, 'w');
    fwrite($temp_code_file, $code);
    fclose($temp_code_file);
    $code_output = shell_exec($this->getCallCommand() . ' ' . $temp_code_file_path . ' ' .
        $this->getFormat() . ' ' . $this->getRulesets());
    unlink($temp_code_file_path);

    return $code_output;
  }
}
