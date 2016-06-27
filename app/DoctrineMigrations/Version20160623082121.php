<?php

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

  public function setContainer(ContainerInterface $container = NULL) {
    $this->container = $container;
  }

  /**
   * @param Schema $schema
   */
  public function up(Schema $schema) {
    // this up() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()
        ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $em = $this->container->get('doctrine')->getManager();

    $slides = $this->container->get('doctrine')
      ->getRepository('IndholdskanalenMainBundle:Slide')
      ->findBy(array('slideType' => 'instagram'));

    foreach ($slides as $slide) {
      $em->remove($slide);
    }

    $slideTemplate = $this->container->get('doctrine')
      ->getRepository('IndholdskanalenMainBundle:SlideTemplate')->find('dokk1-instagram');

    if ($slideTemplate) {
      $em->remove($slideTemplate);
    }

    $em->flush();
  }

  /**
   * @param Schema $schema
   */
  public function down(Schema $schema) {
    // this down() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()
        ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
  }
}
