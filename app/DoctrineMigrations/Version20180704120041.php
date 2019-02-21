<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180704120041 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE media__gallery_media DROP FOREIGN KEY FK_80D4C5414E7AF8F');
        $this->addSql('ALTER TABLE media__gallery_media DROP FOREIGN KEY FK_80D4C541EA9FDD75');
        $this->addSql('ALTER TABLE media__gallery_media ADD CONSTRAINT FK_80D4C5414E7AF8F FOREIGN KEY (gallery_id) REFERENCES media__gallery (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE media__gallery_media ADD CONSTRAINT FK_80D4C541EA9FDD75 FOREIGN KEY (media_id) REFERENCES media__media (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE media__media ADD cdn_flush_identifier VARCHAR(64) DEFAULT NULL');
        $this->addSql('ALTER TABLE fos_user_user DROP locked, DROP expired, DROP expires_at, DROP credentials_expired, DROP credentials_expire_at, CHANGE username username VARCHAR(180) NOT NULL, CHANGE username_canonical username_canonical VARCHAR(180) NOT NULL, CHANGE email email VARCHAR(180) NOT NULL, CHANGE email_canonical email_canonical VARCHAR(180) NOT NULL, CHANGE salt salt VARCHAR(255) DEFAULT NULL, CHANGE confirmation_token confirmation_token VARCHAR(180) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C560D761C05FB297 ON fos_user_user (confirmation_token)');
        $this->addSql('ALTER TABLE ik_sharing_indexes_channels DROP FOREIGN KEY FK_6EB2E7B959C6E386');
        $this->addSql('DROP INDEX IDX_6EB2E7B959C6E386 ON ik_sharing_indexes_channels');
        $this->addSql('ALTER TABLE ik_sharing_indexes_channels DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE ik_sharing_indexes_channels CHANGE sharingindex_id sharing_index_id INT NOT NULL');
        $this->addSql('ALTER TABLE ik_sharing_indexes_channels ADD CONSTRAINT FK_6EB2E7B99440B54B FOREIGN KEY (sharing_index_id) REFERENCES ik_sharing_index (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_6EB2E7B99440B54B ON ik_sharing_indexes_channels (sharing_index_id)');
        $this->addSql('ALTER TABLE ik_sharing_indexes_channels ADD PRIMARY KEY (sharing_index_id, channel_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_C560D761C05FB297 ON fos_user_user');
        $this->addSql('ALTER TABLE fos_user_user ADD locked TINYINT(1) NOT NULL, ADD expired TINYINT(1) NOT NULL, ADD expires_at DATETIME DEFAULT NULL, ADD credentials_expired TINYINT(1) NOT NULL, ADD credentials_expire_at DATETIME DEFAULT NULL, CHANGE username username VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE username_canonical username_canonical VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE email email VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE email_canonical email_canonical VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE salt salt VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE confirmation_token confirmation_token VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE ik_sharing_indexes_channels DROP FOREIGN KEY FK_6EB2E7B99440B54B');
        $this->addSql('DROP INDEX IDX_6EB2E7B99440B54B ON ik_sharing_indexes_channels');
        $this->addSql('ALTER TABLE ik_sharing_indexes_channels DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE ik_sharing_indexes_channels CHANGE sharing_index_id sharingindex_id INT NOT NULL');
        $this->addSql('ALTER TABLE ik_sharing_indexes_channels ADD CONSTRAINT FK_6EB2E7B959C6E386 FOREIGN KEY (sharingindex_id) REFERENCES ik_sharing_index (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_6EB2E7B959C6E386 ON ik_sharing_indexes_channels (sharingindex_id)');
        $this->addSql('ALTER TABLE ik_sharing_indexes_channels ADD PRIMARY KEY (sharingindex_id, channel_id)');
        $this->addSql('ALTER TABLE media__gallery_media DROP FOREIGN KEY FK_80D4C5414E7AF8F');
        $this->addSql('ALTER TABLE media__gallery_media DROP FOREIGN KEY FK_80D4C541EA9FDD75');
        $this->addSql('ALTER TABLE media__gallery_media ADD CONSTRAINT FK_80D4C5414E7AF8F FOREIGN KEY (gallery_id) REFERENCES media__gallery (id)');
        $this->addSql('ALTER TABLE media__gallery_media ADD CONSTRAINT FK_80D4C541EA9FDD75 FOREIGN KEY (media_id) REFERENCES media__media (id)');
        $this->addSql('ALTER TABLE media__media DROP cdn_flush_identifier');
    }
}
