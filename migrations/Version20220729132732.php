<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220729132732 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notifications RENAME INDEX uniq_6000b0d39d86650f TO UNIQ_6000B0D3A76ED395');
        $this->addSql('ALTER TABLE service_info ADD about LONGTEXT DEFAULT NULL, ADD time_range TINYINT(1) NOT NULL, ADD callback_phone VARCHAR(180) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notifications RENAME INDEX uniq_6000b0d3a76ed395 TO UNIQ_6000B0D39D86650F');
        $this->addSql('ALTER TABLE service_info DROP about, DROP time_range, DROP callback_phone');
    }
}
