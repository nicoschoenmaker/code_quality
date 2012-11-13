<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser\DiffParser;

use Hostnet\HostnetCodeQualityBundle\Parser\AbstractParser;

use InvalidArgumentException;

abstract class AbstractDiffParser extends AbstractParser
{
  const T_SPACE_LENGTH = 1;
  const T_DOT = '.';
  const T_FORWARD_SLASH = '/';
  const T_OPEN_PARENTHESIS = '(';
  const T_CLOSE_PARENTHESIS = ')';
  const T_PLUS = '+';
  const T_MINUS = '-';
  const FILE_RANGE_PATTERN = '/@@ -[0-9]*,[0-9]* \+[0-9]*,[0-9]* @@/';
  const SOURCE_START = '--- ';
  const DESTINATION_START = '+++ ';
  const FILE_RANGE_BRACKETS = '@@';
  const FILE_TYPE_PART_LENGTH = 1;
  const FILE_TYPE_PATTERN = '/^(a|b)\//';

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
    return (bool)preg_match(self::FILE_TYPE_PATTERN, $file_path);
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
