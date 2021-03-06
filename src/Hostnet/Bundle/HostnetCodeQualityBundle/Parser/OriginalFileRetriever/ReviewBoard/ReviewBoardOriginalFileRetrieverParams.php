<?php

namespace Hostnet\Bundle\HostnetCodeQualityBundle\Parser\OriginalFileRetriever\ReviewBoard;

use Hostnet\Bundle\HostnetCodeQualityBundle\Parser\OriginalFileRetriever\OriginalFileRetrievalParams;

/**
 * The Review Board original file retrieval parameters
 * These are used to retrieve an original file from Review Board
 *
 * @author rprent
 */
class ReviewBoardOriginalFileRetrieverParams extends OriginalFileRetrievalParams
{
  /**
   * @var integer
   */
  private $review_request_id;

  /**
   * @param integer $review_request_id
   */
  public function __construct($review_request_id)
  {
    $this->review_request_id = $review_request_id;
  }

  /**
   * Get the review request id
   *
   * @return integer
   */
  public function getReviewRequestId()
  {
    return $this->review_request_id;
  }
}
