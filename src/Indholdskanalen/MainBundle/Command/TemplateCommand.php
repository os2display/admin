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

    // Get database hooks.
    $doctrine = $container->get('doctrine');
    $templateRepository = $doctrine->getRepository('IndholdskanalenMainBundle:ScreenTemplate');
    $entityManager = $doctrine->getManager();

    // Get parameters.
    $enabledTemplates = $container->getParameter('templates_screens_enabled');
    $path = $container->get('kernel')
      ->getRootDir() . '/../web/' . $container->getParameter('templates_screens_directory');
    $serverAddress = $container->getParameter('absolute_path_to_server') . '/' . $container->getParameter('templates_screens_directory');

    // Loop over enable templates from the configuration.
    foreach($enabledTemplates as $entry) {
      // Read .json for template
      $str = file_get_contents($path . $entry . '/' . $entry . '.json');
      $obj = json_decode($str);

      // Try to load the template.
      $template = $templateRepository->findOneById($obj->id);

      // Check if the template was loaded, if not create a new template entity.
      if (!$template) {
        $template = new ScreenTemplate();
        $template->setId($obj->id);
        $template->setName($obj->name);
      }

      // Set the template values on the entity.
      $template->setPathIcon($serverAddress . $entry . '/' . $obj->icon);
      $template->setPathLive($serverAddress . $entry . '/' . $obj->paths->live);
      $template->setPathEdit($serverAddress . $entry . '/' . $obj->paths->edit);
      $template->setPathPreview($serverAddress . $entry . '/' . $obj->paths->preview);
      $template->setPathCss($serverAddress . $entry . '/' . $obj->paths->css);
      $template->setWidth($obj->idealdimensions->width);
      $template->setHeight($obj->idealdimensions->height);
      $template->setOrientation($obj->orientation);

      // Ensure that the entity is managed.
      $entityManager->persist($template);

      $output->writeln('Loaded template: ' . $obj->id);
    }

    // Make it stick in the database.
    $entityManager->flush();

    $output->writeln('Done!');
  }
}
