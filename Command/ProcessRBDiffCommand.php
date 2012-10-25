<?php

namespace Hostnet\HostnetCodeQualityBundle\Command;

use JMS\SerializerBundle\Exception\XmlErrorException;

use Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Doctrine\Common\Collection;

use InvalidArgumentException;

/**
 * Processes the Review Board diff based on the given review_request_id
 * by calling the cq:processDiff:RBDiff command on the CLI.
 * Input:   php app/console cq:processDiff:RBDiff review_request_id [--diff_revision|-dr] [--register|-r]
 * Example: php app/console cq:processDiff:RBDiff       12345               -dr 2             -r true
 *
 * @author rprent
 */
class ProcessRBDiffCommand extends ContainerAwareCommand
{
  const RESULT_TYPE_XML = 'Accept: application/xml';
  const RESULT_TYPE_PATCH = 'Accept: text/x-patch';
  const ITEM_TAG_NAME = 'item';

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
          'The version of the diff. If no value is supplied the last one will be picked.'),
        new InputOption('register', 'r', InputOption::VALUE_REQUIRED, 'Register the diff.', false)
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
    // Review request id arg
    $review_request_id = $input->getArgument('review_request_id');
    $repository = $input->getArgument('repository');
    $diff_revision = $input->getOption('diff_revision');
    // If the supplied diff revision is not numeric we throw an exception
    if(isset($diff_revision) && !is_numeric($diff_revision)) {
      throw new InvalidArgumentException('The --diff_revision option value has to contain a number!');
    }

    // Review Board API Data
    $domain = $this->getContainer()->getParameter('hostnet_code_quality.domain');
    // Retrieve the list of diffs of the review request
    $diff_list_url = $domain . '/api/review-requests/' . $review_request_id . '/diffs/';
    $diff_list_in_xml = $this->executeCURLRequest($diff_list_url, self::RESULT_TYPE_XML);

    $xml = new \DomDocument();
    // Try to load the xml data, if it fails we throw an exception
    if(!$xml->loadXML($diff_list_in_xml)) {
      throw new XmlErrorException('Error while parsing XML, invalid XML supplied');
    }

    $diff_list = $xml->getElementsByTagName(self::ITEM_TAG_NAME);
    // If the length of the diff list is 0 it means that
    // the review request has no diffs so we throw an exception
    if(!$diff_list->length) {
      throw new InvalidArgumentException('No diffs found for the selected review request id, '
        . 'are you sure that you selected the right review request?');
    }
    // If the supplied diff revision is bigger than the number of diffs we throw an exception
    if($diff_revision > $diff_list->length) {
      throw new InvalidArgumentException('The given diff revision does not exist, '
        . 'try a value from 1 to '. $diff_list->length);
    }
    // If the diff revision is supplied we use it, otherwise default to the last diff
    $diff_number = ($diff_revision) ? $diff_revision : $diff_list->length;

    // Retrieve the diff patch
    $diff_url = $diff_list_url . $diff_number . '/';
    $diff = $this->executeCURLRequest($diff_url, self::RESULT_TYPE_PATCH);

    // If the optional register arg is set put it on true, otherwise default to false
    $register = $input->getOption('register');
    if(isset($register) && $register !== false) {
      $register = true;
    }

    // Process the review by calling the ReviewProcessor through the container
    $review = $this->getContainer()->get('review_processor')->processReview(
      $diff,
      $register,
      $repository
    );

    $output->write($review->__toString());
  }

  /**
   * Executes a curl request and returns the output
   *
   * @param string $url
   * @param string $result_type
   * @return mixed
   */
  protected function executeCURLRequest($url, $result_type)
  {
    // Initialize the curl handler
    $ch = curl_init();
    // Set the curl options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array($result_type));
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Execute the curl session
    $output = curl_exec($ch);
    // Close the curl connection
    curl_close($ch);

    return $output;
  }
}
