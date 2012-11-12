<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser\DiffParser;

use Hostnet\HostnetCodeQualityBundle\Parser\AbstractParser;

use InvalidArgumentException;

abstract class AbstractDiffParser extends AbstractParser
{
  CONST T_SPACE_LENGTH = 1;
  CONST T_DOT = '.';
  CONST T_FORWARD_SLASH = '/';
  CONST T_OPEN_PARENTHESIS = '(';
  CONST T_CLOSE_PARENTHESIS = ')';
  CONST T_PLUS = '+';
  CONST T_MINUS = '-';
  CONST FILE_RANGE_PATTERN = '/@@ -[0-9]*,[0-9]* \+[0-9]*,[0-9]* @@/';
  CONST SOURCE_START = '--- ';
  CONST DESTINATION_START = '+++ ';
  CONST FILE_RANGE_BRACKETS = '@@';
  const FILE_TYPE_PART_REGEX_LENGTH = 2;
  CONST FILE_TYPE_PART_LENGTH = 1;
  const SOURCE_FILE_TYPE = 'a/';
  const DESTINATION_FILE_TYPE = 'b/';

  /**
   * Checks if the diff parser supports the configured scm setting
   *
   * @param string $scm
   * @return boolean
   */
  public function supports($scm)
  {
    return (strcasecmp($this->resource, $scm) == 0) ? true : false;
  }

  /**
   * Parses the file type part if it exists
   *
   * @param string $file_path
   * @return string
   */
  protected function parseFileTypePart($file_path)
  {
    if($this->hasFileTypePart($file_path)) {
      $file_path = substr($file_path, self::FILE_TYPE_PART_LENGTH);
    }

    return $file_path;
  }

  /**
   * Checks if the file type part exists
   *
   * @param string $file_path
   * @return boolean
   */
  private function hasFileTypePart($file_path)
  {
    $file_type = substr($file_path, 0, self::FILE_TYPE_PART_REGEX_LENGTH);
    if($file_type == self::SOURCE_FILE_TYPE || $file_type == self::DESTINATION_FILE_TYPE) {
      return true;
    }

    return false;
  }

  /**
   * Check if the whole diff parsed cleanly
   *
   * @param array $diff_files
   * @throws InvalidArgumentException
   */
  protected function checkIfDiffParsedCleanly($diff_files)
  {
    $diff_parsed_uncleanly = false;
    $parsing_exception = "The following diff components didn't get parsed correctly:\n\n";

    foreach($diff_files as $diff_file) {
      $empty_diff_parsing_properties = $diff_file->returnEmptyDiffParsingProperties();
      // If a property didn't get parsed correctly we start the exception throwing process
      if(count($empty_diff_parsing_properties) > 0) {
        $diff_parsed_uncleanly = true;
        $last_empty_diff_parsing_property = array_pop($empty_diff_parsing_properties);
        $empty_diff_parsing_properties = implode(', ', $empty_diff_parsing_properties);
        // Add all the failed parsing components of the diff file
        $and = (count($empty_diff_parsing_properties) > 1) ? ' and ' : ', ';
        $parsing_exception .=
        "\t" . $diff_file->getName() . ":\n"
          . "\t\t" . '"' . $empty_diff_parsing_properties . $and
            . $last_empty_diff_parsing_property . '"' . "\n";
        ;
      }
    }

    if($diff_parsed_uncleanly) {
      throw new InvalidArgumentException($parsing_exception);
    }
  }
}
