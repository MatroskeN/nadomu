<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220801120124 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE files ADD private_docs_id INT DEFAULT NULL, ADD public_docs_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE files ADD CONSTRAINT FK_6354059D4D6D82E FOREIGN KEY (private_docs_id) REFERENCES service_images (id)');
        $this->addSql('ALTER TABLE files ADD CONSTRAINT FK_63540597EF14B78 FOREIGN KEY (public_docs_id) REFERENCES service_images (id)');
        $this->addSql('CREATE INDEX IDX_6354059D4D6D82E ON files (private_docs_id)');
        $this->addSql('CREATE INDEX IDX_63540597EF14B78 ON files (public_docs_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE files DROP FOREIGN KEY FK_6354059D4D6D82E');
        $this->addSql('ALTER TABLE files DROP FOREIGN KEY FK_63540597EF14B78');
        $this->addSql('DROP INDEX IDX_6354059D4D6D82E ON files');
        $this->addSql('DROP INDEX IDX_63540597EF14B78 ON files');
        $this->addSql('ALTER TABLE files DROP private_docs_id, DROP public_docs_id');
    }
}
