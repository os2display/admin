<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180624083830 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE acl_classes (id INT UNSIGNED AUTO_INCREMENT NOT NULL, class_type VARCHAR(200) NOT NULL, UNIQUE INDEX UNIQ_69DD750638A36066 (class_type), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acl_security_identities (id INT UNSIGNED AUTO_INCREMENT NOT NULL, identifier VARCHAR(200) NOT NULL, username TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8835EE78772E836AF85E0677 (identifier, username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acl_object_identities (id INT UNSIGNED AUTO_INCREMENT NOT NULL, parent_object_identity_id INT UNSIGNED DEFAULT NULL, class_id INT UNSIGNED NOT NULL, object_identifier VARCHAR(100) NOT NULL, entries_inheriting TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_9407E5494B12AD6EA000B10 (object_identifier, class_id), INDEX IDX_9407E54977FA751A (parent_object_identity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acl_object_identity_ancestors (object_identity_id INT UNSIGNED NOT NULL, ancestor_id INT UNSIGNED NOT NULL, INDEX IDX_825DE2993D9AB4A6 (object_identity_id), INDEX IDX_825DE299C671CEA1 (ancestor_id), PRIMARY KEY(object_identity_id, ancestor_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acl_entries (id INT UNSIGNED AUTO_INCREMENT NOT NULL, class_id INT UNSIGNED NOT NULL, object_identity_id INT UNSIGNED DEFAULT NULL, security_identity_id INT UNSIGNED NOT NULL, field_name VARCHAR(50) DEFAULT NULL, ace_order SMALLINT UNSIGNED NOT NULL, mask INT NOT NULL, granting TINYINT(1) NOT NULL, granting_strategy VARCHAR(30) NOT NULL, audit_success TINYINT(1) NOT NULL, audit_failure TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_46C8B806EA000B103D9AB4A64DEF17BCE4289BF4 (class_id, object_identity_id, field_name, ace_order), INDEX IDX_46C8B806EA000B103D9AB4A6DF9183C9 (class_id, object_identity_id, security_identity_id), INDEX IDX_46C8B806EA000B10 (class_id), INDEX IDX_46C8B8063D9AB4A6 (object_identity_id), INDEX IDX_46C8B806DF9183C9 (security_identity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE acl_object_identities ADD CONSTRAINT FK_9407E54977FA751A FOREIGN KEY (parent_object_identity_id) REFERENCES acl_object_identities (id)');
        $this->addSql('ALTER TABLE acl_object_identity_ancestors ADD CONSTRAINT FK_825DE2993D9AB4A6 FOREIGN KEY (object_identity_id) REFERENCES acl_object_identities (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acl_object_identity_ancestors ADD CONSTRAINT FK_825DE299C671CEA1 FOREIGN KEY (ancestor_id) REFERENCES acl_object_identities (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acl_entries ADD CONSTRAINT FK_46C8B806EA000B10 FOREIGN KEY (class_id) REFERENCES acl_classes (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acl_entries ADD CONSTRAINT FK_46C8B8063D9AB4A6 FOREIGN KEY (object_identity_id) REFERENCES acl_object_identities (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acl_entries ADD CONSTRAINT FK_46C8B806DF9183C9 FOREIGN KEY (security_identity_id) REFERENCES acl_security_identities (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE media__gallery_media CHANGE gallery_id gallery_id INT DEFAULT NULL, CHANGE media_id media_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE media__media CHANGE provider_metadata provider_metadata LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE width width INT DEFAULT NULL, CHANGE height height INT DEFAULT NULL, CHANGE length length NUMERIC(10, 0) DEFAULT NULL, CHANGE content_type content_type VARCHAR(255) DEFAULT NULL, CHANGE content_size content_size INT DEFAULT NULL, CHANGE copyright copyright VARCHAR(255) DEFAULT NULL, CHANGE author_name author_name VARCHAR(255) DEFAULT NULL, CHANGE context context VARCHAR(64) DEFAULT NULL, CHANGE cdn_is_flushable cdn_is_flushable TINYINT(1) DEFAULT NULL, CHANGE cdn_flush_at cdn_flush_at DATETIME DEFAULT NULL, CHANGE cdn_status cdn_status INT DEFAULT NULL, CHANGE user user INT DEFAULT NULL, CHANGE media_type media_type VARCHAR(255) DEFAULT NULL, CHANGE cdn_flush_identifier cdn_flush_identifier VARCHAR(64) DEFAULT NULL');
        $this->addSql('ALTER TABLE jms_jobs CHANGE startedAt startedAt DATETIME DEFAULT NULL, CHANGE checkedAt checkedAt DATETIME DEFAULT NULL, CHANGE workerName workerName VARCHAR(50) DEFAULT NULL, CHANGE executeAfter executeAfter DATETIME DEFAULT NULL, CHANGE closedAt closedAt DATETIME DEFAULT NULL, CHANGE exitCode exitCode SMALLINT UNSIGNED DEFAULT NULL, CHANGE stackTrace stackTrace LONGBLOB DEFAULT NULL COMMENT \'(DC2Type:jms_job_safe_object)\', CHANGE runtime runtime SMALLINT UNSIGNED DEFAULT NULL, CHANGE memoryUsage memoryUsage INT UNSIGNED DEFAULT NULL, CHANGE memoryUsageReal memoryUsageReal INT UNSIGNED DEFAULT NULL, CHANGE originalJob_id originalJob_id BIGINT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE ik_channelslideorder CHANGE channel_id channel_id INT DEFAULT NULL, CHANGE slide_id slide_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ik_grouping CHANGE group_id group_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE fos_user_user CHANGE salt salt VARCHAR(255) DEFAULT NULL, CHANGE last_login last_login DATETIME DEFAULT NULL, CHANGE confirmation_token confirmation_token VARCHAR(180) DEFAULT NULL, CHANGE password_requested_at password_requested_at DATETIME DEFAULT NULL, CHANGE firstname firstname VARCHAR(255) DEFAULT NULL, CHANGE lastname lastname VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE ik_media_order CHANGE slide_id slide_id INT DEFAULT NULL, CHANGE media_id media_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ik_screen CHANGE template_id template_id VARCHAR(255) DEFAULT NULL, CHANGE user user INT DEFAULT NULL, CHANGE options options LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\'');
        $this->addSql('ALTER TABLE ik_channel CHANGE user user INT DEFAULT NULL, CHANGE unique_id unique_id VARCHAR(255) DEFAULT NULL, CHANGE last_push_hash last_push_hash VARCHAR(255) DEFAULT NULL, CHANGE last_push_screens last_push_screens LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\', CHANGE publish_from publish_from INT DEFAULT NULL, CHANGE publish_to publish_to INT DEFAULT NULL, CHANGE schedule_repeat schedule_repeat TINYINT(1) DEFAULT NULL, CHANGE schedule_repeat_from schedule_repeat_from INT DEFAULT NULL, CHANGE schedule_repeat_to schedule_repeat_to INT DEFAULT NULL, CHANGE schedule_repeat_days schedule_repeat_days LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\'');
        $this->addSql('ALTER TABLE ik_channel_screen_regions CHANGE screen_id screen_id INT DEFAULT NULL, CHANGE shared_channel_id shared_channel_id INT DEFAULT NULL, CHANGE channel_id channel_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ik_shared_channel CHANGE last_push_hash last_push_hash VARCHAR(255) DEFAULT NULL, CHANGE last_push_screens last_push_screens LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\', CHANGE last_push_time last_push_time INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ik_slide CHANGE logo_id logo_id INT DEFAULT NULL, CHANGE orientation orientation VARCHAR(255) DEFAULT NULL, CHANGE template template VARCHAR(255) DEFAULT NULL, CHANGE options options LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\', CHANGE user user INT DEFAULT NULL, CHANGE duration duration INT DEFAULT NULL, CHANGE schedule_from schedule_from INT DEFAULT NULL, CHANGE schedule_to schedule_to INT DEFAULT NULL, CHANGE published published TINYINT(1) DEFAULT NULL, CHANGE media_type media_type VARCHAR(255) DEFAULT NULL, CHANGE slide_type slide_type VARCHAR(255) DEFAULT NULL, CHANGE external_data external_data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\'');
        $this->addSql('ALTER TABLE ik_sharing_index CHANGE enabled enabled TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE ik_slide_templates CHANGE slide_type slide_type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE ik_user_group CHANGE role role VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE ik_campaign CHANGE user user INT DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE acl_entries DROP FOREIGN KEY FK_46C8B806EA000B10');
        $this->addSql('ALTER TABLE acl_entries DROP FOREIGN KEY FK_46C8B806DF9183C9');
        $this->addSql('ALTER TABLE acl_object_identities DROP FOREIGN KEY FK_9407E54977FA751A');
        $this->addSql('ALTER TABLE acl_object_identity_ancestors DROP FOREIGN KEY FK_825DE2993D9AB4A6');
        $this->addSql('ALTER TABLE acl_object_identity_ancestors DROP FOREIGN KEY FK_825DE299C671CEA1');
        $this->addSql('ALTER TABLE acl_entries DROP FOREIGN KEY FK_46C8B8063D9AB4A6');
        $this->addSql('DROP TABLE acl_classes');
        $this->addSql('DROP TABLE acl_security_identities');
        $this->addSql('DROP TABLE acl_object_identities');
        $this->addSql('DROP TABLE acl_object_identity_ancestors');
        $this->addSql('DROP TABLE acl_entries');
        $this->addSql('ALTER TABLE fos_user_user CHANGE salt salt VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE last_login last_login DATETIME DEFAULT \'NULL\', CHANGE confirmation_token confirmation_token VARCHAR(180) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE password_requested_at password_requested_at DATETIME DEFAULT \'NULL\', CHANGE firstname firstname VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE lastname lastname VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE ik_campaign CHANGE user user INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ik_channel CHANGE user user INT DEFAULT NULL, CHANGE unique_id unique_id VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE last_push_hash last_push_hash VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE last_push_screens last_push_screens LONGTEXT DEFAULT \'NULL\' COLLATE utf8_unicode_ci COMMENT \'(DC2Type:json_array)\', CHANGE publish_from publish_from INT DEFAULT NULL, CHANGE publish_to publish_to INT DEFAULT NULL, CHANGE schedule_repeat schedule_repeat TINYINT(1) DEFAULT \'NULL\', CHANGE schedule_repeat_from schedule_repeat_from INT DEFAULT NULL, CHANGE schedule_repeat_to schedule_repeat_to INT DEFAULT NULL, CHANGE schedule_repeat_days schedule_repeat_days LONGTEXT DEFAULT \'NULL\' COLLATE utf8_unicode_ci COMMENT \'(DC2Type:json_array)\'');
        $this->addSql('ALTER TABLE ik_channel_screen_regions CHANGE screen_id screen_id INT DEFAULT NULL, CHANGE channel_id channel_id INT DEFAULT NULL, CHANGE shared_channel_id shared_channel_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ik_channelslideorder CHANGE slide_id slide_id INT DEFAULT NULL, CHANGE channel_id channel_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ik_grouping CHANGE group_id group_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ik_media_order CHANGE slide_id slide_id INT DEFAULT NULL, CHANGE media_id media_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ik_screen CHANGE template_id template_id VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE user user INT DEFAULT NULL, CHANGE options options LONGTEXT DEFAULT \'NULL\' COLLATE utf8_unicode_ci COMMENT \'(DC2Type:json_array)\'');
        $this->addSql('ALTER TABLE ik_shared_channel CHANGE last_push_hash last_push_hash VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE last_push_screens last_push_screens LONGTEXT DEFAULT \'NULL\' COLLATE utf8_unicode_ci COMMENT \'(DC2Type:json_array)\', CHANGE last_push_time last_push_time INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ik_sharing_index CHANGE enabled enabled TINYINT(1) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE ik_slide CHANGE logo_id logo_id INT DEFAULT NULL, CHANGE orientation orientation VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE template template VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE options options LONGTEXT DEFAULT \'NULL\' COLLATE utf8_unicode_ci COMMENT \'(DC2Type:json_array)\', CHANGE user user INT DEFAULT NULL, CHANGE duration duration INT DEFAULT NULL, CHANGE schedule_from schedule_from INT DEFAULT NULL, CHANGE schedule_to schedule_to INT DEFAULT NULL, CHANGE published published TINYINT(1) DEFAULT \'NULL\', CHANGE media_type media_type VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE slide_type slide_type VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE external_data external_data LONGTEXT DEFAULT \'NULL\' COLLATE utf8_unicode_ci COMMENT \'(DC2Type:json_array)\'');
        $this->addSql('ALTER TABLE ik_slide_templates CHANGE slide_type slide_type VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE ik_user_group CHANGE role role VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE jms_jobs CHANGE startedAt startedAt DATETIME DEFAULT \'NULL\', CHANGE checkedAt checkedAt DATETIME DEFAULT \'NULL\', CHANGE workerName workerName VARCHAR(50) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE executeAfter executeAfter DATETIME DEFAULT \'NULL\', CHANGE closedAt closedAt DATETIME DEFAULT \'NULL\', CHANGE exitCode exitCode SMALLINT UNSIGNED DEFAULT NULL, CHANGE stackTrace stackTrace LONGBLOB DEFAULT \'NULL\' COMMENT \'(DC2Type:jms_job_safe_object)\', CHANGE runtime runtime SMALLINT UNSIGNED DEFAULT NULL, CHANGE memoryUsage memoryUsage INT UNSIGNED DEFAULT NULL, CHANGE memoryUsageReal memoryUsageReal INT UNSIGNED DEFAULT NULL, CHANGE originalJob_id originalJob_id BIGINT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE media__gallery_media CHANGE gallery_id gallery_id INT DEFAULT NULL, CHANGE media_id media_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE media__media CHANGE provider_metadata provider_metadata LONGTEXT DEFAULT \'NULL\' COLLATE utf8_unicode_ci COMMENT \'(DC2Type:json)\', CHANGE width width INT DEFAULT NULL, CHANGE height height INT DEFAULT NULL, CHANGE length length NUMERIC(10, 0) DEFAULT \'NULL\', CHANGE content_type content_type VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE content_size content_size INT DEFAULT NULL, CHANGE copyright copyright VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE author_name author_name VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE context context VARCHAR(64) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE cdn_is_flushable cdn_is_flushable TINYINT(1) DEFAULT \'NULL\', CHANGE cdn_flush_identifier cdn_flush_identifier VARCHAR(64) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE cdn_flush_at cdn_flush_at DATETIME DEFAULT \'NULL\', CHANGE cdn_status cdn_status INT DEFAULT NULL, CHANGE user user INT DEFAULT NULL, CHANGE media_type media_type VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci');
    }
}
