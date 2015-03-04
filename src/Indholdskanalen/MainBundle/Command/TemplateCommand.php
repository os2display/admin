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
use Indholdskanalen\MainBundle\Entity\ScreenTemplate;

/**
 * Class TemplateCommand
 *
 * @package Indholdskanalen\MainBundle\Command
 */
class TemplateCommand extends ContainerAwareCommand {
  /**
   * Configure the command
   */
  protected function configure() {
    $this
      ->setName('ik:templates:load')
      ->setDescription('Load the templates from the disk into the database.');
  }

  /**
   * Executes the command
   *
   * @param InputInterface $input
   * @param OutputInterface $output
   * @return int|null|void
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $output->write('Loading templates to database... ');

    // Get database hooks.
    $container = $this->getContainer();
    $doctrine = $container->get('doctrine');
    $templateRepository = $doctrine->getRepository('IndholdskanalenMainBundle:ScreenTemplate');
    $entityManager = $doctrine->getManager();

    // Get parameters.
    $enabledTemplates = $container->getParameter('templates_screens_enabled');
    $path = $container->get('kernel')
        ->getRootDir() . '/../web/' . $container->getParameter('templates_screens_directory');
    $serverAddress = $container->getParameter('absolute_path_to_server') . '/' . $container->getParameter('templates_screens_directory');

    // Iterate through templates directory (configurable).
    if ($handle = opendir($path)) {
      while (FALSE !== ($entry = readdir($handle))) {
        if (is_dir($path . '/' . $entry) && $entry !== '.' && $entry !== '..') {
          if (!in_array($entry, $enabledTemplates)) {
            continue;
          }

          // Read .json for template
          $str = file_get_contents($path . $entry . '/' . $entry . '.json');
          $obj = json_decode($str);

          $template = $templateRepository->findOneById($obj->id);

          if (!$template) {
            $template = new ScreenTemplate();
            $template->setId($obj->id);
            $template->setName($obj->name);
            $entityManager->persist($template);
          }

          $template->setPathIcon($serverAddress . $entry . '/' . $obj->icon);
          $template->setPathLive($serverAddress . $entry . '/' . $obj->paths->live);
          $template->setPathEdit($serverAddress . $entry . '/' . $obj->paths->edit);
          $template->setPathPreview($serverAddress . $entry . '/' . $obj->paths->preview);
          $template->setPathCss($serverAddress . $entry . '/' . $obj->paths->css);
          $template->setWidth($obj->idealdimensions->width);
          $template->setHeight($obj->idealdimensions->height);
          $template->setOrientation($obj->orientation);

          $entityManager->flush();
        }
      }

      closedir($handle);
    }

    $output->writeln('Done!');
  }
}