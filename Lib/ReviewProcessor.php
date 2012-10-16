<?php

namespace Hostnet\HostnetCodeQualityBundle\Lib;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Filesystem\Exception\IOException;

use Hostnet\HostnetCodeQualityBundle\Entity\Review,
    Hostnet\HostnetCodeQualityBundle\Lib\EntityFactory,
    Hostnet\HostnetCodeQualityBundle\Parser\CommandLineUtility,
    Hostnet\HostnetCodeQualityBundle\Parser\ParserFactory;

class ReviewProcessor
{
  /**
   *
   * @var EntityManager
   */
  private $em;

  /**
   * @var CommandLineUtility
   */
  private $clu;

  /**
   * @var ParserFactory
   */
  private $pf;

  /**
   * @var EntityFactory
   */
  private $ef;

  /**
   * The raw file url mask setting configured which
   * is used to retrieve the original file
   *
   * @var string
   */
  private $raw_file_url_mask;

  public function __construct(EntityManager $em, EntityFactory $ef,
    CommandLineUtility $clu, ParserFactory $pf, $raw_file_url_mask)
  {
    $this->em = $em;
    $this->ef = $ef;
    $this->clu = $clu;
    $this->pf = $pf;
    $this->raw_file_url_mask = $raw_file_url_mask;
  }

  /**
   * Processes the Diff into a Review object
   *
   * @param string $diff
   * @param boolean $register
   * @throws IOException
   * @return \Hostnet\HostnetCodeQualityBundle\Entity\Review
   */
  public function processReview($diff, $register)
  {
    $tools = $this->ef->retrieveTools();
    // Parse the diff into DiffFile objects
    $diff_parser = $this->pf->getDiffParserInstance();
    $diff_files = $diff_parser->parseDiff($diff);

    // Send each diff file to their specific tool based on the extension
    $review = new Review();
    foreach($diff_files as $diff_file) {
      foreach($tools as $tool) {
        if($tool->supports($diff_file->getExtension())) {
          // cgit implementation:
          // Retrieve the original code file based on the repository raw file url mask
          // and the original file name + parent revision number
          // TODO Make more original file extraction implementations possible
          $original_file = file_get_contents($this->raw_file_url_mask);
          /*$original_file = file_get_contents(
            $this->raw_file_url_mask .
            $code_file->getSource() .
            '?id2=' .
            $code_file->getSourceRevision()
          );*/
          // If the file_get_contents fails it returns false on failure which is why we throw an exception
          if(!$original_file) {
            throw new IOException("The file at '". $this->raw_file_url_mask . "' could not be found.");
          }

          // Let the file be processed by the given tool
          $diff_file->processFile(
            $tool,
            $original_file,
            $this->clu->getTempCodeQualityDirPath()
          );

          // Request the Tool Output Parser from the Factory
          $additional_tool_properties = array('format' => $tool->getFormat());
          $tool_output_parser = $this->pf->getToolOutputParserInstance(
            $tool->getName(),
            $additional_tool_properties
          );

          // Parse the Tool output into Report objects
          $report = $tool_output_parser->parseToolOutput($diff_file);

          // Add the Report object to the Review
          $review->getReports()->add($report);

          // Save the reviews if they should be registered
          if($register) {
            $this->em->persist($report);
            $this->em->flush();
          }
        }
      }
    }

    return $review;
  }
}
