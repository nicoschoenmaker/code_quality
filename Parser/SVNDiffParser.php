<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser;

use Hostnet\HostnetCodeQualityBundle\Parser\DiffParserInterface;
use Hostnet\HostnetCodeQualityBundle\Entity\CodeFile;
use Hostnet\HostnetCodeQualityBundle\Entity\CodeBlock;

class SVNDiffParser implements DiffParserInterface
{
  CONST START_OF_FILE_PATTERN = '/Index: /';
  //TODO Check if has to be fixed?
  CONST REVISION = 'revision ';
  //TODO Fix Working Copy, not always (working copy)!!!
  CONST WORKING_COPY = '(working copy)';

  /**
   * Parse the svn diff into an array of CodeFile objects
   *
   * @param String $diff
   * @return CodeFile array:
   */
  public function parseDiff($diff)
  {
    //Split the patch file into seperate files
    $files = preg_split(self::START_OF_FILE_PATTERN, $diff);
    //Parse files into CodeFile objects
    $code_files = array();
    //The 1st record consists of nothing but whitespace so we start at the 2nd record
    for($i = 1 ; $i < count($files) ; $i++) {
      $file_string = $files[$i];
      $code_file = new CodeFile;
      //Split each file into different code blocks based on the file range pattern
      $code_block_strings = preg_split(self::FILE_RANGE_PATTERN, $file_string);
      $header_string = $code_block_strings[0];
      //Explode each header into lines so we can easily gather the header data, it removes the "Index: " pattern
      $lines = explode(PHP_EOL, $header_string);
      //As the Index pattern got removed we can simply retrieve the whole line
      $code_file->setIndex($lines[0]);
      //Fill the rest of the header data
      //If the Index contains slashes we extract the name after the last slash, otherwise just take the whole line(name remains)
      $is_sub_path = (strpos($lines[0], self::FORWARD_SLASH) !== false) ? true : false;
      $code_file->setName($is_sub_path ? substr($lines[0], strrpos($lines[0], self::FORWARD_SLASH)+strlen(self::FORWARD_SLASH)) : $lines[0]);
      $code_file->setExtension(substr($lines[0], strrpos($lines[0], self::DOT)+strlen(self::DOT)));
      $full_source__and_revision = substr($lines[2], strlen(self::SOURCE_START));
      $code_file->setSource(substr($full_source__and_revision, 0,
          strrpos($full_source__and_revision, self::OPEN_PARENTHESIS)-self::SPACE_LENGTH));
      $pos_revision_string = strrpos($full_source__and_revision, self::REVISION)+strlen(self::REVISION);
      $code_file->setSourceRevision(substr($full_source__and_revision,
          strrpos($full_source__and_revision, self::OPEN_PARENTHESIS)+strlen(self::OPEN_PARENTHESIS),
          strrpos($full_source__and_revision, self::CLOSE_PARENTHESIS)-strlen(self::CLOSE_PARENTHESIS)-strrpos($full_source__and_revision, self::OPEN_PARENTHESIS)));
      $full_destination__and_revision = substr($lines[3], strlen(self::DESTINATION_START));
      $code_file->setDestination(substr($full_destination__and_revision, 0,
          strrpos($full_destination__and_revision, self::OPEN_PARENTHESIS)-self::SPACE_LENGTH));
      $code_file->setDestinationRevision(substr($full_destination__and_revision,
          strrpos($full_destination__and_revision, self::OPEN_PARENTHESIS)+strlen(self::OPEN_PARENTHESIS),
          strrpos($full_destination__and_revision, self::CLOSE_PARENTHESIS)-strlen(self::CLOSE_PARENTHESIS)-strrpos($full_destination__and_revision, self::OPEN_PARENTHESIS)));

      //Parse code blocks into CodeBlock objects
      $code_blocks = array();
      //Same as the for-loop above, the 1st record consists of nothing but whitespace so we start at the 2nd record
      for($j = 1 ; $j < count($code_block_strings) ; $j++) {
        $code_block_string = $code_block_strings[$j];
        //Retrieving the begin and endline of each code block as the split functionality to split each file into code blocks removes the
        //begin and endline used as the delimiter
        $startpos_of_code_block = strpos($file_string, $code_block_string);
        $start_of_delimiter = strrpos(substr($file_string, 0 , $startpos_of_code_block),
            self::FILE_RANGE_BRACKETS, -(strlen(self::FILE_RANGE_BRACKETS)+self::SPACE_LENGTH));
        $begin_and_end_line = substr($file_string, $start_of_delimiter, $startpos_of_code_block-$start_of_delimiter);
        $code_block = new CodeBlock;
        //Extract all the code block data and fill the CodeBlock object
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
