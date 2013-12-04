<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser\DiffParser;

use Hostnet\HostnetCodeQualityBundle\Parser\Diff\DiffFile,
    Hostnet\HostnetCodeQualityBundle\Parser\Diff\DiffCodeBlock,
    Hostnet\HostnetCodeQualityBundle\Parser\DiffParser\AbstractDiffParser,
    Hostnet\HostnetCodeQualityBundle\Parser\DiffParser\DiffParserInterface;

/**
 * The SVN diff parser that parses SVN diffs
 * (requires an update)
 *
 * @author rprent
 */
class SVNDiffParser extends AbstractDiffParser implements DiffParserInterface
{
  CONST START_OF_FILE_PATTERN = 'Index: ';
  CONST REVISION = 'revision ';

  public function __construct()
  {
    $this->resource = 'svn';
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
    for($i = 1; $i < count($files); $i++) {
      $file_string = $files[$i];
      // Split each file into different diff code blocks based on the file range pattern
      $diff_diff_code_block_strings = preg_split(self::FILE_RANGE_PATTERN, $file_string);
      $header_string = $diff_diff_code_block_strings[0];
      // Parse the header data
      $diff_file = new DiffFile();
      $diff_file->setDiffFile($file_string);
      $this->parseDiffHead($diff_file, $header_string);

      // Parse diff code blocks into DiffCodeBlock objects
      $diff_diff_code_blocks = array();
      // Same as the for-loop above, the 1st record consists of
      // nothing but whitespace so we start at the 2nd record
      for($j = 1; $j < count($diff_diff_code_block_strings); $j++) {
        $body_string = $diff_diff_code_block_strings[$j];
        $diff_code_block = $this->parseDiffBody($file_string, $body_string);
        $diff_diff_code_blocks[] = $diff_code_block;
      }
      $diff_file->setDiffCodeBlocks($diff_diff_code_blocks);
      $diff_files[] = $diff_file;
    }

    return $diff_files;
  }

  /**
   * Parse the diff header data
   *
   * @param DiffFile $diff_file
   * @param String $header_string
   * @see \Hostnet\HostnetCodeQualityBundle\Parser\DiffParser\DiffParserInterface::parseDiffHead()
   */
  public function parseDiffHead(DiffFile $diff_file, $header_string)
  {
    // Explode each header into lines so we can easily gather the header data,
    // it removes the "Index: " pattern
    $lines = explode(PHP_EOL, $header_string);
    // As the Index pattern got removed we can simply retrieve the whole line
    $diff_file->setIndex($lines[0]);
    // Fill the rest of the header data
    // If the Index contains slashes we extract the name after the last slash,
    // otherwise just take the whole line(name remains)
    $is_sub_path = (strpos($lines[0], self::T_FORWARD_SLASH) !== false) ? true : false;
    $diff_file->setName(
      $is_sub_path ? substr(
        $lines[0],
        strrpos($lines[0],
        self::T_FORWARD_SLASH) + strlen(self::T_FORWARD_SLASH)
      ) : $lines[0]
    );
    $diff_file->setExtension(substr(
      $lines[0],
      strrpos($lines[0], self::T_DOT) + strlen(self::T_DOT)
    ));
    $full_source__and_revision = substr($lines[2], strlen(self::SOURCE_START));
    $diff_file->setSource(substr(
      $full_source__and_revision,
      0,
      strrpos($full_source__and_revision, self::T_OPEN_PARENTHESIS)
        - self::T_SPACE_LENGTH
    ));
    $pos_revision_string = strrpos(
      $full_source__and_revision,
      self::REVISION
    ) + strlen(self::REVISION);
    $diff_file->setSourceRevision(substr(
      $full_source__and_revision,
      strrpos(
        $full_source__and_revision,
        self::T_OPEN_PARENTHESIS) + strlen(self::T_OPEN_PARENTHESIS),
        strrpos(
          $full_source__and_revision,
          self::T_CLOSE_PARENTHESIS)
            - strlen(self::T_CLOSE_PARENTHESIS)
            - strrpos($full_source__and_revision, self::T_OPEN_PARENTHESIS)
    ));
    $full_destination__and_revision = substr(
      $lines[3],
      strlen(self::DESTINATION_START)
    );
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
    // Retrieving the begin and endline of each code block as the split functionality to split each file into diff code blocks removes the
    // begin and endline used as the delimiter
    $startpos_of_diff_code_block = strpos($file_string, $body_string);
    $start_of_delimiter = strrpos(
      substr(
        $file_string,
        0,
        $startpos_of_diff_code_block
      ),
      self::FILE_RANGE_BRACKETS,
      -(strlen(self::FILE_RANGE_BRACKETS) + self::T_SPACE_LENGTH)
    );
    $begin_and_end_line = substr(
      $file_string,
      $start_of_delimiter,
      $startpos_of_diff_code_block - $start_of_delimiter
    );

    // Extract all the code block data and fill the DiffCodeBlock object
    $diff_code_block = new DiffCodeBlock;
    $diff_code_block->setBeginLine(substr(
      $begin_and_end_line,
      strpos(
        $begin_and_end_line,
        self::T_MINUS
      ) + strlen(self::T_MINUS),
      strpos($begin_and_end_line, self::T_PLUS)
        - (strlen(self::T_PLUS) + self::T_SPACE_LENGTH)
        - strpos($begin_and_end_line, self::T_MINUS)
    ));
    $diff_code_block->setEndLine(substr(
      $begin_and_end_line,
      strpos($begin_and_end_line, self::T_PLUS) + strlen(self::T_PLUS),
      strrpos(
        $begin_and_end_line,
        self::FILE_RANGE_BRACKETS
      ) - (strlen(self::FILE_RANGE_BRACKETS) + self::T_SPACE_LENGTH)
        - strpos($begin_and_end_line, self::T_PLUS)+strlen(self::T_PLUS)
    ));
    $diff_code_block->setCode(substr(
      $body_string,
      strpos($body_string, self::FILE_RANGE_BRACKETS)
        + strlen(self::FILE_RANGE_BRACKETS) + self::T_SPACE_LENGTH
    ));

    return $diff_code_block;
  }
}
