<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser\OriginalFileRetriever;

use Hostnet\HostnetCodeQualityBundle\Parser\OriginalFileRetriever\OriginalFileRetrieverInterface;

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
   * Specifies how the original file should be retrieved
   *
   * @var string
   */
  private $original_file_retrieval_method = '';

  public function __construct($original_file_retrieval_method)
  {
    $this->original_file_retrieval_method = $original_file_retrieval_method;
  }

  /**
   * Adds an OriginalFileRetriever instance to the
   * original file retrievers array
   *
   * @param OriginalFileRetrieverInterface $original_file_retriever
   */
  public function addOriginalFileRetrieverInstance(OriginalFileRetrieverInterface $original_file_retriever)
  {
    $this->original_file_retrievers[] = $original_file_retriever;
  }

  /**
   * Gets an OriginalFileRetriever instance from the
   * original file retrievers array
   *
   * @return OriginalFileRetrieverInterface
   */
  public function getOriginalFileRetrieverInstance()
  {
    $retriever = null;
    foreach($this->original_file_retrievers as $original_file_retriever) {
      if($original_file_retriever->supports($this->original_file_retrieval_method)) {
        $retriever = $original_file_retriever;
      }
    }

    if(!$retriever) {
      throw new InvalidArgumentException("No original file retriever found for: '" .
        $this->original_file_retrieval_method . "'. Please configure the 'original_file_retrieval_method'" .
        ' setting correctly.');
    }
    return $retriever;
  }
}
