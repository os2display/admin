<?php
/**
 * @file
 * This file is a part of the Indholdskanalen MainBundle.
 */

namespace Indholdskanalen\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Indholdskanalen\MainBundle\Entity\ScreenTemplate;

/**
 * Class TemplateCommand.
 *
 * Load screen templates into the database base on the templates defined in the
 * parameters configuration file.
 *
 * @package Indholdskanalen\MainBundle\Command
 */
class TemplateCommand extends ContainerAwareCommand {
  /**
   * Configure the command
   */
  protected function configure() {
    $this->setName('ik:templates:load')
      ->setDescription('Load the templates from the disk into the database.');
  }

  /**
   * Executes the command
   *
   * @param \Symfony\Component\Console\Input\InputInterface $input
   *   Console inputs.
   * @param \Symfony\Component\Console\Output\OutputInterface $output
   *   Used to write output to the console.
   *
   * @return int|null|void
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $output->writeln('Loading templates to database... ');

    // Get the container.
    $container = $this->getContainer();

    // Load templates.
    $container->get('indholdskanalen.template_service')->loadTemplates();

    $output->writeln('Done!');
  }
}
