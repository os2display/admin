<?php
/**
 * @file
 * This file is a part of the Os2Display CoreBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Os2Display\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PushContentCommand
 *
 * @package Os2Display\CoreBundle\Command
 */
class PushContentCommand extends ContainerAwareCommand {
  /**
   * Configure the command
   */
  protected function configure() {
    $this
      ->setName('os2:push')
      ->setDescription('Push content to the screens')
      ->addOption(
        'force',
        NULL,
        InputOption::VALUE_NONE,
        'If set the push will be forced, even though the content has already been pushed.'
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
    $force = $input->getOption('force');

    $middlewareCommunication = $this->getContainer()
      ->get('os2display.middleware.communication');
    $middlewareCommunication->pushToScreens($force);

    $output->writeln('Content pushed to screens.');
  }
}
