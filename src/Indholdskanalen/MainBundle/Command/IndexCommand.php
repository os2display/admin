<?php
/**
 * @file
 * This file is a part of the Indholdskanalen MainBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indholdskanalen\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class IndexCommand
 *
 * @package Indholdskanalen\MainBundle\Command
 */
class IndexCommand extends ContainerAwareCommand {
  /**
   * Configure the command
   */
  protected function configure() {
    $this
      ->setName('ik:index')
      ->setDescription("Index")
      ->addArgument(
        'entity_id',
        InputArgument::REQUIRED,
        'Entity id?'
      )
      ->addArgument(
        'method',
        InputArgument::REQUIRED,
        'Method: POST, PUT, DELETE?'
      );
  }

  /**
   * Executes the command
   *
   * @param InputInterface $input
   * @param OutputInterface $output
   * @return int|null|void
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    // Update shared channels.
    if ($this->getContainer()->getParameter('sharing_enabled')) {
      $sharingService = $this->getContainer()->get('indholdskanalen.sharing_service');
      $sharingService->updateAllSharedChannels();
    }

    // Push content to screens.
    $middlewareCommunication = $this->getContainer()->get('indholdskanalen.middleware.communication');
    $middlewareCommunication->pushToScreens();

    $output->writeln("Content pushed to screens.");
  }
}