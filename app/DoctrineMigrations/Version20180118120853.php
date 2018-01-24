<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180118120853 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE ik_campaign_group');
        $this->addSql('ALTER TABLE ik_campaign ADD user VARCHAR(255) NOT NULL, DROP createdBy');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ik_campaign_group (campaign_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_9049FA96F639F774 (campaign_id), INDEX IDX_9049FA96FE54D947 (group_id), PRIMARY KEY(campaign_id, group_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ik_campaign_group ADD CONSTRAINT FK_9049FA96F639F774 FOREIGN KEY (campaign_id) REFERENCES ik_campaign (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ik_campaign_group ADD CONSTRAINT FK_9049FA96FE54D947 FOREIGN KEY (group_id) REFERENCES ik_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ik_campaign ADD createdBy VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, DROP user');
    }
}
