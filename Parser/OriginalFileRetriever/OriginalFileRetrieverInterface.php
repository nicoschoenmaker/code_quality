<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser\OriginalFileRetriever;

use Hostnet\HostnetCodeQualityBundle\Parser\OriginalFileRetriever\OriginalFileRetrievalParams;

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
}
