<?php

namespace Hostnet\HostnetCodeQualityBundle\Command;

use Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Hostnet\HostnetCodeQualityBundle\Parser\OriginalFileRetriever\ReviewBoard\ReviewBoardOriginalFileRetrieverParams;

use Doctrine\Common\Collection;

use InvalidArgumentException;

/**
 * Processes all the Review Board diffs that haven't been processed yet
 * Input:   php app/console cq:cq:processAllNewDiffs
 *
 * @author rprent
 */
class ProcessAllPendingDiffsCommand extends ContainerAwareCommand
{
  /**
   * Configures the command settings
   *
   * @see \Symfony\Component\Console\Command\Command::configure()
   */
  protected function configure()
  {
    $this
      ->setName('cq:processAllPendingDiffs')
      ->setDescription('Scans all the pending review requests on their latest diff.'
        . ' It checks the quality of the code and returns feedback.')
      ->setDefinition(array(
        new InputOption('line_cap', 'c', InputOption::VALUE_REQUIRED,
          'The maximum number of lines per violation to be shown. Imagine a class with 2000 lines '
          . 'taking way too much space, therefore the default is at 5 lines.', 5)
      ))
    ;
  }

  /**
   * Executes the command
   *
   * @see \Symfony\Component\Console\Command\Command::execute()
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    // Get the Review Board Api Calls service for all the requests
    $rb_api_calls = $this->getContainer()->get('review_board_api_calls');
    // User CLI Input
    $line_cap = $input->getOption('line_cap');

    // Retrieve all the pending review requests
    // that haven't been processed yet
    $current_timestamp = strtotime(date('Y-m-d H:i:s'));
    // Retrieve the value of the temp
    // previously processed date file
    $previous_process_date_file =
      $this->getContainer()->getParameter('hostnet_code_quality.temp_cq_dir_name') . '/'
        . $this->getContainer()->getParameter('hostnet_code_quality.review_board_previous_process_date_file');
    $previous_process_timestamp = file_get_contents($previous_process_date_file);
    $review_requests = $rb_api_calls->retrievePendingReviewRequests();
    foreach($review_requests->review_requests as $review_request) {
      // get the last updated value from each review request
      // (includes diffs and comments)
      $last_updated = $this->parseRBDateToTimestamp($review_request->last_updated);
      if($last_updated > $previous_process_timestamp) {
        $review_request_id = $review_request->id;
        // Check if the latest diff is already processed
        $diff_object = json_decode($rb_api_calls->retrieveDiff($review_request_id, null, $rb_api_calls::RESULT_TYPE_JSON));
        $diff_timestamp = $this->parseRBDateToTimestamp($diff_object->diff->timestamp);
        if($diff_timestamp > $previous_process_timestamp) {
          // Retrieve the latest diff based on the review request id
          $diff = $rb_api_calls->retrieveDiff($review_request_id, null, $rb_api_calls::RESULT_TYPE_TEXT);
          // Pass the review request id wrapped in a OriginalFileRetrieverParams
          // in order to retrieve the original file
          $original_file_retrieval_params = new ReviewBoardOriginalFileRetrieverParams($review_request_id);
          $review = $this->getContainer()->get('review_processor')->processReview(
            $diff,
            true,
            $original_file_retrieval_params
          );
          // Send all the feedback to Review Board
          $rb_api_calls->sendFeedbackToRB($review_request_id, $review, $line_cap);
        }
      }
    }
    // Sets the new previously processed date to the current date
    file_put_contents($previous_process_date_file, $current_timestamp);
  }

  /**
   * Parses the Review Board date to a timestamp
   *
   * @param string $date
   * @return integer
   */
  private function parseRBDateToTimestamp($date)
  {
    return strtotime(substr(
      $date,
      0,
      strrpos($date, '.')
    )) + date('Z');
  }
}
