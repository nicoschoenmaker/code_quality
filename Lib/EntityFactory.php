<?php

namespace Hostnet\HostnetCodeQualityBundle\Lib;

use Hostnet\HostnetCodeQualityBundle\Entity\File,
    Hostnet\HostnetCodeQualityBundle\Entity\Rule,
    Hostnet\HostnetCodeQualityBundle\Entity\CodeLanguage,
    Hostnet\HostnetCodeQualityBundle\Entity\Violation,
    Hostnet\HostnetCodeQualityBundle\Parser\EntityProviderInterface;

use Doctrine\ORM\EntityManager;

class EntityFactory implements EntityProviderInterface
{
  private $em;
  private $code_languages = array();
  private $rules = array();

  public function __construct(EntityManager $em)
  {
    $this->em = $em;

    $this->retrieveCodeLanguages();
    $this->retrieveRules();
  }

  /**
   * Retrieves all the CodeLanguage objects from the DB
   * and assigns the index for easier access.
   */
  public function retrieveCodeLanguages()
  {
    $code_languages = $this->em
      ->getRepository('HostnetCodeQualityBundle:CodeLanguage')
      ->findAll()
    ;

    // Set the code language array index on the name as
    // this will make other functionality easier to use
    foreach($code_languages as $code_language) {
      $this->code_languages[$code_language->getName()] = $code_language;
    }
  }

  /**
   * Retrieves all the Rule objects from the DB
   * and assigns the index for easier access.
   */
  public function retrieveRules()
  {
    $rules = $this->em
      ->getRepository('HostnetCodeQualityBundle:Rule')
      ->findAll()
    ;

    // Set the rule array index on the name as
    // this will make other functionality easier to use
    foreach($rules as $rule) {
      $this->rules[$rule->getName()] = $rule;
    }
  }

  /**
   * Retrieves the File object from the DB
   * based on the name.
   * If it can't find the file it will create it.
   *
   * @param string $name
   * @return File
   */
  public function retrieveFile(CodeLanguage $code_language, $name)
  {
    $file = $this->em
      ->getRepository('HostnetCodeQualityBundle:File')
      ->findByName($name)
    ;

    // If file is null we create it
    if(!$file) {
      $file = new File($code_language, $name);
    }

    return $file;
  }

  /**
   * Retrieves the Violation object from the DB
   * based on the name.
   * If it can't find the violation it will create it.
   *
   * @param Rule $rule
   * @param string $message
   * @param integer $begin_line
   * @param integer $end_line
   * @return Violation
   */
  public function retrieveViolation(Rule $rule, $message, $begin_line, $end_line)
  {
    $violation = $this->em
      ->getRepository('HostnetCodeQualityBundle:Violation')
      ->findByMessage($message)
    ;

    // If file is null we create it
    if(!$violation) {
      $violation = new Violation($rule, $message, $begin_line, $end_line);
    }

    return $violation;
  }

  /**
   * Checks if the code language already exists and gets it,
   * otherwise it creates a new CodeLanguage.
   *
   * @param string $code_language_name
   * @return CodeLanguage
   */
  public function getCodeLanguage($name)
  {
    if(!isset($this->code_languages[$name])) {
      $this->code_languages[$name] = new CodeLanguage($name);
    }
    return $this->code_languages[$name];
  }

  /**
   * Checks if the rule already exists and gets it,
   * otherwise it creates a new Rule.
   *
   * @param string $rule_name
   * @param integer $priority
   * @return Rule
   */
  public function getRule($name, $priority)
  {
    if(!isset($this->rules[$name])) {
      $this->rules[$name] = new Rule($name, $priority);
    }
    return $this->rules[$name];
  }
}
