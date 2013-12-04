<?php

namespace Hostnet\CodeQualityBundle\Lib;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Filesystem\Exception\IOException;

use Hostnet\CodeQualityBundle\Entity\Review,
    Hostnet\CodeQualityBundle\Lib\EntityFactory,
    Hostnet\CodeQualityBundle\Parser\CommandLineUtility,
    Hostnet\CodeQualityBundle\Parser\ParserFactory;

/**
 * The Review Processor processes the whole review.
 * Most of the important component calls like calling parsers
 * is done in this class.
 *
 * @author rprent
 */
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
   * @return \Hostnet\CodeQualityBundle\Entity\Review
   */
  public function processReview($diff, $register)
  {
    $tools = $this->ef->retrieveTools();
    // Parse the diff into DiffFile objects
    $diff_parser = $this->pf->getDiffParserInstance();
    $diff_files = $diff_parser->parseDiff($diff);

    // Send each diff file to their specific tool based on the extension
    $review = new Review();
    // Tell the Entity Factory whether we want to register the Review or not
    $this->ef->setRegister($register);
    $this->ef->persistAndFlush($review);
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
          $report->setReview($review);
          $review->getReports()->add($report);
        }
      }
    }
    $this->ef->persistAndFlush($review);

    return $review;
  }
}
