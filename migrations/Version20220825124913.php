<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220825124913 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE requests_service DROP FOREIGN KEY FK_435EA3D427EB8A5');
        $this->addSql('ALTER TABLE requests_service ADD CONSTRAINT FK_435EA3D427EB8A5 FOREIGN KEY (request_id) REFERENCES requests_specialists (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE requests_service DROP FOREIGN KEY FK_435EA3D427EB8A5');
        $this->addSql('ALTER TABLE requests_service ADD CONSTRAINT FK_435EA3D427EB8A5 FOREIGN KEY (request_id) REFERENCES requests (id)');
    }
}
