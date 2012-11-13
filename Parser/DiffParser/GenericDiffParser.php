<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser\DiffParser;

use Hostnet\HostnetCodeQualityBundle\Parser\Diff\DiffCodeBlock,
    Hostnet\HostnetCodeQualityBundle\Parser\Diff\DiffFile;

abstract class GenericDiffParser extends AbstractDiffParser
{
  /**
   * Parse the diff header data
   *
   * @param DiffFile $diff_file
   * @param string $header_string
   */
  protected final function parseDiffHead(DiffFile $diff_file, $header_string)
  {
    $diff_file->setSource($this->parseSource($header_string));
    $diff_file->setSourceRevision($this->parseSourceRevision($header_string));
    $diff_file->setDestination($this->parseDestination($header_string));
    // If the diff file is removed we don't
    // use the destination as it is empty
    if(!$diff_file->isRemoved()) {
      $file_location_type_value = $diff_file->getDestination();
    } else {
      $file_location_type_value = $diff_file->getSource();
    }
    $startpos_of_name = strrpos(
      $file_location_type_value,
      self::T_FORWARD_SLASH
    ) + strlen(self::T_FORWARD_SLASH);
    $diff_file->setName($this->parseName($startpos_of_name, $file_location_type_value));
    $diff_file->setExtension($this->parseExtension($startpos_of_name, $file_location_type_value));
  }

  /**
   * Parse the diff body data, the actual modified code
   *
   * @param string $file_string
   * @param string $body_string
   * @return DiffCodeBlock
   * @see \Hostnet\HostnetCodeQualityBundle\Parser\DiffParser\DiffParserInterface::parseDiffBody()
   */
  protected function parseDiffBody($file_string, $body_string)
  {
    // Retrieving the begin and endline of each diff code block
    // as the split functionality to split each file into code
    // blocks removes the begin and endline used as the delimiter
    $startpos_of_code_block = strpos($file_string, $body_string);
    $start_of_delimiter = strrpos(
      substr($file_string, 0 , $startpos_of_code_block),
      self::FILE_RANGE_BRACKETS,
        -(strlen(self::FILE_RANGE_BRACKETS) + self::T_SPACE_LENGTH)
    );
    $begin_and_end_line = substr(
      $file_string,
      $start_of_delimiter,
      $startpos_of_code_block - $start_of_delimiter
    );

    // Extract all the diff code block data and fill the DiffCodeBlock object
    $diff_code_block = new DiffCodeBlock();
    $diff_code_block->setBeginLine($this->parseBeginLine($begin_and_end_line));
    $diff_code_block->setEndLine($this->parseEndLine($begin_and_end_line));
    $diff_code_block->setCode($this->parseCode($body_string));

    return $diff_code_block;
  }

  /**
   * Parses the git diff file source
   *
   * @param string $header_string
   * @param integer $source_start_pos
   * @return string
   */
  abstract protected function parseSource($header_string);

  /**
   * Parses the git diff file source revision
   *
   * @param string $header_string
   * @return string
   */
  abstract protected function parseSourceRevision($header_string);

  /**
   * Parses the git diff file destination
   *
   * @param string $header_string
   * @return string
   */
  abstract protected function parseDestination($header_string);

  /**
   * Parses the git diff file name
   *
   * @param integer $startpos_of_name
   * @param string $file_location_type_value
   * @return string
   */
  abstract protected function parseName($startpos_of_name, $file_location_type_value);

  /**
   * Parses the git diff file extension
   *
   * @param integer $startpos_of_name
   * @param string $file_location_type_value
   * @return string
   */
  abstract protected function parseExtension($startpos_of_name, $file_location_type_value);

  /**
   * Parses the git diff file code block begin line
   *
   * @param string $begin_and_end_line
   * @return string
   */
  abstract protected function parseBeginLine($begin_and_end_line);

  /**
   * Parses the git diff file code block end line
   *
   * @param string $begin_and_end_line
   * @return string
   */
  abstract protected function parseEndLine($begin_and_end_line);

  /**
   * Parses the git diff file code block code
   *
   * @param string $body_string
   * @return string
   */
  abstract protected function parseCode($body_string);
}
