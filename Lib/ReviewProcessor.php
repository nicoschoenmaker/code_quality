<?php

namespace Hostnet\HostnetCodeQualityBundle\Lib;

use Monolog\Logger;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Filesystem\Exception\IOException;

use Hostnet\HostnetCodeQualityBundle\Entity\Review,
    Hostnet\HostnetCodeQualityBundle\Lib\EntityFactory,
    Hostnet\HostnetCodeQualityBundle\Parser\OriginalFileRetriever\OriginalFileRetrievalFactory,
    Hostnet\HostnetCodeQualityBundle\Parser\OriginalFileRetriever\OriginalFileRetrievalParams,
    Hostnet\HostnetCodeQualityBundle\Parser\CommandLineUtility,
    Hostnet\HostnetCodeQualityBundle\Parser\ParserFactory;

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
   * @var Logger
   */
  private $logger;

  /**
   * @var OriginalFileRetrievalFactory
   */
  private $original_file_retrieval_factory;

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

  public function __construct(EntityManager $em, Logger $logger, EntityFactory $ef,
    OriginalFileRetrievalFactory $original_file_retrieval_factory,
    CommandLineUtility $clu, ParserFactory $pf)
  {
    $this->em = $em;
    $this->logger = $logger;
    $this->ef = $ef;
    $this->original_file_retrieval_factory = $original_file_retrieval_factory;
    $this->clu = $clu;
    $this->pf = $pf;
  }

  /**
   * Processes the Diff into a Review object
   *
   * @param string $diff
   * @param boolean $register
   * @param OriginalFileRetrievalParams $original_file_retrieval_params
   * @throws IOException
   * @return Review
   */
  public function processReview($diff, $register,
    OriginalFileRetrievalParams $original_file_retrieval_params)
  {
    $tools = $this->ef->retrieveTools();
    // Parse the diff into DiffFile objects
    $diff_parser = $this->pf->getDiffParserInstance();
    $diff_files = $diff_parser->parseDiff($diff);

    // Gets the correct original file retriever based on the given param class
    $original_file_retriever =
      $this->original_file_retrieval_factory->getOriginalFileRetrieverInstance($original_file_retrieval_params);

    // Send each diff file to their specific tool based on the extension
    $review = new Review();
    // Tell the Entity Factory whether we want to register the Review or not
    $this->ef->setRegister($register);
    $this->ef->persistAndFlush($review);
    foreach($diff_files as $diff_file) {
      if(!$diff_file->isRemoved()) {
        foreach($tools as $tool) {
          if($tool->supports($diff_file->getExtension())) {
            // Check if the diff file is new. If it exists we retrieve the original file
            // and merge it. If it's new we don't have to retrieve the original
            // as there is none, so we just insert the whole diff code
            if($diff_file->hasParent()) {
              $original_file_retrieval_params->setDiffFile($diff_file);
              // Retrieves the original file based on the configured retrieval method
              $diff_file->setOriginalFile(
                $original_file_retriever->retrieveOriginalFile($original_file_retrieval_params)
              );
              // Merge the diff with the original in order to be able
              // to scan all the changes made in the actual code
              $diff_file->mergeDiffWithOriginal(
                $this->clu->getTempCodeQualityDirPath(),
                $this->pf->getSCM()
              );
            } else {
              $diff_file->createTempDiffFile($this->clu->getTempCodeQualityDirPath());
            }

            // Let the file be processed by the given tool
            $diff_file->processFile($tool);

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
          } else {
            // If the tool doesn't support the extension we report it to the user.
            $file_not_supported_info =
              "The file " . $diff_file->getName() . '.' . $diff_file->getExtension()
              . ' has the ' . $diff_file->getExtension()
              . ' extension, which is not supported by ' . $tool->getName() . '.' . PHP_EOL
              . 'If ' . $tool->getName() . ' should support the ' . $diff_file->getExtension()
              . ' extension you should contact your administrator to enable it.' . PHP_EOL . PHP_EOL;
            $this->logger->info($file_not_supported_info);
          }
        }
      }
    }
    $this->ef->persistAndFlush($review);

    return $review;
  }
}
