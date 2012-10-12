<?php

namespace Hostnet\HostnetCodeQualityBundle\Tests\Mock;

use Hostnet\HostnetCodeQualityBundle\Entity\CodeLanguage,
    Hostnet\HostnetCodeQualityBundle\Entity\File,
    Hostnet\HostnetCodeQualityBundle\Entity\Rule,
    Hostnet\HostnetCodeQualityBundle\Entity\Violation,
    Hostnet\HostnetCodeQualityBundle\Parser\EntityProviderInterface;

use Hostnet\HostnetCodeQualityBundle\Lib\EntityFactory;

class MockEntityFactory extends EntityFactory implements EntityProviderInterface
{
  public function __construct()
  {
  }

  public function retrieveCodeLanguages()
  {
    return null;
  }

  public function retrieveRules()
  {
    return null;
  }

  public function retrieveFile(CodeLanguage $code_language, $name)
  {
    return new File($code_language, $name);
  }

  public function retrieveViolation(Rule $rule, $message, $begin_line, $end_line)
  {
    return new Violation($rule, $message, $begin_line, $end_line);
  }
}
