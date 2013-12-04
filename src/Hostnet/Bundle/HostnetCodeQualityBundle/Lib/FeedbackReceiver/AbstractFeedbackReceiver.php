<?php

namespace Hostnet\HostnetCodeQualityBundle\Lib\FeedbackReceiver;

use RuntimeException,
    InvalidArgumentException;

/**
 * An Abstract class for all the Feedback Receivers
 *
 * @author rprent
 */
abstract class AbstractFeedbackReceiver
{
  // CURL authorization header
  const BASIC_AUTH = 'Authorization: Basic ';
  // CURL request methods
  const GET = 'get';
  const POST = 'post';

  /**
   * HTTP status codes
   *
   * @var array
   */
  protected static $supported_http_status_codes = array(200, 201);

  /**
   * @var string
   */
  protected $base_url;

  /**
   * @var string
   */
  private $login;

  /**
   * @param string $base_url
   * @param string $username
   * @param string $password
   */
  public function __construct($base_url, $username, $password)
  {
    $this->base_url = $base_url;
    $this->login = base64_encode($username . ':' . $password);
  }

  /**
   * Executes a curl request and returns the output
   *
   * @param string $url
   * @param array $headers
   * @param string $method
   * @param array $fields
   * @return mixed
   */
  final protected function executeCURLRequest($url, $headers = array(),
    $method = self::GET, $fields = array())
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
    // Close the curl connection
    curl_close($ch);

    if(!in_array($code, self::$supported_http_status_codes)) {
      throw new RuntimeException('Wrong status code (' . $code . ') for ' . $url);
    }

    return $output;
  }

  /**
   * Decodes JSON and checks for errors
   *
   * @param string $string
   */
  final protected function decodeJSON($string)
  {
    $result = json_decode($string);
    if(json_last_error() != JSON_ERROR_NONE) {
      throw new InvalidArgumentException("Decoding the string '" . $string
        . "'with JSON failed and returned error code '" . json_last_error() . "'");
    }

    return $result;
  }
}
