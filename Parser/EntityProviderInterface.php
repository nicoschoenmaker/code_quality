<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser;

use Hostnet\HostnetCodeQualityBundle\Entity\Rule,
    Hostnet\HostnetCodeQualityBundle\Entity\CodeLanguage;

interface EntityProviderInterface
{
  /**
   * Retrieves the File object from the DB
   * based on the name.
   * If it can't find the file it will create it.
   *
   * @param string $name
   * @return File
   */
  public function retrieveFile(CodeLanguage $code_language, $name);

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
  public function retrieveViolation(Rule $rule, $message, $begin_line, $end_line);

  /**
   * Checks if the code language already exists and gets it,
   * otherwise it creates a new CodeLanguage.
   *
   * @param string $code_language_name
   * @return CodeLanguage
   */
  public function getCodeLanguage($name);

  /**
   * Checks if the rule already exists and gets it,
   * otherwise it creates a new Rule.
   *
   * @param string $rule_name
   * @param integer $priority
   * @return Rule
   */
  public function getRule($name, $priority);
}
