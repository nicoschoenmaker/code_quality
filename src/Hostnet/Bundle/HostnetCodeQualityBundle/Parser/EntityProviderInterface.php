<?php

namespace Hostnet\Bundle\HostnetCodeQualityBundle\Parser;

use Hostnet\Bundle\HostnetCodeQualityBundle\Entity\CodeLanguage,
    Hostnet\Bundle\HostnetCodeQualityBundle\Parser\Diff\DiffFile;

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
   * @param DiffFile $diff_file
   * @return File
   */
  public function retrieveFile(DiffFile $diff_file);

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
