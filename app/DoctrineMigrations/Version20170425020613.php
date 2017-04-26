<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170425020613 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ik_group (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ik_user_group (id INT AUTO_INCREMENT NOT NULL, group_id INT NOT NULL, user_id INT NOT NULL, role VARCHAR(255) NOT NULL, INDEX IDX_581CA03BFE54D947 (group_id), INDEX IDX_581CA03BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ik_user_group ADD CONSTRAINT FK_581CA03BFE54D947 FOREIGN KEY (group_id) REFERENCES ik_group (id)');
        $this->addSql('ALTER TABLE ik_user_group ADD CONSTRAINT FK_581CA03BA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE fos_user_user CHANGE firstname firstname VARCHAR(255) DEFAULT NULL, CHANGE lastname lastname VARCHAR(255) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ik_user_group DROP FOREIGN KEY FK_581CA03BFE54D947');
        $this->addSql('DROP TABLE ik_group');
        $this->addSql('DROP TABLE ik_user_group');
        $this->addSql('ALTER TABLE fos_user_user CHANGE firstname firstname VARCHAR(64) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE lastname lastname VARCHAR(64) DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
