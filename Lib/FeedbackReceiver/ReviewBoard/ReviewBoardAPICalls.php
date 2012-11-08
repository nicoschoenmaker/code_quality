<?php

namespace Hostnet\HostnetCodeQualityBundle\Lib\FeedbackReceiver\ReviewBoard;

use Hostnet\HostnetCodeQualityBundle\Entity\Review,
    Hostnet\HostnetCodeQualityBundle\Lib\FeedbackReceiver\AbstractFeedbackReceiver,
    Hostnet\HostnetCodeQualityBundle\Lib\FeedbackReceiver\ReviewBoard\ReviewBoardReview,
    Hostnet\HostnetCodeQualityBundle\Parser\OriginalFileRetriever\FeedbackReceiverInterface;

use InvalidArgumentException;

/**
 * The Review Board API calls that are
 * used to retrieve data from and push data
 * to Review Board
 *
 * @author rprent
 */
class ReviewBoardAPICalls extends AbstractFeedbackReceiver implements FeedbackReceiverInterface
{
  // Review Board url path parts
  const API_REVIEW_REQUEST = '/api/review-requests/';
  const API_REPOSITORIES = '/api/repositories/';
  const R = '/r/';
  const DIFFS = '/diffs/';
  const FILES = '/files/';
  const RAW_DIFF = '/diff/raw/';
  const REVIEWS = '/reviews/';
  const DIFF_COMMENTS = '/diff-comments/';
  const ORIGINAL_FILE = '/original-file/';
  const AFTER_FIRST_BACKSLASH_POS = 1;

  /**
   * If the review should contain an auto ship it
   * when there are no violations to be found
   *
   * @var boolean
   */
  private $auto_ship_it;

  public function __construct($base_url, $username, $password, $auto_ship_it)
  {
    $this->auto_ship_it = $auto_ship_it;

    parent::__construct($base_url, $username, $password);
  }

  /**
   * Retrieves the original file from Review Board
   * Note: Functionality added in the Review Board v1.7+ API!
   *
   * @param integer $review_request_id
   * @param string $source_file
   * @return mixed
   */
  public function retrieveOriginalFile($review_request_id, $source_file)
  {
    $files = $this->
      retrieveReviewRequestLastDiffFiles($review_request_id);
    foreach($files->files as $file) {
      if(substr($source_file, self::AFTER_FIRST_BACKSLASH_POS) == $file->source_file) {
        $original_file_url = $file->links->original_file->href;
      }
    }
    $headers = array(self::RESULT_TYPE_TEXT);

    return $this->executeCURLRequest($original_file_url, $headers);
  }

  /**
   * Retrieves all the pending review requests
   *
   * @return mixed
   */
  public function retrievePendingReviewRequests()
  {
    $review_requests_url = $this->base_url . self::API_REVIEW_REQUEST
      . '?status=pending&max-results=999999';
    $headers = array(self::RESULT_TYPE_JSON);

    return $this->decodeJSON($this->executeCURLRequest($review_requests_url, $headers));
  }

  /**
   * Retrieve the diff
   *
   * @param integer $review_request_id
   * @param integer $diff_revision
   * @param string $result_type
   * @return string
   */
  public function retrieveDiff($review_request_id, $diff_revision = null, $result_type = self::RESULT_TYPE_JSON)
  {
    // If the diff revision is supplied we use it, otherwise default to the last diff
    // Also check if we want the raw/text version or in json/other
    if(!empty($diff_revision)) {
      $this->validateDiffRevision($review_request_id, $diff_revision);
      $diff_url = $this->base_url . self::API_REVIEW_REQUEST
        . $review_request_id . self::DIFFS . $diff_revision . '/';
      $headers = array($result_type);

      return $this->executeCURLRequest($diff_url, $headers);
    }
    return $this->retrieveLatestDiff($review_request_id, $result_type);
  }

  /**
   * Retrieve the latest diff
   *
   * @param integer $review_request_id
   * @param string $result_type
   * @return mixed
   */
  public function retrieveLatestDiff($review_request_id, $result_type = self::RESULT_TYPE_JSON)
  {
    if($result_type == self::RESULT_TYPE_TEXT) {
      $diff_url = $this->base_url . self::R . $review_request_id . self::RAW_DIFF;
    } else {
      $last_diff_revision = $this->retrieveAmountOfDiffs($review_request_id);
      $diff_url = $this->base_url . self::API_REVIEW_REQUEST . $review_request_id
        . self::DIFFS . $last_diff_revision . '/';
    }
    $headers = array($result_type);

    return $this->executeCURLRequest($diff_url, $headers);
  }

  /**
   * Retrieve the diff files from the last diff of a review request
   *
   * @param integer $review_request_id
   * @return mixed
   */
  private function retrieveReviewRequestLastDiffFiles($review_request_id)
  {
    $last_diff_revision = $this->retrieveAmountOfDiffs($review_request_id);
    $diff_files_url = $this->base_url . self::API_REVIEW_REQUEST . $review_request_id
      . self::DIFFS . $last_diff_revision . self::FILES;

    return $this->decodeJSON($this->executeCURLRequest($diff_files_url));
  }

