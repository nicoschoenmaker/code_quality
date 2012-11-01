<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser\OriginalFileRetriever;

use Hostnet\HostnetCodeQualityBundle\Parser\OriginalFileRetriever\OriginalFileRetrieverInterface,
    Hostnet\HostnetCodeQualityBundle\Parser\OriginalFileRetriever\AbstractOriginalFileRetriever,
    Hostnet\HostnetCodeQualityBundle\Parser\Diff\DiffFile;

/**
 * The CGIT implementation of retrieving the original file
 * Retrieve the original code file based on the repository raw file url mask
 * and the original file name + parent/original revision number
 *
 * @author rprent
 */
class RetrieveByCGIT extends AbstractOriginalFileRetriever implements OriginalFileRetrieverInterface
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

  /**
   * The retrieval method that the original file retriever supports
   *
   * @var string
   */
  protected $original_file_retrieval_method = 'cgit';

  public function __construct($raw_file_url_mask_1, $raw_file_url_mask_2)
  {
    $this->raw_file_url_mask_1 = $raw_file_url_mask_1;
    $this->raw_file_url_mask_2 = $raw_file_url_mask_2;
  }

  /**
   * Retrieves the original file of the given diff with CGIT
   *
   * @param DiffFile $diff_file
   * @param string $repository
   * $return string
   */
  public function retrieveOriginalFile(DiffFile $diff_file, $repository)
  {
    $original_file_url = $this->raw_file_url_mask_1
      . $repository
      . $this->raw_file_url_mask_2
      . '/'
      . $diff_file->getSource()
      . '?id2='
      . $diff_file->getSourceRevision()
    ;

    return file_get_contents($original_file_url);
  }
}
