<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser\OriginalFileRetriever;

use Hostnet\HostnetCodeQualityBundle\Parser\Diff\DiffFile;

/**
 * The OriginalFileRetriever interface
 *
 * @author rprent
 */
interface OriginalFileRetrieverInterface
{
  /**
   * Retrieves the original file of the given diff
   *
   * @param DiffFile $diff_file
   * @param string $repository
   */
  public function retrieveOriginalFile(DiffFile $diff_file, $repository);
}
