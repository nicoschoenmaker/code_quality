<?php

namespace Hostnet\Bundle\HostnetCodeQualityBundle\Parser\OriginalFileRetriever;

use Hostnet\Bundle\HostnetCodeQualityBundle\Parser\Diff\DiffFile;

/**
 *
 *
 * @author rprent
 */
class OriginalFileRetrievalParams
{
  /**
   * @var DiffFile
   */
  private $diff_file;

  /**
   * Get the DiffFile object
   *
   * @return DiffFile
   */
  public function getDiffFile()
  {
    return $this->diff_file;
  }

  /**
   * Set the DiffFile object
   *
   * @param DiffFile $diff_file
   */
  public function setDiffFile(DiffFile $diff_file)
  {
    $this->diff_file = $diff_file;
  }
}
