<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser\ToolOutputParser;

use Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityMetric,
    Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityMetricCodeLanguage,
    Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityMetricRule,
    Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityMetricRuleset,
    Hostnet\HostnetCodeQualityBundle\Entity\CodeFile,
    Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityReview,
    Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityReviewViolation,
    Hostnet\HostnetCodeQualityBundle\Parser\ToolOutputParser\ToolOutputParserInterface;

class PMDXMLParser extends BaseXMLParser implements ToolOutputParserInterface
{
  CONST BEGINLINE = 'beginline';
  CONST ENDLINE = 'endline';
  CONST RULE = 'rule';
  CONST RULESET = 'ruleset';
  CONST PRIORITY = 'priority';
  CONST START_OF_VIOLATION_PATTERN = '/<violation /';
  CONST END_OF_VIOLATION = '</violation>';

  /**
   * Parse the output of a static code quality tool and fill the CodeQualityReview object with the extracted data
   *
   * @param String $tool_output
   * @param CodeFile $code_file
   * @return CodeQualityReview
   */
  public function parseToolOutput($tool_output, CodeFile $code_file)
  {
    $file_content = self::parseToolOutputSimilarities($tool_output);
    $code_quality_review = new CodeQualityReview();
    // TODO Check if file name should be filled in in the DefaultController
    $code_quality_review->setFileName($code_file->getName());
    //$code_quality_review->setCodeQualityReviewUser(getAuthenticatedUser());

    // TODO Use file_content instead of tool_output, smaller parsing haystack
    $violations = preg_split(self::START_OF_VIOLATION_PATTERN, $tool_output);
    //$violations = preg_split(self::START_OF_VIOLATION_PATTERN, $file_content);
    for($i = 1; $i < count($violations); $i++) {
      $violation = $violations[$i];

      $code_quality_metric_rule = new CodeQualitymetricRule();
      $code_quality_metric_rule->setName(
        $this->extractAttributeData($violation, self::RULE));
      $code_quality_metric_rule->setMessage(trim(substr($violation,
        strpos($violation, PHP_EOL),
        strpos($violation, self::END_OF_VIOLATION)-strpos($violation, PHP_EOL))));

      $code_quality_metric_ruleset = new CodeQualityMetricRuleset();
      $code_quality_metric_ruleset->setName(
        $this->extractAttributeData($violation, self::RULESET));
      $code_quality_metric_ruleset->addCodeQualityMetricRule($code_quality_metric_rule);
      // TODO Check if the language should be filled in in the DefaultController
      $code_quality_metric_code_language = new CodeQualityMetricCodeLanguage();
      $code_quality_metric_code_language->setName($code_file->getExtension());

      $code_quality_metric = new CodeQualityMetric();
      $code_quality_metric->setCodeQualityMetricRuleset($code_quality_metric_ruleset);
      $code_quality_metric->setCodeQualityMetricCodeLanguage(
        $code_quality_metric_code_language);

      $code_quality_review_violation = new CodeQualityReviewViolation();
      $code_quality_review_violation->setBeginLine(
        $this->extractAttributeData($violation, self::BEGINLINE));
      $code_quality_review_violation->setEndLine(
        $this->extractAttributeData($violation, self::ENDLINE));
      $code_quality_review_violation->setPriority(
        $this->extractAttributeData($violation, self::PRIORITY));
      $code_quality_review_violation->setCodeQualityMetric($code_quality_metric);

      $code_quality_review->addCodeQualityReviewViolation($code_quality_review_violation);
    }

    return $code_quality_review;
  }
}
