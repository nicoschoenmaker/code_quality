<?php

namespace Hostnet\HostnetCodeQualityBundle\Lib;

use Doctrine\ORM\EntityManager;

use Hostnet\HostnetCodeQualityBundle\Entity\Review,
    Hostnet\HostnetCodeQualityBundle\Lib\CommandLineUtility,
    Hostnet\HostnetCodeQualityBundle\Parser\ParserFactory;

class ReviewProcessor
{
  private $clu;
  private $parser_factory;
  private $scm;
  private $raw_file_url_mask;

  public function __construct(CommandLineUtility $clu,
    ParserFactory $parser_factory, $scm, $raw_file_url_mask)
  {
    $this->clu = $clu;
    $this->parser_factory = $parser_factory;
    $this->scm = $scm;
    $this->raw_file_url_mask = $raw_file_url_mask;
  }

  public function processReview($diff, $register, EntityManager $em,
    $tools)
  {
    // Parse the diff into CodeFile objects
    $diff_parser = $this->parser_factory->getParserInstance($this->scm);
    $code_files = $diff_parser->parseDiff($diff);

    // Send each code file to their specific tool based on the extension
    $review = new Review();
    foreach($code_files as $code_file) {
      foreach($tools as $tool) {
        if($tool->supports($code_file)) {
          // cgit implementation:
          // Retrieve the original code file based on the repository raw file url mask
          // and the original file name + parent revision number
          // TODO Make more original file extraction implementations possible
          $original_file = file_get_contents($this->raw_file_url_mask);
          //$original_file = file_get_contents($this->raw_file_url_mask
          //  . $code_file->getSource()
          //  . '?id2='
          //  . $code_file->getSourceRevision());
          // If the file_get_contents fails it returns false on failure which is why we throw an exception
          if(!$original_file) {
            throw new \Exception("The file at '". $this->raw_file_url_mask . "' could not be found.");
          }

          // Let the appropriate tool process the file and return the output
          $tool_output = $tool->processFile($code_file, $original_file, $this->clu->getTempCodeQualityDirPath());
          // Generate the Tool Output Parser with a Factory and
          // Parse tool output into CodeQualityReview objects
          $additional_tool_properties = array('format' => $tool->getFormat());
          $tool_output_parser = $this->parser_factory->getParserInstance(
              $tool->getName(),
              $additional_tool_properties
          );
          $report = $tool_output_parser
            ->parseToolOutput($tool_output['diff_output'], $code_file);
          $review->getReports()->add($report);
          // Save the reviews if they should be registered
          if($register) {
            $em->persist($report);
            $em->flush();
          }
        }
      }
    }

    return $review;
  }
}
