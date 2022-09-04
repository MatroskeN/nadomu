<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220808074013 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE files ADD public_photo_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE files ADD CONSTRAINT FK_63540595BAA4073 FOREIGN KEY (public_photo_id) REFERENCES service_images (id)');
        $this->addSql('CREATE INDEX IDX_63540595BAA4073 ON files (public_photo_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE files DROP FOREIGN KEY FK_63540595BAA4073');
        $this->addSql('DROP INDEX IDX_63540595BAA4073 ON files');
        $this->addSql('ALTER TABLE files DROP public_photo_id');
    }
}
