<?php

namespace Hostnet\HostnetCodeQualityBundle\Lib;

use Hostnet\HostnetCodeQualityBundle\Parser\OriginalFileRetriever\CGIT\CGITOriginalFileRetrieverParams;

use Hostnet\HostnetCodeQualityBundle\Parser\OriginalFileRetriever\RetrieveByCGIT;

use Doctrine\ORM\EntityManager;

use Hostnet\HostnetCodeQualityBundle\Entity\Review,
    Hostnet\HostnetCodeQualityBundle\Lib\EntityFactory,
    Hostnet\HostnetCodeQualityBundle\Parser\Diff\DiffFile,
    Hostnet\HostnetCodeQualityBundle\Parser\OriginalFileRetriever\OriginalFileRetrievalFactory,
    Hostnet\HostnetCodeQualityBundle\Parser\OriginalFileRetriever\OriginalFileRetrieverInterface,
    Hostnet\HostnetCodeQualityBundle\Parser\OriginalFileRetriever\OriginalFileRetrievalParams,
    Hostnet\HostnetCodeQualityBundle\Parser\CommandLineUtility,
    Hostnet\HostnetCodeQualityBundle\Parser\ParserFactory;

use Symfony\Component\HttpKernel\Log\LoggerInterface,
    Symfony\Component\Filesystem\Exception\IOException;

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

  /**
   * @var integer
   */
  private $to_be_processed_files_amount = 0;

  /**
   * @param EntityManager $em
   * @param LoggerInterface $logger
   * @param EntityFactory $ef
   * @param OriginalFileRetrievalFactory $original_file_retrieval_factory
   * @param CommandLineUtility $clu
   * @param ParserFactory $pf
   */
  public function __construct(EntityManager $em, LoggerInterface $logger, EntityFactory $ef,
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
    /* @var $diff_file DiffFile */
    foreach($diff_files as $key => $diff_file) {
      if(!$diff_file->isRemoved()) {
        $success = $this->processDiffFile($diff_file, $tools,
          $original_file_retrieval_params, $original_file_retriever);
        // If the diff file failes to be processed
        // we just skip it in the following process.
        if(!$success) {
          unset($diff_files[$key]);
        }
      }
    }

    $this->waitTillAllFilesProcessed($diff_files, $tools);

    foreach($diff_files as $diff_file) {
      if(!$diff_file->isRemoved() && !$diff_file->isRejected()) {
        foreach($tools as $tool) {
          if($tool->supports($diff_file->getExtension())) {
            $diff_file->retrieveAndSetToolOutput();
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

  /**
   * Processes the DiffFile and returns
   * the result of the processing.
   *
   * @param DiffFile $diff_file
   * @param array $tools
   * @param OriginalFileRetrievalParams $original_file_retrieval_params
   * @param OriginalFileRetrieverInterface $original_file_retriever
   * @return boolean
   */
  private function processDiffFile(DiffFile $diff_file, $tools,
    OriginalFileRetrievalParams $original_file_retrieval_params,
    OriginalFileRetrieverInterface $original_file_retriever)
  {
    foreach($tools as $tool) {
      if($tool->supports($diff_file->getExtension())) {
        // Check if the diff file is new. If it exists we retrieve the original file
        // and merge it. If it's new we don't have to retrieve the original
        // as there is none, so we just insert the whole diff code
        if($diff_file->hasParent()) {
          $original_file_retrieval_params->setDiffFile($diff_file);
          // Retrieves the original file based on the configured retrieval method
          $original_file = $original_file_retriever->retrieveOriginalFile($original_file_retrieval_params);

          if($original_file === false) {
            // Original file retrieval failed
            $this->logger->info('The file ' . $diff_file->getName() . '.' . $diff_file->getExtension()
              . ' has a source filled in but it was not possible to retrieve it. External project? ');
            return false;
          }
          $diff_file->setOriginalFile($original_file);
          // Merge the diff with the original in order to be able
          // to scan all the changes made in the actual code
          $diff_file->mergeDiffWithOriginal(
            $this->clu->getTempCodeQualityDirPath(),
            $this->pf->getSCM()
          );
          if($diff_file->isRejected()) {
            return false;
          }
          // Register an original file that has to be processed
          $this->to_be_processed_files_amount++;
        } else {
          $diff_file->createTempDiffFile($this->clu->getTempCodeQualityDirPath());
        }
        // Register a diff file that has to be processed
        $this->to_be_processed_files_amount++;

        // Let the file be processed by the given tool
        $diff_file->processFile($tool);

        return true;
      }
    }
  }

  /**
   * Waits until all the diff files
   * are processed by the tools
   *
   * @param array $diff_files
   * @param array $tools
   */
  private function waitTillAllFilesProcessed(
    $diff_files, $tools)
  {
    while(true) {
      // Sleep so we don't overload the amount of calls
      sleep(2);
      // Reset the amount of processed files to 0 each iteration
      $processed_files_amount = 0;
      foreach($diff_files as $diff_file) {
        if(!$diff_file->isRemoved()) {
          foreach($tools as $tool) {
            if($tool->supports($diff_file->getExtension())) {
              // If the diff is processed we increment the processed amount
              if($diff_file->isDoneProcessingDiff()) {
                $processed_files_amount++;
              }
              // Same goes for the original file
              if($diff_file->isDoneProcessingOriginal()) {
                $processed_files_amount++;
              }
            }
          }
        }
      }
      // If the amount of diff files that should be processed
      // have been processed we stop waiting.
      if($processed_files_amount == $this->to_be_processed_files_amount) {
        break;
      }
    }
  }
}
