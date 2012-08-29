<?php

namespace Hostnet\HostnetCodeQualityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Hostnet\HostnetCodeQualityBundle\Entity\CodeFile;
use Hostnet\HostnetCodeQualityBundle\Entity\CodeBlock;

class DefaultController extends Controller
{
    CONST SPACE_LENGTH = 1;

    /**
     * @Route("/index")
     * @Template()
     */
    public function indexAction()
    {
      return $this->render('HostnetCodeQualityBundle:Default:index.html.twig');
    }

    /**
     * @Route("/viewDiffs")
     * @Template()
     */
    public function viewDiffsAction()
    {
      return $this->render('HostnetCodeQualityBundle:Default:viewDiffs.html.twig');
    }

    /**
     * @Route("/overview")
     * @Template()
     */
    public function overviewAction()
    {
      return $this->render('HostnetCodeQualityBundle:Default:overview.html.twig');
    }

    /**
     * @Route("/companyProfile")
     * @Template()
     */
    public function companyProfileAction()
    {
      return $this->render('HostnetCodeQualityBundle:Default:companyProfile.html.twig');
    }

    /**
     * @Route("/toolManagement")
     * @Template()
     */
    public function toolManagementAction()
    {
      return $this->render('HostnetCodeQualityBundle:Default:toolManagement.html.twig');
    }

    /**
     * Parse the diff into CodeFile objects
     *
     * @param String $diff
     * @return CodeFile array:
     */
    public function parseDiff($diff)
    {
      $index_pattern = 'Index: ';
      $file_range_pattern = '@@ -[0-9]*,[0-9]* \+[0-9]*,[0-9]* @@';
      $source_start = '+++ ';
      $destination_start = '--- ';
      $revision = 'revision ';
      $working_copy = '(working copy)';
      $file_range_brackets = '@@';
      $dot = '.';
      $slash = '/';
      $front_bracket = '(';
      $back_bracket = ')';
      $plus = '+';
      $minus = '-';

      //Split the patch file into seperate files
      $files = split($index_pattern, $diff);
      //Parse files into CodeFile objects
      $code_files = array();
      //The 1st record consists of nothing but whitespace so we start at the 2nd record
      for($i = 1 ; $i < count($files) ; $i++) {
        $file_string = $files[$i];
        $code_file = new CodeFile;
        //Explode each file into lines so we can easily gather the header data, it removes the "Index: " pattern
        $lines = explode(PHP_EOL, $file_string);
        //As the Index pattern got removed we can simply retrieve the whole line
        $code_file->setIndex($lines[0]);
        //Fill the rest of the header data
        $code_file->setName(substr($lines[0], strrpos($lines[0], $slash)+strlen($slash)));
        $code_file->setExtension(substr($lines[0], strrpos($lines[0], $dot)+strlen($dot)));
        $full_source__and_revision = substr($lines[2], strlen($source_start));
        $code_file->setSource(substr($full_source__and_revision, 0,
            strrpos($full_source__and_revision, $front_bracket, -2)-self::SPACE_LENGTH));
        $pos_revision_string = strrpos($full_source__and_revision, $revision, -2)+strlen($revision);
        $code_file->setSourceRevisionNumber(substr($full_source__and_revision, $pos_revision_string,
            strrpos($full_source__and_revision, $back_bracket)-$pos_revision_string));
        $code_file->setDestination(substr($lines[2], strlen($destination_start),
            strrpos($lines[2], $front_bracket)-(strlen($destination_start)+self::SPACE_LENGTH)));
        //Split each file into different code blocks based on the "@@ -[0-9]*,[0-9]* \+[0-9]*,[0-9]* @@" file_range pattern
        $code_block_strings = split($file_range_pattern,
            substr($file_string, strpos($file_string, $working_copy)+strlen($working_copy)));
        //Parse code blocks into CodeBlock objects
        $code_blocks = array();
        //Same as the for-loop above, the 1st record consists of nothing but whitespace so we start at the 2nd record
        for($j = 1 ; $j < count($code_block_strings) ; $j++) {
          $code_block_string = $code_block_strings[$j];
          //Retrieving the begin and endline of each code block as the split functionality to split each file into code blocks removes the
          //begin and endline used as the delimiter
          $startpos_of_code_block = strpos($file_string, $code_block_string);
          $start_of_delimiter = strrpos(substr($file_string, 0 , $startpos_of_code_block),
              $file_range_brackets, -(strlen($file_range_brackets)+self::SPACE_LENGTH));
          $begin_and_end_line = substr($file_string, $start_of_delimiter, $startpos_of_code_block-$start_of_delimiter);
          $code_block = new CodeBlock;
          //Extract all the code block data and fill the CodeBlock object
          $code_block->setBeginLine(substr($begin_and_end_line, strpos($begin_and_end_line, $minus)+strlen($minus),
              strpos($begin_and_end_line, $plus)-(strlen($plus)+self::SPACE_LENGTH)-strpos($begin_and_end_line, $minus)));
          $code_block->setEndLine(substr($begin_and_end_line, strpos($begin_and_end_line, $plus)+strlen($plus),
              strrpos($begin_and_end_line, $file_range_brackets)-(strlen($file_range_brackets)+self::SPACE_LENGTH)-
              strpos($begin_and_end_line, $plus)+strlen($plus)));
          $code_block->setCode(substr($code_block_string, strpos($code_block_string, $file_range_brackets)+
              strlen($file_range_brackets)+self::SPACE_LENGTH));
          array_push($code_blocks, $code_block);
        }
        $code_file->setCodeBlocks($code_blocks);
        array_push($code_files, $code_file);
      }
      return $code_files;
    }
}
