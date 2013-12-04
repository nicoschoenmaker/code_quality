<?php

namespace Hostnet\CodeQualityBundle\Tests\Mock;

use Hostnet\CodeQualityBundle\Entity\CodeLanguage,
    Hostnet\CodeQualityBundle\Entity\File,
    Hostnet\CodeQualityBundle\Entity\Rule,
    Hostnet\CodeQualityBundle\Entity\Argument,
    Hostnet\CodeQualityBundle\Entity\Tool,
    Hostnet\CodeQualityBundle\Entity\Violation,
    Hostnet\CodeQualityBundle\Lib\EntityFactory,
    Hostnet\CodeQualityBundle\Parser\Diff\DiffFile,
    Hostnet\CodeQualityBundle\Parser\EntityProviderInterface;


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
    $PHPMD->setWhitelistedExitCodes('0, 2');

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

  public function retrieveFile(DiffFile $diff_file)
  {
    $code_language = new CodeLanguage('PHP');
    return $diff_file->createFile($code_language);
  }

  public function retrieveViolation(Rule $rule, $message, $begin_line, $end_line)
  {
    return new Violation($rule, $message, $begin_line, $end_line);
  }
}
