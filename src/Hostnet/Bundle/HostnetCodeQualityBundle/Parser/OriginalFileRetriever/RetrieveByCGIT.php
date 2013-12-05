<?php

namespace Hostnet\Bundle\HostnetCodeQualityBundle\Parser\OriginalFileRetriever;

use Hostnet\Bundle\HostnetCodeQualityBundle\Parser\OriginalFileRetriever\OriginalFileRetrieverInterface,
    Hostnet\Bundle\HostnetCodeQualityBundle\Parser\OriginalFileRetriever\OriginalFileRetrievalParams,
    Hostnet\Bundle\HostnetCodeQualityBundle\Parser\OriginalFileRetriever\CGIT\CGITOriginalFileRetrieverParams;

/**
 * The CGIT implementation of retrieving the original file
 * Retrieve the original code file based on the repository raw file url mask
 * and the original file name + parent/original revision number
 *
 * @author rprent
 */
class RetrieveByCGIT implements OriginalFileRetrieverInterface
{
  /**
   * The raw file url mask setting configured which
   * is used to retrieve the original file part 1
   *
   * @var string
   */
  private $raw_file_url_mask_1;

  /**
   * The raw file url mask setting configured which
   * is used to retrieve the original file part 2
   *
   * @var string
   */
  private $raw_file_url_mask_2;

  public function __construct($raw_file_url_mask_1, $raw_file_url_mask_2)
  {
    $this->raw_file_url_mask_1 = $raw_file_url_mask_1;
    $this->raw_file_url_mask_2 = $raw_file_url_mask_2;
  }

  /**
   * Retrieves the original file of the given diff with CGIT
   *
   * @param OriginalFileRetrievalParams $original_file_retrieval_params
   * @return string
   */
  public function retrieveOriginalFile(
    OriginalFileRetrievalParams $original_file_retrieval_params)
  {
    $diff_file = $original_file_retrieval_params->getDiffFile();
    $original_file_url = $this->raw_file_url_mask_1
      . $original_file_retrieval_params->getRepository()
      . $this->raw_file_url_mask_2
      . '/'
      . $diff_file->getSource()
      . '?id2='
      . $diff_file->getSourceRevision()
    ;

    return file_get_contents($original_file_url);
  }

  /**
   * Checks if the original file retriever supports the original
   * file retrieval params
   *
   * @param OriginalFileRetrievalParams $original_file_retrieval_params
   * @return boolean
   */
  public function supports(OriginalFileRetrievalParams $original_file_retrieval_params)
  {
    return $original_file_retrieval_params instanceof CGITOriginalFileRetrieverParams;
  }
}
