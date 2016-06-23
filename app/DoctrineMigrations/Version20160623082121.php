<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Remove all instagram slides from the db.
 * Remove instagram template from db.
 */
class Version20160623082121 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // Delete all Instagram slide occurrences in the ik_channelslideorder table.
        $this->addSql('DELETE FROM ik_channelslideorder
                                WHERE slide_id IN (
                                      SELECT (id) FROM ik_slide WHERE slide_type=\'instagram\'
                                      )
                                                                
        ');

        // Delete all Instagram slides
        $this->addSql('DELETE FROM ik_slide WHERE slide_type=\'instagram\'');

        // Remove instagram template from db.
        $this->addSql('DELETE FROM ik_slide_templates WHERE id=\'dokk1-instagram\'');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
