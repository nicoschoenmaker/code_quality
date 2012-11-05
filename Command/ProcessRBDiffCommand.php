<?php

namespace Hostnet\HostnetCodeQualityBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand,
    Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Output\OutputInterface;

use InvalidArgumentException;

/**
 * Processes the Review Board diff based on the given review_request_id
 * by calling the cq:processDiff:RBDiff command on the CLI.
 * Input:   php app/console cq:processDiff:RBDiff review_request_id repository [--diff_revision|-r]
 * Example: php app/console cq:processDiff:RBDiff       12345      code_quality        -r 2
 *
 * @author rprent
 */
class ProcessRBDiffCommand extends ContainerAwareCommand
{
  /**
   * Configures the command settings
   *
   * @see \Symfony\Component\Console\Command\Command::configure()
   */
  protected function configure()
  {
    $this
      ->setName('cq:processDiff:RBDiff')
      ->setDescription('Scans the diff on the quality of the code and returns feedback.')
      ->setDefinition(array(
        new InputArgument('review_request_id', InputArgument::REQUIRED,
            'The id of the review request to give feedback on.'),
        new InputArgument('repository', InputArgument::REQUIRED,
          'The repository that the review request is made for.'),
        new InputOption('diff_revision', 'd', InputOption::VALUE_REQUIRED,
          'The version of the diff. If no value is supplied the last one will be picked.')
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
    $diff_revision = $input->getOption('diff_revision');

    $diff = $rb_api_calls->retrieveDiff($review_request_id, $diff_revision, $rb_api_calls::RESULT_TYPE_TEXT);
    // Process the review by calling the ReviewProcessor through the container
    $review = $this->getContainer()->get('review_processor')->processReview(
      $diff,
      true,
      $repository
    );

    $output->write($review->__toString());
  }
}
