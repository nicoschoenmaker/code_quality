<?php

namespace Hostnet\HostnetCodeQualityBundle\Tests\Controller;

use Hostnet\HostnetCodeQualityBundle\Controller\DefaultController;
use Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityTool;
use Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityReview;

class DefaultControllerTest extends \PHPUnit_Framework_TestCase
{
  /*public function testSendDiffToAPI()
  {

  }*/

  /**
   * Check if the diff is correctly parsed into CodeFile objects containing CodeBlock objects
   */
  public function testParseDiff()
  {
    //Load test patch file
    $diff_location = 'http://codequality.rickp.ontw.hostnetbv.nl/test_patch.patch';
    $diff = file_get_contents($diff_location);
    $code_files = DefaultController::parseDiff($diff);

    //first code file
    $code_file = $code_files[0];
    $this->assertEquals('apps/aurora/modules/uml/actions/components.class.php', $code_file->getIndex());
    $this->assertEquals('components.class.php', $code_file->getName());
    $this->assertEquals('php', $code_file->getExtension());
    $this->assertEquals('apps/aurora/modules/uml/actions/components.class.php', $code_file->getSource());
    $this->assertEquals('9347', $code_file->getSourceRevisionNumber());
    $this->assertEquals('apps/aurora/modules/uml/actions/components.class.php', $code_file->getDestination());
    $code_blocks = $code_file->getCodeBlocks();
    //first code block of first code file
    $code_block = $code_blocks[0];
    $this->assertEquals('21,11', $code_block->getBeginLine());
    $this->assertEquals('21,14', $code_block->getEndLine());

    //second code file
    $code_file = $code_files[4];
    $this->assertEquals('lib/classes/tracXmlRpcTickets.php', $code_file->getIndex());
    $this->assertEquals('tracXmlRpcTickets.php', $code_file->getName());
    $this->assertEquals('php', $code_file->getExtension());
    $this->assertEquals('lib/classes/tracXmlRpcTickets.php', $code_file->getSource());
    $this->assertEquals('9347', $code_file->getSourceRevisionNumber());
    $this->assertEquals('lib/classes/tracXmlRpcTickets.php', $code_file->getDestination());
    $code_blocks = $code_file->getCodeBlocks();
    //first code block of second code file
    $code_block = $code_blocks[0];
    $this->assertEquals('7,8', $code_block->getBeginLine());
    $this->assertEquals('7,13', $code_block->getEndLine());
    //second code blockof second code file
    $code_block = $code_blocks[2];
    $this->assertEquals('78,7', $code_block->getBeginLine());
    $this->assertEquals('88,7', $code_block->getEndLine());
  }

  /*public function find_all_occurences()
  {
    return strpos('', '', $position_after_last_position);
  }*/

  /*public function strpos_forward($haystack, $needle, $shift_forward = true, $search_first_occurence = true, $offset = 0)
  {
    $position = $search_first_occurence ? strpos($haystack, $needle, $offset) : strrpos($haystack, $needle, $offset);
    $needle_length = strlen($needle);
    $position += $shift_forward ? $needle_length : -$needle_length;

    return $position;
  }*/

  /*function get_string_between($haystack, $start, $end){
    $first_occurence = strpos($haystack, $start)+strlen($start);
    $last_occurence = strrpos($haystack, $end);
    $string_length = $last_occurence - $first_occurence;
    return substr($haystack, $first_occurence, $string_length);
  }*/

  /*public function testCreateCodeFile()
  {

  }*/


  /**
   * Code Quality Tools can be added through the Web-UI with their corresponding
   * path_to_tool, command and output format.
   * TODO: Factory pattern weghalen, zelfde impl voor alle tools.
   */
  public function testAddAndUseCQTool()
  {
    $result = false;
    //Create the PHPMD object and set all the user-filled properties.
    $PHPMD = new CodeQualityTool;
    $PHPMD->setName('PHPMD');
    $PHPMD->setPathToTool('~/projects/code_quality_tools/phpmd');
    $PHPMD->setCommand('phpmd ~/code_quality_tools/phpmd/test.class.php xml codesize,unusedcode,naming');
    $PHPMD->setFormat('xml');
    //Execute the command on the command line.
    exec($PHPMD->getCommand(), $tool_output);
    //$result = count(preg_grep('/violation/', $tool_output)) > 0 ? true : false;
    foreach($tool_output as $tool_output_line) {
      //Used the string 'violation' as those are the tags that represent possible
      //violations if they are found for the rule/metric.
      $result = strpos($tool_output_line, 'violation');
      if($result !== false) {
        break;
      }
    }
    //Perform the test and check if 'violation' has been found
    $this->assertTrue($result !== false);
  }

  /**
   * TODO: Log van tool-management activiteit bijhouden (insert / update / delete).
   */
  /*public function testCQToolOutputParsing()
  {

  }*/
}
