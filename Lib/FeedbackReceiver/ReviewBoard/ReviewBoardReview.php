<?php

namespace Hostnet\HostnetCodeQualityBundle\Lib\FeedbackReceiver\ReviewBoard;

/**
 * The Review Board Review object.
 * The properties are used to post a review
 * on Review Board.
 *
 * @author rprent
 */
class ReviewBoardReview
{
  /**
   * The review content below the comments.
   *
   * @var string
   */
  private $body_bottom;

  /**
   * The review content above the comments.
   *
   * @var string
   */
  private $body_top;

  /**
   * Whether or not to make the review public.
   * If a review is public, it cannot be made private again.
   *
   * @var boolean
   */
  private $public;

  /**
   * Whether or not to mark the review “Ship It!”
   *
   * @var boolean
   */
  private $ship_it;

  public function __construct($body_bottom = '', $body_top = '', $public = false, $ship_it = false)
  {
    $this->body_bottom = '';
    $this->body_top = $body_top;
    $this->public = $public;
    $this->ship_it = $ship_it;
  }

  /**
   * Set the bottom body
   *
   * @param string $body_bottom
   */
  public function setBodyBottom($body_bottom)
  {
    $this->body_bottom = $body_bottom;
  }

  /**
   * Set the top body
   *
   * @param string $body_top
   */
  public function setBodyTop($body_top)
  {
    $this->body_top = $body_top;
  }

  /**
   * Set if the review should be public
   *
   * @param boolean $public
   */
  public function setPublic($public)
  {
    $this->public = $public;
  }

  /**
   * Set if the review should contain a ship it
   *
   * @param boolean $ship_it
   */
  public function setShipIt($ship_it)
  {
    $this->ship_it = $ship_it;
  }

  /**
   * Returns the object as array
   *
   * @return array
   */
  public function toArray()
  {
    return array(
      'body_bottom' => $this->body_bottom,
      'body_top' => $this->body_top,
      'public' => $this->public,
      'ship_it' => $this->ship_it
    );
  }
}
