<?php

namespace Hostnet\HostnetCodeQualityBundle\Lib;

use Hostnet\HostnetCodeQualityBundle\Entity\File,
    Hostnet\HostnetCodeQualityBundle\Entity\Rule,
    Hostnet\HostnetCodeQualityBundle\Entity\CodeLanguage,
    Hostnet\HostnetCodeQualityBundle\Entity\Violation;

use Doctrine\ORM\EntityManager;

class EntityFactory
{
  private $em;
  private $code_languages = array();
  private $files = array();
  private $rules = array();
  private $violations = array();

  public function __construct(EntityManager $em)
  {
    $this->em = $em;
  }

  /**
   * Calls all the retrieve methods in order to retrieve
   * all the entities, call this before each tool output parsing
   */
  public function retrieveEntities()
  {
    $this->retrieveCodeLanguages();
    $this->retrieveFiles();
    $this->retrieveRules();
    $this->retrieveViolations();
  }

  /**
   * Retrieves all the CodeLanguage objects from the DB
   */
  private function retrieveCodeLanguages()
  {
    $this->code_languages = $this->em
      ->getRepository('HostnetCodeQualityBundle:CodeLanguage')
      ->findAll()
    ;
  }

  /**
   * Retrieves all the File objects from the DB
   */
  private function retrieveFiles()
  {
    $this->files = $this->em
      ->getRepository('HostnetCodeQualityBundle:File')
      ->findAll()
    ;
  }

  /**
   * Retrieves all the Rule objects from the DB
   */
  private function retrieveRules()
  {
    $this->rules = $this->em
      ->getRepository('HostnetCodeQualityBundle:Rule')
      ->findAll()
    ;
  }

  /**
   * Retrieves all the Violation objects from the DB
   */
  private function retrieveViolations()
  {
    $this->violations = $this->em
      ->getRepository('HostnetCodeQualityBundle:Violation')
      ->findAll()
    ;
  }

  /**
   * Checks if the code language already exists and retrieves it,
   * otherwise it creates a new CodeLanguage
   *
   * @param string $code_language_name
   * @return CodeLanguage
   */
  public function getCodeLanguage($code_language_name)
  {
    $code_language_to_return = null;
    foreach($this->code_languages as $code_language) {
      if($code_language->hasPropertyValue($code_language_name)) {
        $code_language_to_return = $code_language;
        break;
      }
    }
    if(!$code_language_to_return) {
      $code_language_to_return = new CodeLanguage($code_language_name);
    }

    return $code_language_to_return;
  }

  /**
   * Checks if the file already exists and retrieves it,
   * otherwise it creates a new File
   *
   * @param string $file_name
   * @return File
   */
  public function getFile($file_name)
  {
    $file_to_return = null;
    foreach($this->files as $file) {
      if($file->hasPropertyValue($file_name)) {
        $file_to_return = $file;
        break;
      }
    }
    if(!$file_to_return) {
      $file_to_return = new File($file_name);
    }

    return $file_to_return;
  }

  /**
   * Checks if the rule already exists and retrieves it,
   * otherwise it creates a new Rule
   *
   * @param string $rule_name
   * @param integer $priority
   * @return Rule
   */
  public function getRule($rule_name, $priority)
  {
    $rule_to_return = null;
    foreach($this->rules as $rule) {
      if($rule->hasPropertyValue($rule_name)) {
        $rule_to_return = $rule;
        break;
      }
    }
    if(!$rule_to_return) {
      $rule_to_return = new Rule($rule_name, $priority);
    }

    return $rule_to_return;
  }

  /**
   * Checks if the violation already exists and retrieves it,
   * otherwise it creates a new Violation
   *
   * @param Rule $rule
   * @param string $violation_message
   * @param integer $begin_line
   * @param integer $end_line
   * @return Violation
   */
  public function getViolation(Rule $rule, $violation_message, $begin_line, $end_line)
  {
    $violation_to_return = null;
    foreach($this->violations as $violation) {
      if($violation->hasPropertyValue($violation_message)) {
        $violation_to_return = $violation;
        break;
      }
    }
    if(!$violation_to_return) {
      $violation_to_return = new Violation($rule, $violation_message, $begin_line, $end_line);
    }

    return $violation_to_return;
  }
}
