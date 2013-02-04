<?php

namespace Hostnet\HostnetCodeQualityBundle\Parser\OriginalFileRetriever;

use Hostnet\HostnetCodeQualityBundle\Parser\OriginalFileRetriever\OriginalFileRetrievalParams,
    Hostnet\HostnetCodeQualityBundle\Parser\OriginalFileRetriever\FeedbackReceiverInterface,
    Hostnet\HostnetCodeQualityBundle\Parser\OriginalFileRetriever\OriginalFileRetrieverInterface,
    Hostnet\HostnetCodeQualityBundle\Parser\OriginalFileRetriever\ReviewBoard\ReviewBoardOriginalFileRetrieverParams;

/**
 * The Review Board implementation of retrieving the original file
 * Retrieves the original file based on the supplied review request id
 *
 * @author rprent
 */
class RetrieveByReviewBoard implements OriginalFileRetrieverInterface
{
  /**
   * @var FeedbackReceiverInterface
   */
  private $feedback_receiver;

  /**
   * @param FeedbackReceiverInterface $feedback_receiver
   */
  public function __construct(FeedbackReceiverInterface $feedback_receiver)
  {
    $this->feedback_receiver = $feedback_receiver;
  }

  /**
   * Retrieves the original file of a diff
   *
   * @param OriginalFileRetrievalParams $original_file_retrieval_params
   * @see \Hostnet\HostnetCodeQualityBundle\Parser\OriginalFileRetriever\OriginalFileRetrieverInterface::retrieveOriginalFile()
   * @return mixed
   */
  public function retrieveOriginalFile(OriginalFileRetrievalParams $original_file_retrieval_params)
  {
    $review_request_id = $original_file_retrieval_params->getReviewRequestId();
    $source_file = $original_file_retrieval_params->getDiffFile()->getSource();

    return $this->feedback_receiver->
      retrieveOriginalFile($review_request_id, $source_file);
  }

  /**
   * Checks if the original file retriever supports the original
   * file retrieval params
   *
   * @param OriginalFileRetrievalParams $original_file_retrieval_params
   * @return boolean
   */
  public function supports(OriginalFileRetrievalParams $original_file_retrieval_params)
  {
    return $original_file_retrieval_params instanceof ReviewBoardOriginalFileRetrieverParams;
  }
}