  /**
   * Retrieve the amount of diffs that a review request contains
   *
   * @param integer $review_request_id
   * @throws InvalidArgumentException
   * @return integer
   */
  private function retrieveAmountOfDiffs($review_request_id)
  {
    // Retrieve the list of diffs of the review request
    $diff_list_url = $this->base_url . self::API_REVIEW_REQUEST
      . $review_request_id . self::DIFFS;
    $amount_of_diffs = count($this->decodeJSON($this->executeCURLRequest($diff_list_url)));

    // If the length of the diff list is 0 it means that
    // the review request has no diffs so we throw an exception
    if(!$amount_of_diffs) {
      throw new InvalidArgumentException('No diffs found for the selected review request id ('
        . $review_request_id . '), are you sure that you selected the right review request?');
    }

    return $amount_of_diffs;
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

    $amount_of_diffs = $this->retrieveAmountOfDiffs($review_request_id);
    // If the supplied diff revision is bigger than the number of diffs we throw an exception
    if($diff_revision > $amount_of_diffs || $diff_revision == 0) {
      $to_be_text = $amount_of_diffs == 1 ? 'is ' : 'are ';
      $revision_text = $amount_of_diffs == 1 ? 'revision' : 'revisions';
      throw new InvalidArgumentException('The given diff revision does not exist, '
        . 'there ' . $to_be_text . $amount_of_diffs . ' diff ' . $revision_text
        .' so use a value from 1 to '. $amount_of_diffs
      );
    }
  }

  /**
   * Create a new review for the specified review request
   * and return the new review id
   *
   * @param integer $review_request_id
   * @param array $fields
   */
  private function createReview($review_request_id, $fields = array())
  {
    $reviews_url = $this->base_url . self::API_REVIEW_REQUEST
      . $review_request_id . self::REVIEWS;
    $new_review = $this->decodeJSON($this->executeCURLRequest(
      $reviews_url, array(), self::POST, $fields
    ));

    return $new_review->review->id;
  }

  /**
   * Create a comment for a draft review
   *
   * @param integer $review_request_id
   * @param integer $review_id
   * @param array $fields
   */
  private function createComment($review_request_id, $review_id, $fields = array())
  {
    $diff_comments_url = $this->base_url . self::API_REVIEW_REQUEST
      . $review_request_id . self::REVIEWS . $review_id . self::DIFF_COMMENTS;

    $this->executeCURLRequest($diff_comments_url, array(), self::POST, $fields);
  }

  /**
   * Send the feedback to Review Board
   *
   * @param integer $review_request_id
   * @param Review $review
   * @param integer $line_cap
   */
  public function sendFeedbackToRB($review_request_id, $review, $line_cap)
  {
    // Create a draft review
    $new_review_id = $this->createReview($review_request_id);
    $diff_files = $this->retrieveReviewRequestLastDiffFiles($review_request_id);

    $reports = $review->getReports();
    $violation_detected = false;
    $total_original_violations_amount = 0;
    $total_diff_violations_amount = 0;
    foreach($reports as $report) {
      $file = $report->getFile();
      $diff_violations = $report->getDiffViolations();
      $amount_of_diff_violations = count($report->getDiffViolations());
      // Count the total of original and diff violations to see if the diff had positive changes
      $total_original_violations_amount += count($report->getOriginalViolations());
      $total_diff_violations_amount += $amount_of_diff_violations;
      // Check if there are any violations
      if($amount_of_diff_violations > 0) {
        $violation_detected = true;
      }
      // Go through all the diff files retrieved from RB
      foreach($diff_files->files as $diff_file) {
        // If the reviewed file is the same as the RB diff file
        // we push all the violations for that file to RB
        $same_source = substr($file->getSource(), self::AFTER_FIRST_BACKSLASH_POS) == $diff_file->source_file;
        if(!$file->hasParent() || $same_source) {
          foreach($diff_violations as $violation) {
            // Set the number of lines to post the comment on,
            // if it exceeds the cap we just take the cap
            $number_of_lines = $violation->getEndLine() + 1 - $violation->getBeginLine();
            $number_of_lines = ($number_of_lines > $line_cap) ? $line_cap : $number_of_lines;
            $fields = array(
                'filediff_id'      => $diff_file->id,
                'first_line'       => $violation->getBeginLine(),
                'issue_opened'     => false,
                'num_lines'        => $number_of_lines,
                'text'             => $violation->getMessage()
            );
            // Post the comment onto the draft review
            $this->createComment($review_request_id, $new_review_id, $fields);
          }
        }
      }
    }

    // Make the review public and add the appropriate progression message
    $progression = $total_original_violations_amount - $total_diff_violations_amount;
    if($progression > 0) {
      $progression_text = 'better by removing ' . abs($progression) . ' old violations!';
    } else if($progression == 0) {
      $progression_text = 'stay the same!';
    } else {
      $progression_text = 'worse by adding ' . abs($progression) . ' new violations!';
    }
    $body_top = "Static Code Quality feedback:\n\n";
    $public = true;
    $rb_review = new ReviewBoardReview('', '', $public);
    if($violation_detected) {
      $rb_review->setBodyTop($body_top . "\tYour latest diff made the code quality "
        . $progression_text . "\n\tThe next messages are all the violations detected in "
        . 'all the files you modified with this diff.');
    } else {
      $rb_review->setBodyTop($body_top . "\tNo code quality violations found, good job!");
      $rb_review->setShipIt($this->auto_ship_it);
    }
    // Publish the review
    $this->createReview($review_request_id, $rb_review->toArray());
  }
}
