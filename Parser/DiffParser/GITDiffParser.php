<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser\DiffParser;

use Hostnet\HostnetCodeQualityBundle\Parser\DiffParser\DiffParserInterface,
    Hostnet\HostnetCodeQualityBundle\Lib\CodeFile,
    Hostnet\HostnetCodeQualityBundle\Lib\CodeBlock,
    Hostnet\HostnetCodeQualityBundle\Parser\DiffParser\AbstractDiffParser;

class GITDiffParser extends AbstractDiffParser implements DiffParserInterface
{
  CONST T_DOUBLE_DOT = '..';
  CONST START_OF_FILE_PATTERN = 'diff --git ';
  CONST INDEX = 'index ';
  CONST UNNECESSARY_LOCATION_PART_LENGTH = 2;

  protected $resource;

  public function __construct()
  {
    $this->resource = 'git';
  }

  public function parseDiff($diff)
  {
    // Split the patch file into seperate files
    $files = explode(self::START_OF_FILE_PATTERN, $diff);
    // Parse files into CodeFile objects
    $code_files = array();
    // The 1st record consists of nothing but whitespace so we start at the 2nd record
    array_shift($files);
    foreach($files as $file_string) {
      // Split each file into different code blocks based on the file range pattern
      $code_block_strings = preg_split(self::FILE_RANGE_PATTERN, $file_string);
      $header_string = $code_block_strings[0];
      // Parse the header data
      $code_file = $this->parseDiffHead($header_string);

      // Parse code blocks into CodeBlock objects
      $code_blocks = array();
      // Same as the for-loop above, the 1st record consists of
      // nothing but whitespace so we start at the 2nd record
      array_shift($code_block_strings);
      foreach($code_block_strings as $code_block_string) {
        // Parse the body data, the actual modified code
        $code_block = $this->parseDiffBody($file_string, $code_block_string);
        $code_blocks[] = $code_block;
      }
      $code_file->setCodeBlocks($code_blocks);
      $code_files[] = $code_file;
    }

    return $code_files;
  }


  public function parseDiffHead($header_string)
  {
    $code_file = new CodeFile();
    // Fill the header data
    $source_start_pos = strpos($header_string, self::SOURCE_START);
    $code_file->setSource(
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
    $index_pos = strrpos($header_string, self::INDEX, -0);
    $index = substr(
      $header_string,
      $index_pos+strlen(self::INDEX),
      strpos(
        $header_string,
        self::SOURCE_START
      ) - ($index_pos + strlen(self::INDEX)
        + self::T_SPACE_LENGTH)
    );
    $code_file->setSourceRevision(
      substr(
        $index,
        0,
        strpos($index, self::T_DOUBLE_DOT)
      )
    );
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
    $startpos_of_name = strrpos(
      $destination,
      self::T_FORWARD_SLASH
    ) + strlen(self::T_FORWARD_SLASH);
    $code_file->setName(
      substr($destination, $startpos_of_name,
      strrpos($destination, self::T_DOT)
        - $startpos_of_name)
    );
    $code_file->setExtension(
      substr(
        $destination,
        strrpos($destination, self::T_DOT)
          + strlen(self::T_DOT)
      )
    );

    return $code_file;
  }

  public function parseDiffBody($file_string, $body_string)
  {
    // Retrieving the begin and endline of each code block
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

    // Extract all the code block data and fill the CodeBlock object
    $code_block = new CodeBlock;
    $code_block->setBeginLine(
      substr(
        $begin_and_end_line,
        strpos($begin_and_end_line, self::T_MINUS) + strlen(self::T_MINUS),
        strpos($begin_and_end_line, self::T_PLUS)
        - (strlen(self::T_PLUS)+self::T_SPACE_LENGTH)
        - strpos($begin_and_end_line, self::T_MINUS)
      )
    );
    $code_block->setEndLine(
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
    $code_block->setCode(
      substr(
        $body_string,
        strpos(
          $body_string,
          self::FILE_RANGE_BRACKETS
        ) + self::T_SPACE_LENGTH
      )
    );

    return $code_block;
  }
}
