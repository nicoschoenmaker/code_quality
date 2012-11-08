<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser\OriginalFileRetriever\CGIT;

use Hostnet\HostnetCodeQualityBundle\Parser\OriginalFileRetriever\OriginalFileRetrievalParams;

/**
 * The CGIT original file retrieval parameters
 * These are used to retrieve an original file with the
 * CGIT raw file url
 *
 * @author rprent
 */
class CGITOriginalFileRetrieverParams extends OriginalFileRetrievalParams
{
  /**
   * @var string
   */
  private $repository;

  public function __construct($repository)
  {
    $this->repository = $repository;
  }

  /**
   * Get the repository
   *
   * @return string
   */
  public function getRepository()
  {
    return $this->repository;
  }
}
