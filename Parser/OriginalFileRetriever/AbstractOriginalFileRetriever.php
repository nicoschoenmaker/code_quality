<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser\OriginalFileRetriever;

/**
 * The abstract original file retriever which has to be extended
 * in order to specify a new original file retrieval method
 *
 * @author rprent
 */
class AbstractOriginalFileRetriever
{
  /**
   * The retrieval method that the original file retriever supports
   *
   * @var string
   */
  protected $original_file_retrieval_method;

  /**
   * Checks if the original file retriever supports the retrieval method
   *
   * @param string $retrieval_method
   * @return boolean
   */
  public function supports($retrieval_method)
  {
    return (strcasecmp($this->original_file_retrieval_method, $retrieval_method) == 0) ? true : false;
  }
}
