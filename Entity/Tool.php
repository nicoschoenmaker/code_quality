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

/**
 * @ORM\Table(name="tool")
 * @ORM\Entity
 */
class Tool
{
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
   * @var Collection
   *
   * @ORM\ManyToMany(targetEntity="Argument", cascade={"persist"})
   * @ORM\JoinTable(name="tool_argument",
   *   joinColumns={@ORM\JoinColumn(name="tool_id", referencedColumnName="id")},
   *   inverseJoinColumns={@ORM\JoinColumn(name="argument_id", referencedColumnName="id")}
   * )
   */
  private $arguments;

  /**
   * @var Collection
   *
   * @ORM\OneToMany(targetEntity="Rule", mappedBy="id")
   */
  private $rules;

  /**
   * The languages that the tool supports to scan
   *
   * @var Collection
   *
   * @ORM\ManyToMany(targetEntity="CodeLanguage", cascade={"persist"})
   * @ORM\JoinTable(name="tool_code_language",
   *   joinColumns={@ORM\JoinColumn(name="tool_id", referencedColumnName="id")},
   *   inverseJoinColumns={@ORM\JoinColumn(name="code_language_id", referencedColumnName="id")}
   * )
   */
  private $supported_languages;

  /**
   * The exit codes that should be
   * whitelisted for the tool
   *
   * @var string
   *
   * @ORM\Column(name="whitelisted_exit_codes", type="string", length=255)
   */
  private $whitelisted_exit_codes;


  public function __construct($name, $path_to_tool, $call_command, $format)
  {
    $this->name = $name;
    $this->path_to_tool = $path_to_tool;
    $this->call_command = $call_command;
    $this->format = $format;
    $this->arguments = new \Doctrine\Common\Collections\ArrayCollection();
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
   * Get an array of Argument objects
   *
   * @return Collection
   */
  public function getArguments()
  {
    return $this->arguments;
  }

  /**
   * Get an array of Rule objects
   *
   * @return Collection
   */
  public function getRules()
  {
    return $this->rules;
  }

  /**
   * Get an array of CodeLanguage objects
   *
   * @return Collection
   */
  public function getSupportedLanguages()
  {
    return $this->supported_languages;
  }

  /**
   * Gets the whitelisted exit codes
   *
   * @return string
   */
  public function getWhitelistedExitCodes()
  {
    return explode(', ', $this->whitelisted_exit_codes);
  }

  /**
   * Sets the whitelisted exit codes
   *
   * @param string $whitelisted_exit_codes
   */
  public function setWhitelistedExitCodes($whitelisted_exit_codes)
  {
    $this->whitelisted_exit_codes = $whitelisted_exit_codes;
  }

  /**
  * Checks if the Tool supports the given extension.
  *
  * @param string $extension
  * @return boolean
  */
  public function supports($extension)
  {
    foreach($this->getSupportedLanguages() as $code_language) {
      if($extension == $code_language->getName()) {
        return true;
      }
    }

    return false;
  }
}
