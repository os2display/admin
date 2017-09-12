<?php
/**
 * @file
 * This file is a part of the Os2Display CoreBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Os2Display\CoreBundle\Command;

use Os2Display\CoreBundle\Events\CronEvent;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CronCommand
 *
 * @package Os2Display\CoreBundle\Command
 */
class CronCommand extends ContainerAwareCommand {
  /**
   * Configure the command
   */
  protected function configure() {
    $this
      ->setName('os2:cron')
      ->setDescription('Cron');
  }

  /**
   * Executes the command
   *
   * @param InputInterface $input
   * @param OutputInterface $output
   * @return int|null|void
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    // Send CronEvent to trigger updates.
    $event = new CronEvent();
    $dispatcher = $this->getContainer()->get('event_dispatcher');
    $dispatcher->dispatch(CronEvent::EVENT_NAME, $event);

    $output->writeln('Cron done.');
  }
}
