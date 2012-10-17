<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser;

use Hostnet\HostnetCodeQualityBundle\Entity\CodeLanguage;

/**
 * The Entity Provider Interface is implemented
 * by the Entity Factory so that it can be
 * injected without having the dependency.
 *
 * @author rprent
 */
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
