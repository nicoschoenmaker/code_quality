<?php

namespace Hostnet\HostnetCodeQualityBundle\Command;

use Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Output\OutputInterface;

use Doctrine\Common\Collection;

class ProcessDiffCommand extends Command
{
  protected function configure()
  {
    $this
      ->setName('cq:processDiff')
      ->setDescription('Scans the diff on the quality of the code and returns feedback.')
      ->setDefinition(array(
        new InputArgument('path_to_diff', InputArgument::REQUIRED, 'Path to the diff'),
        new InputArgument('review_request_id', InputArgument::REQUIRED,
          'The id of the review request to give feedback on'),
        new InputArgument('register', InputArgument::OPTIONAL, 'Register the diff')
      ))
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    // User CLI Input
    // Path to diff arg & retrieve the diff
    $path_to_diff = $input->getArgument('path_to_diff');
    $diff = file_get_contents('/' . $path_to_diff);
    // Review request id arg
    $review_request_id = $input->getArgument('review_request_id');
    // If the optional register arg is set use it, otherwise default to false
    $register = isset($input->getArgument('register')) ?
      $input->getArgument('register') : false;

    // Get the container from the kernel in order to call all the required services.
    $this->container = $this->getApplication()->getKernel()->getContainer();
    // All the required services
    $em = $this->container->get('doctrine')->getEntityManager();
    $tools = $this->container->get('entity_factory')->retrieveTools();
    $review = $this->container->get('review_processor')->processReview(
      $diff,
      $register,
      $em,
      $tools
    );
  }
}
