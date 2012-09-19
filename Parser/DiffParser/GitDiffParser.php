<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser\DiffParser;

use Hostnet\HostnetCodeQualityBundle\Parser\DiffParser\DiffParserInterface;
use Hostnet\HostnetCodeQualityBundle\Entity\CodeFile;
use Hostnet\HostnetCodeQualityBundle\Entity\CodeBlock;

class GitDiffParser implements DiffParserInterface
{
  CONST START_OF_FILE_PATTERN = '/diff --git /';
  CONST INDEX = 'index ';
  CONST UNNECESSARY_LOCATION_PART_LENGTH = 2;
  CONST DOUBLE_DOT = '..';

  /**
   * Parse the git diff into an array of CodeFile objects
   *
   * @param String $diff
   * @return CodeFile array:
   */
  public function parseDiff($diff)
  {
    // Split the patch file into seperate files
    $files = preg_split(self::START_OF_FILE_PATTERN, $diff);
    // Parse files into CodeFile objects
    $code_files = array();
    // The 1st record consists of nothing but whitespace so we start at the 2nd record
    for($i = 1; $i < count($files); $i++) {
      $file_string = $files[$i];
      // Split each file into different code blocks based on the file range pattern
      $code_block_strings = preg_split(self::FILE_RANGE_PATTERN, $file_string);
      $header_string = $code_block_strings[0];
      $code_file = new CodeFile;
      // Fill the header data
      //$code_file->setIndex();
      $source_start_pos = strpos($header_string, self::SOURCE_START);
      $code_file->setSource(substr($header_string, $source_start_pos+strlen(self::SOURCE_START)+self::UNNECESSARY_LOCATION_PART_LENGTH,
          strpos($header_string, PHP_EOL, $source_start_pos)-self::SPACE_LENGTH-
          ($source_start_pos+strlen($source_start_pos)+self::UNNECESSARY_LOCATION_PART_LENGTH)));
      $index_pos = strrpos($header_string, self::INDEX, -0);
      $index = substr($header_string, $index_pos+strlen(self::INDEX),
          strpos($header_string, self::SOURCE_START)-($index_pos+strlen(self::INDEX)+self::SPACE_LENGTH));
      $code_file->setSourceRevision(substr($index, 0, strpos($index, self::DOUBLE_DOT)));
      $destination_start_pos = strpos($header_string, self::DESTINATION_START);
      $destination = substr($header_string, $destination_start_pos+
          strlen(self::DESTINATION_START)+self::UNNECESSARY_LOCATION_PART_LENGTH,
          strpos($header_string, PHP_EOL, $destination_start_pos)-self::SPACE_LENGTH-
          ($destination_start_pos+strlen($destination_start_pos)+self::UNNECESSARY_LOCATION_PART_LENGTH));
      $code_file->setDestination($destination);
      $startpos_of_name = strrpos($destination, self::FORWARD_SLASH)+strlen(self::FORWARD_SLASH);
      $code_file->setName(substr($destination, $startpos_of_name,
          strrpos($destination, self::DOT)-$startpos_of_name));
      $code_file->setExtension(substr($destination, strrpos($destination, self::DOT)+strlen(self::DOT)));
      $code_file->setDestinationRevision(substr($index, strpos($index, self::DOUBLE_DOT)+strlen(self::DOUBLE_DOT),
          strrpos($index, " ")-(strpos($index, self::DOUBLE_DOT)+strlen(self::DOUBLE_DOT))));

      // Parse code blocks into CodeBlock objects
      $code_blocks = array();
      // Same as the for-loop above, the 1st record consists of nothing but whitespace so we start at the 2nd record
      for($j = 1 ; $j < count($code_block_strings) ; $j++) {
        $code_block_string = $code_block_strings[$j];
        // Retrieving the begin and endline of each code block as the split functionality to split each file into code blocks removes the
        // begin and endline used as the delimiter
        $startpos_of_code_block = strpos($file_string, $code_block_string);
        $start_of_delimiter = strrpos(substr($file_string, 0 , $startpos_of_code_block),
            self::FILE_RANGE_BRACKETS, -(strlen(self::FILE_RANGE_BRACKETS)+self::SPACE_LENGTH));
        $begin_and_end_line = substr($file_string, $start_of_delimiter, $startpos_of_code_block-$start_of_delimiter);
        $code_block = new CodeBlock;
        //  Extract all the code block data and fill the CodeBlock object
        $code_block->setBeginLine(substr($begin_and_end_line, strpos($begin_and_end_line, self::MINUS)+strlen(self::MINUS),
            strpos($begin_and_end_line, self::PLUS)-(strlen(self::PLUS)+self::SPACE_LENGTH)-strpos($begin_and_end_line, self::MINUS)));
        $code_block->setEndLine(substr($begin_and_end_line, strpos($begin_and_end_line, self::PLUS)+strlen(self::PLUS),
            strrpos($begin_and_end_line, self::FILE_RANGE_BRACKETS)-(strlen(self::FILE_RANGE_BRACKETS)+self::SPACE_LENGTH)-
            strpos($begin_and_end_line, self::PLUS)+strlen(self::PLUS)));
        $code_block->setCode(substr($code_block_string, strpos($code_block_string, self::FILE_RANGE_BRACKETS)+
            strlen(self::FILE_RANGE_BRACKETS)+self::SPACE_LENGTH));
        array_push($code_blocks, $code_block);
      }
      $code_file->setCodeBlocks($code_blocks);
      array_push($code_files, $code_file);
    }

    return $code_files;
  }
}
