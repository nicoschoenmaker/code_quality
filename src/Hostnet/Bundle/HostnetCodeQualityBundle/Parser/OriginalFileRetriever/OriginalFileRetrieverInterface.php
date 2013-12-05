<?php

namespace Hostnet\Bundle\HostnetCodeQualityBundle\Parser\OriginalFileRetriever;

use Hostnet\Bundle\HostnetCodeQualityBundle\Parser\OriginalFileRetriever\OriginalFileRetrievalParams;

/**
 * The OriginalFileRetriever interface
 *
 * @author rprent
 */
interface OriginalFileRetrieverInterface
{
  /**
   * Retrieves the original file of a diff
   *
   * @param OriginalFileRetrievalParams $original_file_retrieval_params
   */
  public function retrieveOriginalFile(OriginalFileRetrievalParams $original_file_retrieval_params);

  /**
   * Checks if the original file retriever supports the original
   * file retrieval params
   *
   * @param OriginalFileRetrievalParams $original_file_retrieval_params
   * @return boolean
   */
  public function supports(OriginalFileRetrievalParams $original_file_retrieval_params);
}
