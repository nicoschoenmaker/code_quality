<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser\ToolOutputParser;

use Hostnet\HostnetCodeQualityBundle\Parser\ToolOutputParser\ToolOutputParserInterface,
    Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityMetric,
    Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityMetricCodeLanguage,
    Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityMetricRule,
    Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityMetricRuleset,
    Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityReview,
    Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityReviewViolation,
    Hostnet\HostnetCodeQualityBundle\lib\CodeFile;

class PMDXMLParser implements ToolOutputParserInterface
{
  CONST BEGINLINE = 'beginline';
  CONST ENDLINE = 'endline';
  CONST RULE = 'rule';
  CONST RULESET = 'ruleset';
  CONST PRIORITY = 'priority';
  CONST VIOLATION = 'violation';

  public function parseToolOutput($tool_output, CodeFile $code_file)
  {
    $code_quality_review = new CodeQualityReview();
    $code_quality_review->setFileName($code_file->getName());
    // Retrieve the violations array in advance as
    // it's only required to add all the violations
    $violations_array = $code_quality_review->getCodeQualityReviewViolations();

    $xml = new \DomDocument();
    // Load the tool output string in the xml format as xml
    $xml->loadXML($tool_output);
    // Extract all the violation nodes out of the tool output
    $violations = $xml->getElementsByTagName(self::VIOLATION);
    foreach($violations as $violation) {
      $code_quality_metric_rule =
        $this->fillCodeQualityMetricRule($violation);
      $code_quality_metric_ruleset =
        $this->fillCodeQualityMetricRuleset($violation, $code_quality_metric_rule);
      $code_quality_metric_code_language =
        $this->fillCodeQualityMetricCodelanguage($code_file);
      $code_quality_metric =
        $this->fillCodeQualityMetric($code_quality_metric_ruleset, $code_quality_metric_code_language);
      $code_quality_review_violation =
        $this->fillCodeQualityReviewViolation($violation, $code_quality_metric);

      $violations_array->add($code_quality_review_violation);
    }

    return $code_quality_review;
  }

  /**
   * Fills the CodeQualityMetricRule object and returns it
   *
   * @param String $violation
   * @return CodeQualityMetricRule
   */
  private function fillCodeQualityMetricRule($violation)
  {
    $code_quality_metric_rule = new CodeQualitymetricRule();
    $code_quality_metric_rule
      ->setName($violation->getAttribute(self::RULE));
    // Get the child value of the violation which is the feedback message
    $code_quality_metric_rule
      ->setMessage(trim($violation->firstChild->nodeValue));

    return $code_quality_metric_rule;
  }

  /**
   * Fills the CodeQualityMetricRuleset object and returns it
   *
   * @param String $violation
   * @param CodeQualityMetricRule $code_quality_metric_rule
   * @return CodeQualityMetricRuleset
   */
  private function fillCodeQualityMetricRuleset($violation, $code_quality_metric_rule)
  {
    $code_quality_metric_ruleset = new CodeQualityMetricRuleset();
    $code_quality_metric_ruleset
      ->setName($violation->getAttribute(self::RULESET));
    $code_quality_metric_ruleset
      ->getCodeQualityMetricRules()
      ->add($code_quality_metric_rule);

    return $code_quality_metric_ruleset;
  }

  /**
   * Fills the CodeQualityMetricCodeLanguage object and returns it
   *
   * @param String $code_file
   * @return CodeQualityMetricCodeLanguage
   */
  private function fillCodeQualityMetricCodelanguage($code_file)
  {
    $code_quality_metric_code_language = new CodeQualityMetricCodeLanguage();
    $code_quality_metric_code_language
      ->setName($code_file->getExtension());

    return $code_quality_metric_code_language;
  }

  /**
   * Fills the CodeQualityMetric object and returns it
   *
   * @param CodeQualityMetricRuleset $code_quality_metric_ruleset
   * @param CodeQualityMetricCodeLanguage $code_quality_metric_code_language
   * @return CodeQualityMetric
   */
  private function fillCodeQualityMetric($code_quality_metric_ruleset, $code_quality_metric_code_language)
  {
    $code_quality_metric = new CodeQualityMetric();
    $code_quality_metric
      ->setCodeQualityMetricRuleset($code_quality_metric_ruleset);
    $code_quality_metric
      ->setCodeQualityMetricCodeLanguage($code_quality_metric_code_language);

    return $code_quality_metric;
  }

  /**
   * Fills the CodeQualityReviewViolation object and returns it
   *
   * @param String $violation
   * @param CodeQualityMetric $code_quality_metric
   * @return CodeQualityReviewViolation
   */
  private function fillCodeQualityReviewViolation($violation, $code_quality_metric)
  {
    $code_quality_review_violation = new CodeQualityReviewViolation();
    $code_quality_review_violation
      ->setBeginLine($violation->getAttribute(self::BEGINLINE));
    $code_quality_review_violation
      ->setEndLine($violation->getAttribute(self::ENDLINE));
    $code_quality_review_violation
      ->setPriority($violation->getAttribute(self::PRIORITY));
    $code_quality_review_violation
      ->setCodeQualityMetric($code_quality_metric);

    return $code_quality_review_violation;
  }
}
