<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser\OriginalFileRetriever;

use Hostnet\HostnetCodeQualityBundle\Parser\Diff\DiffFile;

/**
 *
 *
 * @author rprent
 */
abstract class AbstractOriginalFileRetrievalParams
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
