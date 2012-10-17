<?php

namespace Hostnet\HostnetCodeQualityBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Output\OutputInterface;

use Doctrine\Common\Collection;

/**
 * Processes the diff by calling the cq:processDiff command on the CLI.
 * Input: php app/console cq:processDiff path_to_diff review_request_id [-register|-r]
 * Example: php app/console cq:processDiff path/to/diff 43261 -register false
 *
 * @author rprent
 */
class ProcessDiffCommand extends ContainerAwareCommand
{
  /**
   * Configures the command settings
   *
   * @see \Symfony\Component\Console\Command\Command::configure()
   */
  protected function configure()
  {
    $this
      ->setName('cq:processDiff')
      ->setDescription('Scans the diff on the quality of the code and returns feedback.')
      ->setDefinition(array(
        new InputArgument('path_to_diff', InputArgument::REQUIRED, 'Path to the diff / patch file'),
        new InputArgument('review_request_id', InputArgument::REQUIRED,
          'The id of the review request to give feedback on'),
        new InputOption('register', 'r', InputOption::VALUE_REQUIRED, 'Register the diff', false)
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
    $diff = file_get_contents('/' . $path_to_diff);
    // Review request id arg
    $review_request_id = $input->getArgument('review_request_id');
    // If the optional register arg is set put it on true, otherwise default to false
    $register = $input->getOption('register');
    if(isset($register) && $register !== false) {
      $register = true;
    }

    $this->container = $this->getApplication()->getKernel()->getContainer();
    // Process the review by calling the ReviewProcessor through the container
    $review = $this->container->get('review_processor')->processReview(
      $diff,
      $register
    );

    echo $review;
  }
}
