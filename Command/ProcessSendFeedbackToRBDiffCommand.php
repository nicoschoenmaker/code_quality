<?php

namespace Hostnet\HostnetCodeQualityBundle\Command;

use Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Hostnet\HostnetCodeQualityBundle\Lib\FeedbackReceiver\FeedbackReceiverInterface;

use Doctrine\Common\Collection;

use InvalidArgumentException;

/**
 * Processes the Review Board diff based on the given review_request_id
 * by calling the cq:processDiff:RBDiff command on the CLI.
 * Input:   php app/console cq:processDiff:RBDiff review_request_id repository [--line_cap|-c]
 * Example: php app/console cq:processDiff:RBDiff       12345      code_quality     -c 25
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
      ->setDefinition(array(
        new InputArgument('review_request_id', InputArgument::REQUIRED,
          'The id of the review request to give feedback on.'),
        new InputArgument('repository', InputArgument::REQUIRED,
          'The repository that the review request is made for.'),
        new InputOption('line_cap', 'c', InputOption::VALUE_REQUIRED,
          'The maximum number of lines per violation to be shown. Imagine a class with 2000 lines '
          . 'taking way too much space, therefore the default is at 50 lines.', 50)
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
    $repository = $input->getArgument('repository');
    $line_cap = $input->getOption('line_cap');

    // Process the review by calling the ReviewProcessor through the container
    $diff = $rb_api_calls->retrieveDiff($review_request_id, null, FeedbackReceiverInterface::RESULT_TYPE_TEXT);
    $review = $this->getContainer()->get('review_processor')->processReview(
      $diff,
      true,
      $repository
    );

    $rb_api_calls->sendFeedbackToRB($review_request_id, $review, $line_cap);
  }
}
