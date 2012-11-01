<?php

namespace Hostnet\HostnetCodeQualityBundle\Command;

use JMS\SerializerBundle\Exception\XmlErrorException;

use DomDocument,
    Exception,
    InvalidArgumentException;

/**
 * The Review Board API calls that are
 * used to retrieve data from Review Board
 *
 * @author rprent
 */
class ReviewBoardAPICalls
{
  // Review Board url path parts
  const API_RR = '/api/review-requests/';
  const R = '/r/';
  const DIFFS = '/diffs/';
  const RAW_DIFF = '/diff/raw/';
  // CURL request methods
  const GET = 'get';
  const POST = 'post';
  // CURL headers
  const RESULT_TYPE_TEXT = 'Accept: text/plain';
  const RESULT_TYPE_XML = 'Accept: application/xml';
  const BASIC_AUTH = 'Authorization: Basic ';
  // XML tag names
  const ITEM_TAG_NAME = 'item';
  const ID_TAG_NAME = 'id';
  const DEST_FILE_TAG_NAME = 'dest_file';
  // Exit codes
  const EXIT_CODE_200 = 200;

  /**
   * @var string
   */
  private $domain;

  /**
   * @var string
   */
  private $login;

  public function __construct($domain, $username, $password)
  {
    $this->domain = $domain;
    $this->login = base64_encode($username . ':' . $password);
  }

  /**
   * Retrieve the diff
   *
   * @param integer $review_request_id
   * @param integer $diff_revision
   * @return string
   */
  public function retrieveDiff($review_request_id, $diff_revision = null)
  {
    // If the diff revision is supplied we use it, otherwise default to the last diff
    if(!empty($diff_revision)) {
      return $this->retrieveDiffByRevision($review_request_id, $diff_revision);
    } else {
      return $this->retrieveLastDiff($review_request_id);
    }
  }

  /**
   * Retrieve the review request diff by the given diff revision
   *
   * @param integer $review_request_id
   * @param integer $diff_revision
   * @return mixed
   */
  private function retrieveDiffByRevision($review_request_id, $diff_revision)
  {
    $this->validateDiffRevision($review_request_id, $diff_revision);

    $url = $this->domain . self::API_RR . $review_request_id . self::DIFFS . $diff_revision . '/';
    $headers = array(self::RESULT_TYPE_TEXT);

    return $this->executeCURLRequest($url, $headers);
  }

  /**
   * Retrieves the last diff of the given review request
   *
   * @param integer $review_request_id
   * @return mixed
   */
  private function retrieveLastDiff($review_request_id)
  {
    $diff_url = $this->domain . self::R . $review_request_id . self::RAW_DIFF;
    $headers = array(self::RESULT_TYPE_TEXT);

    return $this->executeCURLRequest($diff_url, $headers, false);
  }

  /**
   * Retrieve the diff list and return it
   *
   * @param integer $review_request_id
   * @throws XmlErrorException
   * @throws InvalidArgumentException
   */
  private function retrieveDiffList($review_request_id)
  {
    // Retrieve the list of diffs of the review request
    $diff_list_url = $this->domain . self::API_RR . $review_request_id . self::DIFFS;
    $headers = array(self::RESULT_TYPE_XML);
    $diff_list_in_xml = $this->executeCURLRequest($diff_list_url, $headers);

    $xml = new DomDocument();
    // Try to load the xml data, if it fails we throw an exception
    if(!$xml->loadXML($diff_list_in_xml)) {
      throw new XmlErrorException('Error while parsing XML, invalid XML supplied');
    }
    $diff_list = $xml->getElementsByTagName(self::ITEM_TAG_NAME);

    // If the length of the diff list is 0 it means that
    // the review request has no diffs so we throw an exception
    if(!$diff_list->length) {
      throw new InvalidArgumentException('No diffs found for the selected review request id, '
        . 'are you sure that you selected the right review request?');
    }

    return $diff_list;
  }

  /**
   * Validates if the diff revision exists
   *
   * @param integer $review_request_id
   * @param integer $diff_revision
   * @throws InvalidArgumentException
   */
  private function validateDiffRevision($review_request_id, $diff_revision)
  {
    // If the supplied diff revision is not numeric we throw an exception
    if(!is_numeric($diff_revision)) {
      throw new InvalidArgumentException('The diff revision option value has to contain a number!');
    }

    $diff_list = $this->retrieveDiffList($review_request_id);
    // If the supplied diff revision is bigger than the number of diffs we throw an exception
    $to_be_text = $diff_list->length == 1 ? 'is ' : 'are ';
    $revision_text = $diff_list->length == 1 ? 'revision' : 'revisions';
    if($diff_revision > $diff_list->length || $diff_revision == 0) {
      throw new InvalidArgumentException('The given diff revision does not exist, '
        . 'there ' . $to_be_text . $diff_list->length . ' diff ' . $revision_text
        .' so use a value from 1 to '. $diff_list->length
      );
    }
  }

  /**
   * Executes a curl request and returns the output
   *
   * @param string $url
   * @param string $headers
   * @param string $method
   * @param array $fields
   * @return mixed
   */
  private function executeCURLRequest($url, $headers = array(), $decode_json = true, $method = self::GET, $fields = array())
  {
    // Initialize the curl handler
    $ch = curl_init($url);
    // Set the curl options
    $full_http_header = array_merge(array(self::BASIC_AUTH . $this->login), $headers);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $full_http_header);
    if($method == self::POST) {
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
    }
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Execute the curl session
    $output = curl_exec($ch);
    // Check if the curl request returned a valid code
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if($code != self::EXIT_CODE_200) {
      throw new Exception('Wrong status code (' . $code . ') for ' . $url);
    }
    // Close the curl connection
    curl_close($ch);

    return $decode_json ? json_decode($output) : $output;
  }
}
