<?php

namespace Hostnet\HostnetCodeQualityBundle\Lib;

use Hostnet\HostnetCodeQualityBundle\Entity\File,
    Hostnet\HostnetCodeQualityBundle\Entity\Rule,
    Hostnet\HostnetCodeQualityBundle\Entity\CodeLanguage,
    Hostnet\HostnetCodeQualityBundle\Parser\Diff\DiffFile,
    Hostnet\HostnetCodeQualityBundle\Parser\EntityProviderInterface;

use Doctrine\ORM\EntityManager,
    Doctrine\Common\Collection;

/**
 * The Entity Factory holds the entity arrays and all
 * the access to the database.
 *
 * @author rprent
 */
class EntityFactory implements EntityProviderInterface
{
  /**
   * @var EntityManager
   */
  private $em;

  /**
   * @var Collection
   */
  private $code_languages = array();

  /**
   * @var Collection
   */
  private $rules = array();

  /**
   * Whether the Review data should be saved or not.
   *
   * @var boolean
   */
  private $register = false;


  public function __construct(EntityManager $em)
  {
    $this->em = $em;

    $this->retrieveCodeLanguages();
    $this->retrieveRules();
  }

  /**
   * Sets whether the Review data should be saved or not.
   *
   * @param boolean $register
   */
  public function setRegister($register)
  {
    $this->register = $register;
  }

  public function persist($entity)
  {
    if($this->register) {
      $this->em->persist($entity);
    }
  }

  /**
   * Persists and Flushes the entity if it should be registered.
   * Required for certain entities before filling other entities.
   *
   * @param Object $entity
   */
  public function persistAndFlush($entity)
  {
    if($this->register) {
      $this->em->persist($entity);
      $this->em->flush();
    }
  }

  /**
   * Returns a list of code quality tools
   *
   * @return Collection
   */
  public function retrieveTools()
  {
    return $this->em
      ->getRepository('HostnetCodeQualityBundle:Tool')
      ->findAll()
    ;
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
   * @param DiffFile $diff_file
   * @return File
   */
  public function retrieveFile(DiffFile $diff_file)
  {
    $file = $this->em
      ->getRepository('HostnetCodeQualityBundle:File')
      ->findOneByName($diff_file->getName())
    ;

    // If file is null we create it
    if(!$file) {
      $code_language = $this->getCodeLanguage($diff_file->getExtension());
      $file = $diff_file->createFile($code_language);
      $this->persistAndFlush($file);
    }

    return $file;
  }

  /**
   * Checks if the code language already exists and gets it,
   * otherwise it creates a new CodeLanguage.
   *
   * @param string $name
   * @return CodeLanguage
   */
  private function getCodeLanguage($name)
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
