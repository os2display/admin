<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180704125152 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ik_campaign (id INT AUTO_INCREMENT NOT NULL, user INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, schedule_from DATETIME NOT NULL, schedule_to DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_BEC5A5C58D93D649 (user), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ik_campaign_channel (campaign_id INT NOT NULL, channel_id INT NOT NULL, INDEX IDX_1FB6E3D2F639F774 (campaign_id), INDEX IDX_1FB6E3D272F5A1AA (channel_id), PRIMARY KEY(campaign_id, channel_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ik_campaign_screen (campaign_id INT NOT NULL, screen_id INT NOT NULL, INDEX IDX_2DD3E8C0F639F774 (campaign_id), INDEX IDX_2DD3E8C041A67722 (screen_id), PRIMARY KEY(campaign_id, screen_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ik_campaign_group (campaign_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_9049FA96F639F774 (campaign_id), INDEX IDX_9049FA96FE54D947 (group_id), PRIMARY KEY(campaign_id, group_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ik_campaign ADD CONSTRAINT FK_BEC5A5C58D93D649 FOREIGN KEY (user) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE ik_campaign_channel ADD CONSTRAINT FK_1FB6E3D2F639F774 FOREIGN KEY (campaign_id) REFERENCES ik_campaign (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ik_campaign_channel ADD CONSTRAINT FK_1FB6E3D272F5A1AA FOREIGN KEY (channel_id) REFERENCES ik_channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ik_campaign_screen ADD CONSTRAINT FK_2DD3E8C0F639F774 FOREIGN KEY (campaign_id) REFERENCES ik_campaign (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ik_campaign_screen ADD CONSTRAINT FK_2DD3E8C041A67722 FOREIGN KEY (screen_id) REFERENCES ik_screen (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ik_campaign_group ADD CONSTRAINT FK_9049FA96F639F774 FOREIGN KEY (campaign_id) REFERENCES ik_campaign (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ik_campaign_group ADD CONSTRAINT FK_9049FA96FE54D947 FOREIGN KEY (group_id) REFERENCES ik_group (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ik_campaign_channel DROP FOREIGN KEY FK_1FB6E3D2F639F774');
        $this->addSql('ALTER TABLE ik_campaign_screen DROP FOREIGN KEY FK_2DD3E8C0F639F774');
        $this->addSql('ALTER TABLE ik_campaign_group DROP FOREIGN KEY FK_9049FA96F639F774');
        $this->addSql('DROP TABLE ik_campaign');
        $this->addSql('DROP TABLE ik_campaign_channel');
        $this->addSql('DROP TABLE ik_campaign_screen');
        $this->addSql('DROP TABLE ik_campaign_group');
    }
}
