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
    $review_requests = $rb_api_calls->retrievePendingReviewRequests();
    foreach($review_requests->review_requests as $review_request) {
      $review_request_id = $review_request->id;
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
