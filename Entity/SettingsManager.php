<?php

namespace Hostnet\HostnetCodeQualityBundle\Entity;

use Doctrine\ORM\Mapping as ORM,
    Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityTool,
    Hostnet\HostnetCodeQualityBundle\Parser\DiffParser\SVNDiffParser,
    Hostnet\HostnetCodeQualityBundle\Parser\DiffParser\GitDiffParser;

/**
 * Hostnet\HostnetCodeQualityBundle\Entity\SettingsManager
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class SettingsManager
{
  private static $instance;
  private $code_quality_tools = array();
  private $raw_file_url_mask;

  /**
   * @var integer $id
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @var String $scm
   *
   * @ORM\Column(name="scm", type="string", length=30)
   */
  private $scm;

  /**
   * Private constructor so nobody else can instance it
   */
  private function __construct(){}

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
   * Returns the SettingsManager singleton class
   *
   * @return SettingsManager
   */
  public static function getInstance()
  {
    if(!self::$instance) {
      self::$instance = new SettingsManager();
    }
    return self::$instance;
  }

  /**
   * Returns the Diff Parser based on the set SCM settings, requires a new SCM implementation class if a new SCM is required
   *
   * @return DiffParser
   */
  public function getDiffParser()
  {
    if($this->scm == null) {
      // TODO Retrieve the SCM settings and perform the diff parse actions based on the configured SCM.
      $this->scm = 'git';
    }
    $diff_parser = null;
    switch($this->scm){
      case('git'):
        $diff_parser = new GitDiffParser();
        break;
      case('svn'):
        $diff_parser = new SVNDiffParser();
        break;
      default:
        // TODO Send message that the manager should fill the SCM in in the Company Profile tab.
        break;
    }

    return $diff_parser;
  }

  /**
   * Returns a list of code quality tools
   *
   * @return CodeQualityTool array
   */
  public function getCodeQualityTools()
  {
    if(!$this->code_quality_tools) {
      // TODO Retrieve the CodeQualityTools from the DB into an array
      $PHPMD = new CodeQualityTool();
      $PHPMD->setName('PMD');
      $PHPMD->setPathToTool('~/projects/code_quality_tools/phpmd');
      $PHPMD->setCallCommand('/usr/local/zend/bin/phpmd');
      $PHPMD->setFormat('xml');
      $PHPMD->setRulesets('codesize,unusedcode,naming');
      $this->code_quality_tools['php'] = $PHPMD;
    }
    return $this->code_quality_tools;
  }

  /**
   * Returns the set SCM raw file url mask string
   *
   * @return String
   */
  public function getRawFileUrlMask()
  {
    if($this->raw_file_url_mask) {
      // TODO Retrieve the SCM raw file url mask from the DB
      $this->raw_file_url_mask = 'http://servername/browse/repo/blob/';
    }
    return $this->raw_file_url_mask;
  }


}
