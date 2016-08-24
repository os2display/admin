<?php
/**
 * @file
 * Update database tables in relation to instagram.
 *
 * 1. Remove all instagram slides from the db.
 * 2. Remove instagram template from db.
 */

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Remove all instagram slides from the db.
 * Remove instagram template from db.
 */
class Version20160623082121 extends AbstractMigration implements ContainerAwareInterface {
  private $container;

  /**
   * Implements container aware interface.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface|NULL $container
   *    The container that is injected.
   */
  public function setContainer(ContainerInterface $container = NULL) {
    $this->container = $container;
  }

  /**
   * Up migration.
   *
   * @param Schema $schema
   */
  public function up(Schema $schema) {
    $this->abortIf($this->connection->getDatabasePlatform()
        ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    // Find all instagram slides and remove them.
    $em = $this->container->get('doctrine')->getManager();
    $slides = $this->container->get('doctrine')
      ->getRepository('IndholdskanalenMainBundle:Slide')
      ->findBy(array('slideType' => 'instagram'));

    foreach ($slides as $slide) {
      // This will automatically update the channels and all other places this
      // slide is used.
      $em->remove($slide);
    }

    // Remove all usage of the instagram templates.
    $slideTemplate = $this->container->get('doctrine')
      ->getRepository('IndholdskanalenMainBundle:SlideTemplate')->find('dokk1-instagram');

    if ($slideTemplate) {
      $em->remove($slideTemplate);
    }

    $em->flush();
  }

  /**
   * Down migration.
   *
   * @param Schema $schema
   */
  public function down(Schema $schema) {
    $this->abortIf($this->connection->getDatabasePlatform()
        ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
  }
}
