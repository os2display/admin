<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170424185532 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fos_user_user_group DROP FOREIGN KEY FK_B3C77447FE54D947');
        $this->addSql('DROP TABLE fos_user_group');
        $this->addSql('DROP TABLE fos_user_user_group');
        $this->addSql('ALTER TABLE fos_user_user DROP created_at, DROP updated_at, DROP date_of_birth, DROP website, DROP biography, DROP gender, DROP locale, DROP timezone, DROP phone, DROP facebook_uid, DROP facebook_name, DROP facebook_data, DROP twitter_uid, DROP twitter_name, DROP twitter_data, DROP gplus_uid, DROP gplus_name, DROP gplus_data, DROP token, DROP two_step_code');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE fos_user_group (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, roles LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_583D1F3E5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fos_user_user_group (user_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_B3C77447A76ED395 (user_id), INDEX IDX_B3C77447FE54D947 (group_id), PRIMARY KEY(user_id, group_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fos_user_user_group ADD CONSTRAINT FK_B3C77447A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fos_user_user_group ADD CONSTRAINT FK_B3C77447FE54D947 FOREIGN KEY (group_id) REFERENCES fos_user_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fos_user_user ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL, ADD date_of_birth DATETIME DEFAULT NULL, ADD website VARCHAR(64) DEFAULT NULL COLLATE utf8_unicode_ci, ADD biography VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD gender VARCHAR(1) DEFAULT NULL COLLATE utf8_unicode_ci, ADD locale VARCHAR(8) DEFAULT NULL COLLATE utf8_unicode_ci, ADD timezone VARCHAR(64) DEFAULT NULL COLLATE utf8_unicode_ci, ADD phone VARCHAR(64) DEFAULT NULL COLLATE utf8_unicode_ci, ADD facebook_uid VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD facebook_name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD facebook_data LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:json)\', ADD twitter_uid VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD twitter_name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD twitter_data LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:json)\', ADD gplus_uid VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD gplus_name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD gplus_data LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:json)\', ADD token VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD two_step_code VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
