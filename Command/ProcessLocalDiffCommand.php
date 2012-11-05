<?php

namespace Hostnet\HostnetCodeQualityBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand,
    Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Output\OutputInterface;

/**
 * Processes the local supplied diff by calling the cq:processDiff:localDiff command on the CLI.
 * Input:   php app/console cq:processDiff:localDiff path_to_diff  repository
 * Example: php app/console cq:processDiff:localDiff path/to/diff code_quality
 *
 * @author rprent
 */
class ProcessLocalDiffCommand extends ContainerAwareCommand
{
  /**
   * Configures the command settings
   *
   * @see \Symfony\Component\Console\Command\Command::configure()
   */
  protected function configure()
  {
    $this
      ->setName('cq:processDiff:localDiff')
      ->setDescription('Scans the diff on the quality of the code and returns feedback.')
      ->setDefinition(array(
        new InputArgument('path_to_diff', InputArgument::REQUIRED,
          'Path to the diff / patch file.'),
        new InputArgument('repository', InputArgument::REQUIRED,
          'The repository that the review request is made for.')
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
    // User CLI Input
    // Path to diff arg & retrieve the diff
    $path_to_diff = $input->getArgument('path_to_diff');
    $repository = $input->getArgument('repository');
    $diff = file_get_contents($path_to_diff);

    // Process the review by calling the ReviewProcessor through the container
    $review = $this->getContainer()->get('review_processor')->processReview(
      $diff,
      true,
      $repository
    );

    $output->write((string) $review);
  }
}
