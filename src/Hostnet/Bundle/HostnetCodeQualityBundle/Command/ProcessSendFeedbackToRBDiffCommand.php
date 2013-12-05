<?php

namespace Hostnet\Bundle\HostnetCodeQualityBundle\Command;

use Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Hostnet\Bundle\HostnetCodeQualityBundle\Command\Configuration\ReviewConfiguration,
    Hostnet\Bundle\HostnetCodeQualityBundle\Command\Definition\RBFeedbackDefinition,
    Hostnet\Bundle\HostnetCodeQualityBundle\Parser\OriginalFileRetriever\FeedbackReceiverInterface,
    Hostnet\Bundle\HostnetCodeQualityBundle\Parser\OriginalFileRetriever\ReviewBoard\ReviewBoardOriginalFileRetrieverParams;

use Doctrine\Common\Collection;

use InvalidArgumentException;

/**
 * Processes the Review Board diff based on the given review_request_id
 * by calling the cq:processDiff:RBDiff command on the CLI.
 * Input:   php app/console cq:processDiff:sendToRBDiff review_request_id [--publish_empty|-p] [--line_context|-c] [--line_limit|-l]
 * Example: php app/console cq:processDiff:sendToRBDiff       12345           -s true               -c 0               -l 25
 *
 * @author rprent
 */
class ProcessSendFeedbackToRBDiffCommand extends ContainerAwareCommand
{
  /**
   * Configures the command settings
   *
   * @see \Symfony\Component\Console\Command\Command::configure()
   */
  protected function configure()
  {
    $this
      ->setName('cq:processDiff:sendToRBDiff')
      ->setDescription('Scans the diff on the quality of the code and returns feedback.')
      ->setDefinition(new RBFeedbackDefinition(
        array(new InputArgument('review_request_id', InputArgument::REQUIRED,
          'The id of the review request to give feedback on.'))
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
    $review_request_id = $input->getArgument('review_request_id');
    $publish_empty = $input->getOption('publish_empty') !== false ? true : false;
    $line_context = $input->getOption('line_context');
    $line_limit = $input->getOption('line_limit');

    $original_file_retrieval_params = new ReviewBoardOriginalFileRetrieverParams($review_request_id);
    $diff = $rb_api_calls->retrieveDiff($review_request_id, null,
      FeedbackReceiverInterface::RESULT_TYPE_TEXT);
    // Process the review by calling the ReviewProcessor through the container
    $review = $this->getContainer()->get('review_processor')->processReview(
      $diff,
      true,
      $original_file_retrieval_params
    );

    $review_configuration = new ReviewConfiguration($review_request_id,
      $publish_empty, $line_context, $line_limit);
    $rb_api_calls->sendFeedbackToRB($review_configuration, $review);
  }
}
