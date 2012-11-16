<?php

namespace Hostnet\HostnetCodeQualityBundle\Command\Configuration;

/**
 * The ReviewConfiguration is used to pass CLI args
 * to the Review Board post code in order to
 * configure how the comments should be placed.
 *
 * @author rprent
 */
class ReviewConfiguration
{
  /**
   * @var integer
   */
  private $review_request_id;

  /**
   * @var boolean
   */
  private $publish_empty;

  /**
   * @var integer
   */
  private $line_context;

  /**
   * @var integer
   */
  private $line_limit;

  public function __construct($review_request_id, $publish_empty, $line_context, $line_limit)
  {
    $this->review_request_id = $review_request_id;
    $this->publish_empty = $publish_empty;
    $this->line_context = $line_context;
    $this->line_limit = $line_limit;
  }

  /**
   * Gets the review request id
   *
   * @return integer
   */
  public function getReviewRequestId()
  {
    return $this->review_request_id;
  }

  /**
   * Gets the publish empty
   *
   * @return boolean
   */
  public function getPublishEmpty()
  {
    return $this->publish_empty;
  }

  /**
   * Gets the line context
   *
   * @return integer
   */
  public function getLineContext()
  {
    return $this->line_context;
  }

  /**
   * Gets the line limit
   *
   * @return integer
   */
  public function getLineLimit()
  {
    return $this->line_limit;
  }
}
