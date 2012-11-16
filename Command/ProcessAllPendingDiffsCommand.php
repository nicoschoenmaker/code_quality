<?php

namespace Hostnet\HostnetCodeQualityBundle\Command;

use Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Hostnet\HostnetCodeQualityBundle\Lib\FeedbackReceiver\ReviewBoard\ReviewBoardAPICalls,
    Hostnet\HostnetCodeQualityBundle\Parser\OriginalFileRetriever\FeedbackReceiverInterface,
    Hostnet\HostnetCodeQualityBundle\Parser\OriginalFileRetriever\ReviewBoard\ReviewBoardOriginalFileRetrieverParams;

use Doctrine\Common\Collection;

use DateTime,
    InvalidArgumentException;

/**
 * Processes all the Review Board diffs that haven't been processed yet
 * Input:   php app/console cq:processAllNewDiffs [--show_success|-s] [--line_context|-c] [--line_limit|-l]
 * Example: php app/console cq:processAllNewDiffs       -s true               -c 0              -l 25
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
        new InputOption('show_success', 'sc', InputOption::VALUE_REQUIRED,
          "Sends a comment if there are no violations to display. This can be used in combination with "
            . "the configurable auto_shipit setting to auto shipit if no violations found. "
            . "Defaults to false", false),
        new InputOption('line_context', 'c', InputOption::VALUE_REQUIRED,
          "The amount of lines width around the violated line that should be shown as 'context'.", 1),
        new InputOption('line_limit', 'l', InputOption::VALUE_REQUIRED,
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
    $rb_api_calls = $this->getReviewBoardAPICalls();
    // User CLI Input
    $show_success = $input->getOption('show_success');
    $line_context = $input->getOption('line_context');
    $line_limit = $input->getOption('line_limit');

    // Retrieve all the pending review requests
    // that haven't been processed yet
    $current_datetime = new DateTime();
    $current_timestamp = $current_datetime->getTimestamp();
    // Retrieve the value of the temp
    // previously processed date file
    $previous_process_date_file = $this->getPreviousProcessedDateFilename();
    if(file_exists($previous_process_date_file)) {
      $previous_process_timestamp = file_get_contents($previous_process_date_file);
    } else {
      // If this is the first call made we set the timestamp to 0
      $previous_process_timestamp = 0;
    }

    $review_requests = $rb_api_calls->retrievePendingReviewRequests();
    foreach($review_requests->review_requests as $review_request) {
      // get the last updated value from each review request
      // (includes diffs and comments)
      $last_updated = $this->parseRBDateToTimestamp($review_request->last_updated);
      if($last_updated > $previous_process_timestamp) {
        $review_request_id = $review_request->id;
        // Check if the latest diff is already processed
        $diff_object = json_decode($rb_api_calls->retrieveDiff($review_request_id, null, FeedbackReceiverInterface::RESULT_TYPE_JSON));
        $diff_timestamp = $this->parseRBDateToTimestamp($diff_object->diff->timestamp);
        if($diff_timestamp > $previous_process_timestamp) {
          // Retrieve the latest diff based on the review request id
          $diff = $rb_api_calls->retrieveDiff($review_request_id, null, FeedbackReceiverInterface::RESULT_TYPE_TEXT);
          // Pass the review request id wrapped in a OriginalFileRetrieverParams
          // in order to retrieve the original file
          $original_file_retrieval_params = new ReviewBoardOriginalFileRetrieverParams($review_request_id);
          $review = $this->getContainer()->get('review_processor')->processReview(
            $diff,
            true,
            $original_file_retrieval_params
          );
          // Send all the feedback to Review Board
          $rb_api_calls->sendFeedbackToRB(
            $review_request_id, $review, $show_success, $line_context, $line_limit
          );
        }
      }
    }
    // Sets the new previously processed date to the current date
    file_put_contents($previous_process_date_file, $current_timestamp);
  }

  /**
   * Gets the Review Board API
   *
   * @return ReviewBoardAPICalls
   */
  private function getReviewBoardAPICalls()
  {
    return $this->getContainer()->get('review_board_api_calls');
  }

  /**
   * Gets the previously processed date filename
   * from the temp directory
   *
   * @return string
   */
  private function getPreviousProcessedDateFilename()
  {
    return $this->getContainer()->getParameter('hostnet_code_quality.temp_cq_dir_name') . '/'
      . $this->getContainer()->getParameter('hostnet_code_quality.review_board_previous_process_date_file');
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
