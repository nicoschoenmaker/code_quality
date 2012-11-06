<?php

namespace Hostnet\HostnetCodeQualityBundle\Lib\FeedbackReceiver;

interface FeedbackReceiverInterface
{
  // CURL request methods
  const GET = 'get';
  const POST = 'post';
  // CURL headers
  const RESULT_TYPE_TEXT = 'Accept: text/plain';
  const RESULT_TYPE_XML = 'Accept: application/xml';
  const RESULT_TYPE_JSON = 'Accept: application/json';
  const BASIC_AUTH = 'Authorization: Basic ';

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
