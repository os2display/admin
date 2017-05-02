<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Add interest_interval to options field of dokk1-coming-events slides.
 */
class Version20161205132100 extends AbstractMigration implements ContainerAwareInterface {
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
   * @param Schema $schema
   */
  public function up(Schema $schema) {
    // this up() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    // Find all instagram slides and remove them.
    $em = $this->container->get('doctrine')->getManager();
    $slides = $this->container->get('doctrine')
      ->getRepository('IndholdskanalenMainBundle:Slide')
      ->findBy(array('template' => 'dokk1-coming-events'));

    // Add field interest_interval to options.
    foreach ($slides as $slide) {
      $options = $slide->getOptions();

      $options['interest_interval'] = 7;

      $slide->setOptions($options);
    }

    $em->flush();
  }

  /**
   * @param Schema $schema
   */
  public function down(Schema $schema) {
    // this down() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');


  }
}
