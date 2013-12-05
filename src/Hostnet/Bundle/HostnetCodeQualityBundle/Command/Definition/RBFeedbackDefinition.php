<?php

namespace Hostnet\Bundle\HostnetCodeQualityBundle\Command\Definition;

use Symfony\Component\Console\Input\InputDefinition,
    Symfony\Component\Console\Input\InputOption;

/**
 * The RBFeedbackDefinition class is used to add multiple used
 * options to the command from one central spot which is this class.
 *
 * @author rprent
 */
class RBFeedbackDefinition extends InputDefinition
{
  /**
   * @param array $input_definitions
   */
  public function __construct(array $input_definitions = array())
  {
    array_push($input_definitions,
        new InputOption('publish_empty', 'p', InputOption::VALUE_REQUIRED,
          "Sends a comment if there are no violations to display. This can be used in combination with "
            . "the configurable auto_shipit setting to auto shipit if no violations found. "
            . "Defaults to false", false),
        new InputOption('line_context', 'c', InputOption::VALUE_REQUIRED,
          "The amount of lines width around the violated line that should be shown as 'context'.", 1),
        new InputOption('line_limit', 'l', InputOption::VALUE_REQUIRED,
          'The maximum number of lines per violation to be shown. Imagine a class with 2000 lines '
            . 'taking way too much space, therefore the default is at 5 lines.', 5)
    );

    parent::__construct($input_definitions);
  }
}
