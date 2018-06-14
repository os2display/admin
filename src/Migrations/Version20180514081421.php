<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180514081421 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // Change username to user id. Default to user 1.
        $this->addSql('update ik_campaign set `user` = (CASE WHEN EXISTS (select 1 from fos_user_user where username = `user`) THEN (select id from fos_user_user where username = `user`) ELSE 1 END);');

        $this->addSql('ALTER TABLE ik_campaign DROP updatedBy, CHANGE user user INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ik_campaign ADD CONSTRAINT FK_BEC5A5C58D93D649 FOREIGN KEY (user) REFERENCES fos_user_user (id)');
        $this->addSql('CREATE INDEX IDX_BEC5A5C58D93D649 ON ik_campaign (user)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ik_campaign DROP FOREIGN KEY FK_BEC5A5C58D93D649');
        $this->addSql('DROP INDEX IDX_BEC5A5C58D93D649 ON ik_campaign');
        $this->addSql('ALTER TABLE ik_campaign ADD updatedBy VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE user user INT NOT NULL');

        // Change user id to username.
        $this->addSql('update ik_campaign set `user` = (select username from fos_user_user where id = `user`)');
    }
}
