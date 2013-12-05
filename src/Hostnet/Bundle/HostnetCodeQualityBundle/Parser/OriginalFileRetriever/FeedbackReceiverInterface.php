<?php

namespace Hostnet\Bundle\HostnetCodeQualityBundle\Parser\OriginalFileRetriever;

/**
 * The Feedback Receiver Interface
 * All the Feedback Receivers should implement this
 *
 * @author rprent
 */
interface FeedbackReceiverInterface
{
  // CURL result type headers
  const RESULT_TYPE_TEXT = 'Accept: text/plain';
  const RESULT_TYPE_JSON = 'Accept: application/json';

  /**
   * Retrieve the diff
   *
   * @param integer $review_request_id
   * @param integer $diff_revision
   * @param string $result_type
   * @return string
   */
  public function retrieveDiff($review_request_id, $diff_revision = null, $result_type = self::RESULT_TYPE_JSON);
}
