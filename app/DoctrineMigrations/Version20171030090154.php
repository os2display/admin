<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171030090154 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('UPDATE ik_grouping SET entity_type = \'Os2Display\\\\CoreBundle\\\\Entity\\\\Channel\' WHERE entity_type LIKE \'%Indholdskanalen\\\\\\\\MainBundle\\\\\\\\Entity\\\\\\\\Channel\'');
        $this->addSql('UPDATE ik_grouping SET entity_type = \'Os2Display\\\\CoreBundle\\\\Entity\\\\Screen\' WHERE entity_type LIKE \'%Indholdskanalen\\\\\\\\MainBundle\\\\\\\\Entity\\\\\\\\Screen\'');
        $this->addSql('UPDATE ik_grouping SET entity_type = \'Os2Display\\\\CoreBundle\\\\Entity\\\\Slide\' WHERE entity_type LIKE \'%Indholdskanalen\\\\\\\\MainBundle\\\\\\\\Entity\\\\\\\\Slide\'');
        $this->addSql('UPDATE ik_grouping SET entity_type = \'Os2Display\\\\MediaBundle\\\\Entity\\\\Media\' WHERE entity_type LIKE \'%Application\\\\\\\\Sonata\\\\\\\\MediaBundle\\\\\\\\Entity\\\\\\\\Media\'');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('UPDATE ik_grouping SET entity_type = \'Indholdskanalen\\\\MainBundle\\\\Entity\\\\Channel\' WHERE entity_type LIKE \'Os2Display\\\\\\\\CoreBundle\\\\\\\\Entity\\\\\\\\Channel\'');
        $this->addSql('UPDATE ik_grouping SET entity_type = \'Indholdskanalen\\\\MainBundle\\\\Entity\\\\Screen\' WHERE entity_type LIKE \'Os2Display\\\\\\\\CoreBundle\\\\\\\\Entity\\\\\\\\Screen\'');
        $this->addSql('UPDATE ik_grouping SET entity_type = \'Indholdskanalen\\\\MainBundle\\\\Entity\\\\Slide\' WHERE entity_type LIKE \'Os2Display\\\\\\\\CoreBundle\\\\\\\\Entity\\\\\\\\Slide\'');
        $this->addSql('UPDATE ik_grouping SET entity_type = \'Application\\\\Sonata\\\\MainBundle\\\\Entity\\\\Media\' WHERE entity_type LIKE \'Os2Display\\\\\\\\MediaBundle\\\\\\\\Entity\\\\\\\\Media\'');
    }
}
