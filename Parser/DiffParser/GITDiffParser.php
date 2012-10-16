<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser\DiffParser;

use Hostnet\HostnetCodeQualityBundle\Parser\Diff\DiffFile,
    Hostnet\HostnetCodeQualityBundle\Parser\Diff\DiffCodeBlock,
    Hostnet\HostnetCodeQualityBundle\Parser\DiffParser\AbstractDiffParser,
    Hostnet\HostnetCodeQualityBundle\Parser\DiffParser\DiffParserInterface;

class GITDiffParser extends AbstractDiffParser implements DiffParserInterface
{
  CONST T_DOUBLE_DOT = '..';
  CONST START_OF_FILE_PATTERN = 'diff --git ';
  CONST INDEX = 'index ';
  CONST UNNECESSARY_LOCATION_PART_LENGTH = 2;

  public function __construct()
  {
    $this->resource = 'git';
  }

  /**
   * Parse the diff into an array of DiffFile objects
   *
   * @param String $diff
   * @return DiffFile array
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
      $diff_file = $this->parseDiffHead($header_string);

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

    return $diff_files;
  }

  /**
   * Parse the diff header data
   *
   * @param String $header_string
   * @return DiffFile
   * @see \Hostnet\HostnetCodeQualityBundle\Parser\DiffParser\DiffParserInterface::parseDiffHead()
   */
  public function parseDiffHead($header_string)
  {
    $diff_file = new DiffFile();
    // Fill the DiffFile with the header data
    // Fill the DiffFile source property
    $source_start_pos = strpos($header_string, self::SOURCE_START);
    $diff_file->setSource(
      substr(
        $header_string,
        $source_start_pos + strlen(self::SOURCE_START)
          + self::UNNECESSARY_LOCATION_PART_LENGTH,
        strpos(
          $header_string,
          PHP_EOL,
          $source_start_pos
        ) - self::T_SPACE_LENGTH
          - ($source_start_pos+strlen($source_start_pos)
          + self::UNNECESSARY_LOCATION_PART_LENGTH)
      )
    );
    // Fill the DiffFile index property
    $index_pos = strrpos($header_string, self::INDEX);
    $index = substr(
      $header_string,
      $index_pos+strlen(self::INDEX),
      strpos(
        $header_string,
        self::SOURCE_START
      ) - ($index_pos + strlen(self::INDEX)
        + self::T_SPACE_LENGTH)
    );
    // Fill the DiffFile source revision property
    $diff_file->setSourceRevision(
      substr(
        $index,
        0,
        strpos($index, self::T_DOUBLE_DOT)
      )
    );
    // Fill the DiffFile destination property
    $destination_start_pos = strpos($header_string, self::DESTINATION_START);
    $destination = substr(
      $header_string,
      $destination_start_pos
        + strlen(self::DESTINATION_START)
        + self::UNNECESSARY_LOCATION_PART_LENGTH,
      strpos(
        $header_string,
        PHP_EOL,
        $destination_start_pos
      ) - self::T_SPACE_LENGTH
        - ($destination_start_pos
        + strlen($destination_start_pos)
        + self::UNNECESSARY_LOCATION_PART_LENGTH)
    );
    // Fill the DiffFile name property
    $startpos_of_name = strrpos(
      $destination,
      self::T_FORWARD_SLASH
    ) + strlen(self::T_FORWARD_SLASH);
    $diff_file->setName(
      substr($destination, $startpos_of_name,
      strrpos($destination, self::T_DOT)
        - $startpos_of_name)
    );
    // Fill the DiffFile extension property
    $diff_file->setExtension(
      substr(
        $destination,
        strrpos($destination, self::T_DOT)
          + strlen(self::T_DOT)
      )
    );

    return $diff_file;
  }

  /**
   * Parse the diff body data, the actual modified code
   *
   * @param String $file_string
   * @param String $body_string
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
    $diff_code_block->setBeginLine(
      substr(
        $begin_and_end_line,
        strpos($begin_and_end_line, self::T_MINUS) + strlen(self::T_MINUS),
        strpos($begin_and_end_line, self::T_PLUS)
        - (strlen(self::T_PLUS)+self::T_SPACE_LENGTH)
        - strpos($begin_and_end_line, self::T_MINUS)
      )
    );
    $diff_code_block->setEndLine(
      substr(
        $begin_and_end_line,
        strpos($begin_and_end_line, self::T_PLUS) + strlen(self::T_PLUS),
        strrpos($begin_and_end_line, self::FILE_RANGE_BRACKETS)
          - (strlen(self::FILE_RANGE_BRACKETS) + self::T_SPACE_LENGTH)
          - strpos($begin_and_end_line, self::T_PLUS) + strlen(self::T_PLUS)
      )
    );
    // Extract all the code after the '@@' part,
    // which is the code that has been modified
    $diff_code_block->setCode(
      substr(
        $body_string,
        strpos(
          $body_string,
          self::FILE_RANGE_BRACKETS
        ) + self::T_SPACE_LENGTH
      )
    );

    return $diff_code_block;
  }
}
