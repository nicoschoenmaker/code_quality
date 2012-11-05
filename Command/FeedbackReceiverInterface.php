<?php

namespace Hostnet\HostnetCodeQualityBundle\Command;

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
  // HTTP status codes
  const HTTP_STATUS_CODE_OK = 200;
  const HTTP_STATUS_CODE_CREATED = 201;
}
