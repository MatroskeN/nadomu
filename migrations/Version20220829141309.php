<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220829141309 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE feedback (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, status TINYINT(1) NOT NULL, created_at INT NOT NULL, updated_at INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE chat ADD feedback_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE chat ADD CONSTRAINT FK_659DF2AAEBE50732 FOREIGN KEY (feedback_id) REFERENCES feedback (id)');
        $this->addSql('CREATE INDEX IDX_659DF2AAEBE50732 ON chat (feedback_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chat DROP FOREIGN KEY FK_659DF2AAEBE50732');
        $this->addSql('DROP TABLE feedback');
        $this->addSql('DROP INDEX IDX_659DF2AAEBE50732 ON chat');
        $this->addSql('ALTER TABLE chat DROP feedback_id_id');
    }
}
