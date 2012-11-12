<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser\DiffParser;

use Hostnet\HostnetCodeQualityBundle\Parser\Diff\DiffFile,
    Hostnet\HostnetCodeQualityBundle\Parser\Diff\DiffCodeBlock,
    Hostnet\HostnetCodeQualityBundle\Parser\DiffParser\AbstractDiffParser,
    Hostnet\HostnetCodeQualityBundle\Parser\DiffParser\DiffParserInterface;

use InvalidArgumentException;

class GITDiffParser extends AbstractDiffParser implements DiffParserInterface
{
  CONST T_DOUBLE_DOT = '..';
  CONST START_OF_FILE_PATTERN = 'diff --git ';
  CONST INDEX = 'index ';

  public function __construct()
  {
    $this->resource = 'git';
  }

  /**
   * Parse the diff into an array of DiffFile objects
   *
   * @param string $diff
   * @return array
   * @see \Hostnet\HostnetCodeQualityBundle\Parser\DiffParser\DiffParserInterface::parseDiff()
   */
  public function parseDiff($diff)
  {
    // Split the patch file into seperate files
    $files = explode(self::START_OF_FILE_PATTERN, $diff);
    // Parse files into DiffFile objects
    $diff_files = array();
    // The 1st record consists of nothing but whitespace so we start at the 2nd record
    array_shift($files);
    foreach($files as $file_string) {
      // Split each file into different diff code blocks based on the file range pattern
      $diff_code_block_strings = preg_split(self::FILE_RANGE_PATTERN, $file_string);
      $header_string = $diff_code_block_strings[0];
      // Parse the header data
      $diff_file = new DiffFile();
      $diff_file->setDiffFile($file_string);
      $this->parseDiffHead($diff_file, $header_string);

      // Parse diff code blocks into DiffCodeBlock objects
      $diff_code_blocks = array();
      // Same as the for-loop above, the 1st record consists of
      // nothing but whitespace so we start at the 2nd record
      array_shift($diff_code_block_strings);
      foreach($diff_code_block_strings as $diff_code_block_string) {
        // Parse the body data, the actual modified code
        $diff_code_block = $this->parseDiffBody($file_string, $diff_code_block_string);
        $diff_code_blocks[] = $diff_code_block;
      }
      $diff_file->setDiffCodeBlocks($diff_code_blocks);
      $diff_files[] = $diff_file;
    }
    $this->checkIfDiffParsedCleanly($diff_files);

    return $diff_files;
  }

  /**
   * Parse the diff header data
   *
   * @param DiffFile $diff_file
   * @param string $header_string
   * @see \Hostnet\HostnetCodeQualityBundle\Parser\DiffParser\DiffParserInterface::parseDiffHead()
   */
  public function parseDiffHead(DiffFile $diff_file, $header_string)
  {
    $source_start_pos = strpos(
      $header_string,
      self::T_FORWARD_SLASH,
      strpos($header_string, self::SOURCE_START)
    );
    $diff_file->setSource($this->parseSource($header_string, $source_start_pos));
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
  public function parseDiffBody($file_string, $body_string)
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
  protected function parseSource($header_string, $source_start_pos)
  {
    return substr(
      $header_string,
      $source_start_pos,
      strpos(
        $header_string,
        PHP_EOL,
        $source_start_pos
      ) - $source_start_pos
    );
  }

  /**
   * Parses the git diff file source revision
   *
   * @param string $header_string
   * @return string
   */
  protected function parseSourceRevision($header_string)
  {
    $index_pos = strrpos($header_string, self::INDEX);
    $index = substr(
      $header_string,
      $index_pos + strlen(self::INDEX),
      strpos(
        $header_string,
        self::SOURCE_START
      ) - ($index_pos + strlen(self::INDEX)
        + self::T_SPACE_LENGTH)
    );

    return substr(
      $index,
      0,
      strpos($index, self::T_DOUBLE_DOT)
    );
  }

  /**
   * Parses the git diff file destination
   *
   * @param string $header_string
   * @return string
   */
  protected function parseDestination($header_string)
  {
    $destination_start_pos = strpos($header_string, self::DESTINATION_START);
    $destination = substr(
      $header_string,
      $destination_start_pos
        + strlen(self::DESTINATION_START),
      strpos(
        $header_string,
        PHP_EOL,
        $destination_start_pos
      ) - self::T_SPACE_LENGTH
        - ($destination_start_pos
          + strlen($destination_start_pos))
    );

    return $this->parseFileTypePart($destination);
  }

  /**
   * Parses the git diff file name
   *
   * @param integer $startpos_of_name
   * @param string $file_location_type_value
   * @return string
   */
  protected function parseName($startpos_of_name, $file_location_type_value)
  {
    return substr(
      $file_location_type_value, $startpos_of_name,
      strrpos($file_location_type_value, self::T_DOT)
        - $startpos_of_name
    );
  }

  /**
   * Parses the git diff file extension
   *
   * @param integer $startpos_of_name
   * @param string $file_location_type_value
   * @return string
   */
  protected function parseExtension($startpos_of_name, $file_location_type_value)
  {
    return substr(
      $file_location_type_value,
      strrpos($file_location_type_value, self::T_DOT)
        + strlen(self::T_DOT)
    );
  }

  /**
   * Parses the git diff file code block begin line
   *
   * @param string $begin_and_end_line
   * @return string
   */
  protected function parseBeginLine($begin_and_end_line)
  {
    return substr(
      $begin_and_end_line,
      strpos($begin_and_end_line, self::T_MINUS) + strlen(self::T_MINUS),
      strpos($begin_and_end_line, ',')
        - strpos($begin_and_end_line, self::T_MINUS)
        - strlen(self::T_MINUS)
    );
  }

  /**
   * Parses the git diff file code block end line
   *
   * @param string $begin_and_end_line
   * @return string
   */
  protected function parseEndLine($begin_and_end_line)
  {
    return substr(
      $begin_and_end_line,
      strpos($begin_and_end_line, self::T_PLUS) + strlen(self::T_PLUS),
      strrpos($begin_and_end_line, self::FILE_RANGE_BRACKETS)
        - (strlen(self::FILE_RANGE_BRACKETS) + self::T_SPACE_LENGTH)
        - strpos($begin_and_end_line, self::T_PLUS) + strlen(self::T_PLUS)
    );
  }

  /**
   * Parses the git diff file code block code
   *
   * @param string $body_string
   * @return string
   */
  protected function parseCode($body_string)
  {
    return substr(
      $body_string,
      strpos(
        $body_string,
        PHP_EOL
      ) + self::T_SPACE_LENGTH
    );
  }
}
