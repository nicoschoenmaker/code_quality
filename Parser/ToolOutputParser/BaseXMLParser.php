<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser\ToolOutputParser;

use Hostnet\HostnetCodeQualityBundle\Parser\BaseParser,
    Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityReview;

class BaseXMLParser extends BaseParser
{
  CONST START_FILE_TAG = '<file ';
  CONST END_FILE_TAG = '</file>';
  CONST ATTRIBUTE_DECLARATION = '="';
  CONST DOUBLE_QUOTES = '"';

  /**
   * Parse the parts of xml output that so far all the tool outputs have in common
   *
   * @param String $tool_output
   * @return string
   */
  protected function parseToolOutputSimilarities($tool_output)
  {
    $pos_of_file_tag = strpos($tool_output, self::START_FILE_TAG);
    // Return the part of the file that we require, the body
    return substr($tool_output,
        $pos_of_file_tag + strpos($tool_output, PHP_EOL, $pos_of_file_tag),
        strpos($tool_output, self::END_FILE_TAG) - $pos_of_file_tag);
  }

  /**
   * Extract the property of a violation by parsing on the attribute name
   *
   * @param String $violation
   * @param String $attribute
   * @return string
   */
  protected function extractAttributeData($violation, $attribute)
  {
    $attribute .= self::ATTRIBUTE_DECLARATION;
    $attribute_pos = strpos($violation, $attribute) + strlen($attribute);
    return substr($violation, $attribute_pos,
      strpos($violation, self::DOUBLE_QUOTES, $attribute_pos) - $attribute_pos);
  }
}
