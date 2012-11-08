<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser\OriginalFileRetriever;

use Hostnet\HostnetCodeQualityBundle\Parser\OriginalFileRetriever\CGIT\CGITOriginalFileRetrieverParams,
    Hostnet\HostnetCodeQualityBundle\Parser\OriginalFileRetriever\ReviewBoard\ReviewBoardOriginalFileRetrieverParams,
    Hostnet\HostnetCodeQualityBundle\Parser\OriginalFileRetriever\OriginalFileRetrievalParams,
    Hostnet\HostnetCodeQualityBundle\Parser\OriginalFileRetriever\OriginalFileRetrieverInterface;

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
      if($original_file_retrieval_params instanceof ReviewBoardOriginalFileRetrieverParams
        && $original_file_retriever instanceof RetrieveByReviewBoard) {
        return $original_file_retriever;
      }
      else if($original_file_retrieval_params instanceof CGITOriginalFileRetrieverParams
        && $original_file_retriever instanceof RetrieveByCGIT) {
        return $original_file_retriever;
      }
    }

    throw new InvalidArgumentException("No original file retriever found for: '" .
      $this->original_file_retrieval_method . "'. Please configure the 'original_file_retrieval_method'" .
      ' setting correctly.');
  }
}
