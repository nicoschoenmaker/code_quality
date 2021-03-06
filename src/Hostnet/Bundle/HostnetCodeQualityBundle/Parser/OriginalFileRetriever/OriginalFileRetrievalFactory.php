<?php

namespace Hostnet\Bundle\HostnetCodeQualityBundle\Parser\OriginalFileRetriever;

use Hostnet\Bundle\HostnetCodeQualityBundle\Parser\OriginalFileRetriever\OriginalFileRetrievalParams,
    Hostnet\Bundle\HostnetCodeQualityBundle\Parser\OriginalFileRetriever\OriginalFileRetrieverInterface;

use InvalidArgumentException;

/**
 * The original file retrieval factory holds the possible implementation
 * instances of retrieving the original file
 *
 * @author rprent
 */
class OriginalFileRetrievalFactory
{
  /**
   * An array of the original file retrievers,
   * each should perform its own method of retrieving the
   * original file
   *
   * @var array
   */
  private $original_file_retrievers = array();

  /**
   * Adds an OriginalFileRetriever instance to the
   * original file retrievers array
   *
   * @param OriginalFileRetrieverInterface $original_file_retriever
   */
  public function addOriginalFileRetrieverInstance(
    OriginalFileRetrieverInterface $original_file_retriever)
  {
    $this->original_file_retrievers[] = $original_file_retriever;
  }

  /**
   * Gets an OriginalFileRetriever instance from the
   * original file retrievers array
   *
   * @param OriginalFileRetrievalParams $original_file_retrieval_params
   * @return OriginalFileRetrieverInterface
   */
  public function getOriginalFileRetrieverInstance(
    OriginalFileRetrievalParams $original_file_retrieval_params)
  {
    // Switch cases unfortunately aren't possible with instanceof
    foreach($this->original_file_retrievers as $original_file_retriever) {
      if($original_file_retriever->supports($original_file_retrieval_params)) {
        return $original_file_retriever;
      }
    }

    throw new InvalidArgumentException("No original file retriever found for: '" .
      $this->original_file_retrieval_method . "'. Please configure the 'original_file_retrieval_method'" .
      ' setting correctly.');
  }
}
