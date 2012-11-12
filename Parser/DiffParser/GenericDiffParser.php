<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser\DiffParser;

abstract class GenericDiffParser extends AbstractDiffParser
{
  /**
   * Parses the git diff file source
   *
   * @param string $header_string
   * @param integer $source_start_pos
   * @return string
   */
  protected function parseSource($header_string, $source_start_pos);

  /**
   * Parses the git diff file source revision
   *
   * @param string $header_string
   * @return string
   */
  protected function parseSourceRevision($header_string);

  /**
   * Parses the git diff file destination
   *
   * @param string $header_string
   * @return string
   */
  protected function parseDestination($header_string);

  /**
   * Parses the git diff file name
   *
   * @param integer $startpos_of_name
   * @param string $file_location_type_value
   * @return string
   */
  protected function parseName($startpos_of_name, $file_location_type_value);

  /**
   * Parses the git diff file extension
   *
   * @param integer $startpos_of_name
   * @param string $file_location_type_value
   * @return string
   */
  protected function parseExtension($startpos_of_name, $file_location_type_value);

  /**
   * Parses the git diff file code block begin line
   *
   * @param string $begin_and_end_line
   * @return string
   */
  protected function parseBeginLine($begin_and_end_line);

  /**
   * Parses the git diff file code block end line
   *
   * @param string $begin_and_end_line
   * @return string
   */
  protected function parseEndLine($begin_and_end_line);

  /**
   * Parses the git diff file code block code
   *
   * @param string $body_string
   * @return string
   */
  protected function parseCode($body_string);
}
