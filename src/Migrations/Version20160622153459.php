<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160622153459 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE fos_user_group (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_583D1F3E5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fos_user_user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, username_canonical VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, email_canonical VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, locked TINYINT(1) NOT NULL, expired TINYINT(1) NOT NULL, expires_at DATETIME DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', credentials_expired TINYINT(1) NOT NULL, credentials_expire_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, date_of_birth DATETIME DEFAULT NULL, firstname VARCHAR(64) DEFAULT NULL, lastname VARCHAR(64) DEFAULT NULL, website VARCHAR(64) DEFAULT NULL, biography VARCHAR(1000) DEFAULT NULL, gender VARCHAR(1) DEFAULT NULL, locale VARCHAR(8) DEFAULT NULL, timezone VARCHAR(64) DEFAULT NULL, phone VARCHAR(64) DEFAULT NULL, facebook_uid VARCHAR(255) DEFAULT NULL, facebook_name VARCHAR(255) DEFAULT NULL, facebook_data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', twitter_uid VARCHAR(255) DEFAULT NULL, twitter_name VARCHAR(255) DEFAULT NULL, twitter_data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', gplus_uid VARCHAR(255) DEFAULT NULL, gplus_name VARCHAR(255) DEFAULT NULL, gplus_data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', token VARCHAR(255) DEFAULT NULL, two_step_code VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_C560D76192FC23A8 (username_canonical), UNIQUE INDEX UNIQ_C560D761A0D96FBF (email_canonical), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fos_user_user_group (user_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_B3C77447A76ED395 (user_id), INDEX IDX_B3C77447FE54D947 (group_id), PRIMARY KEY(user_id, group_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media__gallery (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, context VARCHAR(64) NOT NULL, default_format VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media__gallery_media (id INT AUTO_INCREMENT NOT NULL, gallery_id INT DEFAULT NULL, media_id INT DEFAULT NULL, position INT NOT NULL, enabled TINYINT(1) NOT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_80D4C5414E7AF8F (gallery_id), INDEX IDX_80D4C541EA9FDD75 (media_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media__media (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, enabled TINYINT(1) NOT NULL, provider_name VARCHAR(255) NOT NULL, provider_status INT NOT NULL, provider_reference VARCHAR(255) NOT NULL, provider_metadata LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', width INT DEFAULT NULL, height INT DEFAULT NULL, length NUMERIC(10, 0) DEFAULT NULL, content_type VARCHAR(255) DEFAULT NULL, content_size INT DEFAULT NULL, copyright VARCHAR(255) DEFAULT NULL, author_name VARCHAR(255) DEFAULT NULL, context VARCHAR(64) DEFAULT NULL, cdn_is_flushable TINYINT(1) DEFAULT NULL, cdn_flush_at DATETIME DEFAULT NULL, cdn_status INT DEFAULT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, user INT DEFAULT NULL, media_type VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ik_channel (id INT AUTO_INCREMENT NOT NULL, title LONGTEXT NOT NULL, created_at INT NOT NULL, user INT DEFAULT NULL, modified_at INT NOT NULL, unique_id VARCHAR(255) DEFAULT NULL, last_push_hash VARCHAR(255) DEFAULT NULL, last_push_screens LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\', publish_from INT DEFAULT NULL, publish_to INT DEFAULT NULL, schedule_repeat TINYINT(1) DEFAULT NULL, schedule_repeat_from INT DEFAULT NULL, schedule_repeat_to INT DEFAULT NULL, schedule_repeat_days LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ik_channel_screen_regions (id INT AUTO_INCREMENT NOT NULL, screen_id INT DEFAULT NULL, channel_id INT DEFAULT NULL, shared_channel_id INT DEFAULT NULL, sort_order INT NOT NULL, region INT NOT NULL, INDEX IDX_2275F81541A67722 (screen_id), INDEX IDX_2275F81572F5A1AA (channel_id), INDEX IDX_2275F8156E64165 (shared_channel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ik_channelslideorder (id INT AUTO_INCREMENT NOT NULL, slide_id INT DEFAULT NULL, channel_id INT DEFAULT NULL, sort_order INT NOT NULL, INDEX IDX_CF85F9ACDD5AFB87 (slide_id), INDEX IDX_CF85F9AC72F5A1AA (channel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ik_media_order (id INT AUTO_INCREMENT NOT NULL, slide_id INT DEFAULT NULL, media_id INT DEFAULT NULL, sort_order INT NOT NULL, INDEX IDX_D5524FEBDD5AFB87 (slide_id), INDEX IDX_D5524FEBEA9FDD75 (media_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ik_screen (id INT AUTO_INCREMENT NOT NULL, template_id VARCHAR(255) DEFAULT NULL, title LONGTEXT NOT NULL, created_at INT NOT NULL, token LONGTEXT NOT NULL, activation_code INT NOT NULL, user INT DEFAULT NULL, modified_at INT NOT NULL, description LONGTEXT NOT NULL, options LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\', INDEX IDX_205BC6715DA0FB8 (template_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ik_screen_templates (id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, path_icon VARCHAR(255) NOT NULL, path_live VARCHAR(255) NOT NULL, path_edit VARCHAR(255) NOT NULL, path_css VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, tools VARCHAR(255) NOT NULL, orientation VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ik_shared_channel (id INT AUTO_INCREMENT NOT NULL, unique_id LONGTEXT NOT NULL, `index` LONGTEXT NOT NULL, created_at INT NOT NULL, modified_at INT NOT NULL, content LONGTEXT DEFAULT NULL, last_push_hash VARCHAR(255) DEFAULT NULL, last_push_screens LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\', last_push_time INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ik_sharing_index (id INT AUTO_INCREMENT NOT NULL, name LONGTEXT NOT NULL, `index` LONGTEXT NOT NULL, enabled TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ik_sharing_indexes_channels (sharingindex_id INT NOT NULL, channel_id INT NOT NULL, INDEX IDX_6EB2E7B959C6E386 (sharingindex_id), INDEX IDX_6EB2E7B972F5A1AA (channel_id), PRIMARY KEY(sharingindex_id, channel_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ik_slide (id INT AUTO_INCREMENT NOT NULL, logo_id INT DEFAULT NULL, title LONGTEXT NOT NULL, orientation VARCHAR(255) DEFAULT NULL, template VARCHAR(255) DEFAULT NULL, created_at INT NOT NULL, options LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\', user INT DEFAULT NULL, duration INT DEFAULT NULL, schedule_from INT DEFAULT NULL, schedule_to INT DEFAULT NULL, published TINYINT(1) DEFAULT NULL, media_type VARCHAR(255) DEFAULT NULL, modified_at INT NOT NULL, slide_type VARCHAR(255) DEFAULT NULL, external_data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\', interest_period VARCHAR(255) DEFAULT NULL, INDEX IDX_6A22DFBBF98F144A (logo_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ik_slide_templates (id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, path_icon VARCHAR(255) NOT NULL, path_preview VARCHAR(255) NOT NULL, path_live VARCHAR(255) NOT NULL, path_edit VARCHAR(255) NOT NULL, path_css VARCHAR(255) NOT NULL, path_js VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, script_id VARCHAR(255) NOT NULL, orientation VARCHAR(255) NOT NULL, ideal_dimensions LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', media_type VARCHAR(255) NOT NULL, slide_type VARCHAR(255) DEFAULT NULL, empty_options LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', tools LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE jms_cron_jobs (id INT UNSIGNED AUTO_INCREMENT NOT NULL, command VARCHAR(200) NOT NULL, lastRunAt DATETIME NOT NULL, UNIQUE INDEX UNIQ_55F5ED428ECAEAD4 (command), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE jms_jobs (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, state VARCHAR(15) NOT NULL, queue VARCHAR(50) NOT NULL, priority SMALLINT NOT NULL, createdAt DATETIME NOT NULL, startedAt DATETIME DEFAULT NULL, checkedAt DATETIME DEFAULT NULL, workerName VARCHAR(50) DEFAULT NULL, executeAfter DATETIME DEFAULT NULL, closedAt DATETIME DEFAULT NULL, command VARCHAR(255) NOT NULL, args LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', output LONGTEXT DEFAULT NULL, errorOutput LONGTEXT DEFAULT NULL, exitCode SMALLINT UNSIGNED DEFAULT NULL, maxRuntime SMALLINT UNSIGNED NOT NULL, maxRetries SMALLINT UNSIGNED NOT NULL, stackTrace LONGBLOB DEFAULT NULL COMMENT \'(DC2Type:jms_job_safe_object)\', runtime SMALLINT UNSIGNED DEFAULT NULL, memoryUsage INT UNSIGNED DEFAULT NULL, memoryUsageReal INT UNSIGNED DEFAULT NULL, originalJob_id BIGINT UNSIGNED DEFAULT NULL, INDEX IDX_704ADB9349C447F1 (originalJob_id), INDEX cmd_search_index (command), INDEX sorting_index (state, priority, id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE jms_job_dependencies (source_job_id BIGINT UNSIGNED NOT NULL, dest_job_id BIGINT UNSIGNED NOT NULL, INDEX IDX_8DCFE92CBD1F6B4F (source_job_id), INDEX IDX_8DCFE92C32CF8D4C (dest_job_id), PRIMARY KEY(source_job_id, dest_job_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE jms_job_related_entities (job_id BIGINT UNSIGNED NOT NULL, related_class VARCHAR(150) NOT NULL, related_id VARCHAR(100) NOT NULL, INDEX IDX_E956F4E2BE04EA9 (job_id), PRIMARY KEY(job_id, related_class, related_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acl_classes (id INT UNSIGNED AUTO_INCREMENT NOT NULL, class_type VARCHAR(200) NOT NULL, UNIQUE INDEX UNIQ_69DD750638A36066 (class_type), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acl_security_identities (id INT UNSIGNED AUTO_INCREMENT NOT NULL, identifier VARCHAR(200) NOT NULL, username TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8835EE78772E836AF85E0677 (identifier, username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acl_object_identities (id INT UNSIGNED AUTO_INCREMENT NOT NULL, parent_object_identity_id INT UNSIGNED DEFAULT NULL, class_id INT UNSIGNED NOT NULL, object_identifier VARCHAR(100) NOT NULL, entries_inheriting TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_9407E5494B12AD6EA000B10 (object_identifier, class_id), INDEX IDX_9407E54977FA751A (parent_object_identity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acl_object_identity_ancestors (object_identity_id INT UNSIGNED NOT NULL, ancestor_id INT UNSIGNED NOT NULL, INDEX IDX_825DE2993D9AB4A6 (object_identity_id), INDEX IDX_825DE299C671CEA1 (ancestor_id), PRIMARY KEY(object_identity_id, ancestor_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acl_entries (id INT UNSIGNED AUTO_INCREMENT NOT NULL, class_id INT UNSIGNED NOT NULL, object_identity_id INT UNSIGNED DEFAULT NULL, security_identity_id INT UNSIGNED NOT NULL, field_name VARCHAR(50) DEFAULT NULL, ace_order SMALLINT UNSIGNED NOT NULL, mask INT NOT NULL, granting TINYINT(1) NOT NULL, granting_strategy VARCHAR(30) NOT NULL, audit_success TINYINT(1) NOT NULL, audit_failure TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_46C8B806EA000B103D9AB4A64DEF17BCE4289BF4 (class_id, object_identity_id, field_name, ace_order), INDEX IDX_46C8B806EA000B103D9AB4A6DF9183C9 (class_id, object_identity_id, security_identity_id), INDEX IDX_46C8B806EA000B10 (class_id), INDEX IDX_46C8B8063D9AB4A6 (object_identity_id), INDEX IDX_46C8B806DF9183C9 (security_identity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE jms_job_statistics (job_id BIGINT UNSIGNED NOT NULL, characteristic VARCHAR(30) NOT NULL, createdAt DATETIME NOT NULL, charValue DOUBLE PRECISION NOT NULL, PRIMARY KEY(job_id, characteristic, createdAt)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fos_user_user_group ADD CONSTRAINT FK_B3C77447A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fos_user_user_group ADD CONSTRAINT FK_B3C77447FE54D947 FOREIGN KEY (group_id) REFERENCES fos_user_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE media__gallery_media ADD CONSTRAINT FK_80D4C5414E7AF8F FOREIGN KEY (gallery_id) REFERENCES media__gallery (id)');
        $this->addSql('ALTER TABLE media__gallery_media ADD CONSTRAINT FK_80D4C541EA9FDD75 FOREIGN KEY (media_id) REFERENCES media__media (id)');
        $this->addSql('ALTER TABLE ik_channel_screen_regions ADD CONSTRAINT FK_2275F81541A67722 FOREIGN KEY (screen_id) REFERENCES ik_screen (id)');
        $this->addSql('ALTER TABLE ik_channel_screen_regions ADD CONSTRAINT FK_2275F81572F5A1AA FOREIGN KEY (channel_id) REFERENCES ik_channel (id)');
        $this->addSql('ALTER TABLE ik_channel_screen_regions ADD CONSTRAINT FK_2275F8156E64165 FOREIGN KEY (shared_channel_id) REFERENCES ik_shared_channel (id)');
        $this->addSql('ALTER TABLE ik_channelslideorder ADD CONSTRAINT FK_CF85F9ACDD5AFB87 FOREIGN KEY (slide_id) REFERENCES ik_slide (id)');
        $this->addSql('ALTER TABLE ik_channelslideorder ADD CONSTRAINT FK_CF85F9AC72F5A1AA FOREIGN KEY (channel_id) REFERENCES ik_channel (id)');
        $this->addSql('ALTER TABLE ik_media_order ADD CONSTRAINT FK_D5524FEBDD5AFB87 FOREIGN KEY (slide_id) REFERENCES ik_slide (id)');
        $this->addSql('ALTER TABLE ik_media_order ADD CONSTRAINT FK_D5524FEBEA9FDD75 FOREIGN KEY (media_id) REFERENCES media__media (id)');
        $this->addSql('ALTER TABLE ik_screen ADD CONSTRAINT FK_205BC6715DA0FB8 FOREIGN KEY (template_id) REFERENCES ik_screen_templates (id)');
        $this->addSql('ALTER TABLE ik_sharing_indexes_channels ADD CONSTRAINT FK_6EB2E7B959C6E386 FOREIGN KEY (sharingindex_id) REFERENCES ik_sharing_index (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ik_sharing_indexes_channels ADD CONSTRAINT FK_6EB2E7B972F5A1AA FOREIGN KEY (channel_id) REFERENCES ik_channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ik_slide ADD CONSTRAINT FK_6A22DFBBF98F144A FOREIGN KEY (logo_id) REFERENCES media__media (id)');
        $this->addSql('ALTER TABLE jms_jobs ADD CONSTRAINT FK_704ADB9349C447F1 FOREIGN KEY (originalJob_id) REFERENCES jms_jobs (id)');
        $this->addSql('ALTER TABLE jms_job_dependencies ADD CONSTRAINT FK_8DCFE92CBD1F6B4F FOREIGN KEY (source_job_id) REFERENCES jms_jobs (id)');
        $this->addSql('ALTER TABLE jms_job_dependencies ADD CONSTRAINT FK_8DCFE92C32CF8D4C FOREIGN KEY (dest_job_id) REFERENCES jms_jobs (id)');
        $this->addSql('ALTER TABLE jms_job_related_entities ADD CONSTRAINT FK_E956F4E2BE04EA9 FOREIGN KEY (job_id) REFERENCES jms_jobs (id)');
        $this->addSql('ALTER TABLE acl_object_identities ADD CONSTRAINT FK_9407E54977FA751A FOREIGN KEY (parent_object_identity_id) REFERENCES acl_object_identities (id)');
        $this->addSql('ALTER TABLE acl_object_identity_ancestors ADD CONSTRAINT FK_825DE2993D9AB4A6 FOREIGN KEY (object_identity_id) REFERENCES acl_object_identities (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acl_object_identity_ancestors ADD CONSTRAINT FK_825DE299C671CEA1 FOREIGN KEY (ancestor_id) REFERENCES acl_object_identities (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acl_entries ADD CONSTRAINT FK_46C8B806EA000B10 FOREIGN KEY (class_id) REFERENCES acl_classes (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acl_entries ADD CONSTRAINT FK_46C8B8063D9AB4A6 FOREIGN KEY (object_identity_id) REFERENCES acl_object_identities (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acl_entries ADD CONSTRAINT FK_46C8B806DF9183C9 FOREIGN KEY (security_identity_id) REFERENCES acl_security_identities (id) ON UPDATE CASCADE ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fos_user_user_group DROP FOREIGN KEY FK_B3C77447FE54D947');
        $this->addSql('ALTER TABLE fos_user_user_group DROP FOREIGN KEY FK_B3C77447A76ED395');
        $this->addSql('ALTER TABLE media__gallery_media DROP FOREIGN KEY FK_80D4C5414E7AF8F');
        $this->addSql('ALTER TABLE media__gallery_media DROP FOREIGN KEY FK_80D4C541EA9FDD75');
        $this->addSql('ALTER TABLE ik_media_order DROP FOREIGN KEY FK_D5524FEBEA9FDD75');
        $this->addSql('ALTER TABLE ik_slide DROP FOREIGN KEY FK_6A22DFBBF98F144A');
        $this->addSql('ALTER TABLE ik_channel_screen_regions DROP FOREIGN KEY FK_2275F81572F5A1AA');
        $this->addSql('ALTER TABLE ik_channelslideorder DROP FOREIGN KEY FK_CF85F9AC72F5A1AA');
        $this->addSql('ALTER TABLE ik_sharing_indexes_channels DROP FOREIGN KEY FK_6EB2E7B972F5A1AA');
        $this->addSql('ALTER TABLE ik_channel_screen_regions DROP FOREIGN KEY FK_2275F81541A67722');
        $this->addSql('ALTER TABLE ik_screen DROP FOREIGN KEY FK_205BC6715DA0FB8');
        $this->addSql('ALTER TABLE ik_channel_screen_regions DROP FOREIGN KEY FK_2275F8156E64165');
        $this->addSql('ALTER TABLE ik_sharing_indexes_channels DROP FOREIGN KEY FK_6EB2E7B959C6E386');
        $this->addSql('ALTER TABLE ik_channelslideorder DROP FOREIGN KEY FK_CF85F9ACDD5AFB87');
        $this->addSql('ALTER TABLE ik_media_order DROP FOREIGN KEY FK_D5524FEBDD5AFB87');
        $this->addSql('ALTER TABLE jms_jobs DROP FOREIGN KEY FK_704ADB9349C447F1');
        $this->addSql('ALTER TABLE jms_job_dependencies DROP FOREIGN KEY FK_8DCFE92CBD1F6B4F');
        $this->addSql('ALTER TABLE jms_job_dependencies DROP FOREIGN KEY FK_8DCFE92C32CF8D4C');
        $this->addSql('ALTER TABLE jms_job_related_entities DROP FOREIGN KEY FK_E956F4E2BE04EA9');
        $this->addSql('ALTER TABLE acl_entries DROP FOREIGN KEY FK_46C8B806EA000B10');
        $this->addSql('ALTER TABLE acl_entries DROP FOREIGN KEY FK_46C8B806DF9183C9');
        $this->addSql('ALTER TABLE acl_object_identities DROP FOREIGN KEY FK_9407E54977FA751A');
        $this->addSql('ALTER TABLE acl_object_identity_ancestors DROP FOREIGN KEY FK_825DE2993D9AB4A6');
        $this->addSql('ALTER TABLE acl_object_identity_ancestors DROP FOREIGN KEY FK_825DE299C671CEA1');
        $this->addSql('ALTER TABLE acl_entries DROP FOREIGN KEY FK_46C8B8063D9AB4A6');
        $this->addSql('DROP TABLE fos_user_group');
        $this->addSql('DROP TABLE fos_user_user');
        $this->addSql('DROP TABLE fos_user_user_group');
        $this->addSql('DROP TABLE media__gallery');
        $this->addSql('DROP TABLE media__gallery_media');
        $this->addSql('DROP TABLE media__media');
        $this->addSql('DROP TABLE ik_channel');
        $this->addSql('DROP TABLE ik_channel_screen_regions');
        $this->addSql('DROP TABLE ik_channelslideorder');
        $this->addSql('DROP TABLE ik_media_order');
        $this->addSql('DROP TABLE ik_screen');
        $this->addSql('DROP TABLE ik_screen_templates');
        $this->addSql('DROP TABLE ik_shared_channel');
        $this->addSql('DROP TABLE ik_sharing_index');
        $this->addSql('DROP TABLE ik_sharing_indexes_channels');
        $this->addSql('DROP TABLE ik_slide');
        $this->addSql('DROP TABLE ik_slide_templates');
        $this->addSql('DROP TABLE jms_cron_jobs');
        $this->addSql('DROP TABLE jms_jobs');
        $this->addSql('DROP TABLE jms_job_dependencies');
        $this->addSql('DROP TABLE jms_job_related_entities');
        $this->addSql('DROP TABLE acl_classes');
        $this->addSql('DROP TABLE acl_security_identities');
        $this->addSql('DROP TABLE acl_object_identities');
        $this->addSql('DROP TABLE acl_object_identity_ancestors');
        $this->addSql('DROP TABLE acl_entries');
        $this->addSql('DROP TABLE jms_job_statistics');
    }
}
