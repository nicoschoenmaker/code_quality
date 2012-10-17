<?php

namespace Hostnet\HostnetCodeQualityBundle\Tests\Mock;

use Hostnet\HostnetCodeQualityBundle\Entity\CodeLanguage,
    Hostnet\HostnetCodeQualityBundle\Entity\File,
    Hostnet\HostnetCodeQualityBundle\Entity\Rule,
    Hostnet\HostnetCodeQualityBundle\Entity\Argument,
    Hostnet\HostnetCodeQualityBundle\Entity\Tool,
    Hostnet\HostnetCodeQualityBundle\Entity\Violation,
    Hostnet\HostnetCodeQualityBundle\Lib\EntityFactory,
    Hostnet\HostnetCodeQualityBundle\Parser\EntityProviderInterface;


class MockEntityFactory extends EntityFactory implements EntityProviderInterface
{
  public function __construct()
  {
  }

  public function retrieveTools()
  {
    $PHPMD = new Tool(
      'pmd',
      '~/projects/code_quality_tools/phpmd',
      '/usr/local/zend/bin/phpmd',
      'xml'
    );
    $PHPMD->getArguments()->add(new Argument('codesize,unusedcode,naming'));
    $PHPMD->getSupportedLanguages()->add(new CodeLanguage('php'));

    return array($PHPMD);
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