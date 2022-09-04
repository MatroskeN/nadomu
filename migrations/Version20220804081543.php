<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220804081543 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notifications ADD email_users_response TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64986383B10');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649EF1A9D84');
        $this->addSql('DROP INDEX UNIQ_8D93D64986383B10 ON user');
        $this->addSql('DROP INDEX UNIQ_8D93D649EF1A9D84 ON user');
        $this->addSql('ALTER TABLE user DROP avatar_id, DROP notification_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notifications DROP email_users_response');
        $this->addSql('ALTER TABLE user ADD avatar_id INT DEFAULT NULL, ADD notification_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64986383B10 FOREIGN KEY (avatar_id) REFERENCES files (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649EF1A9D84 FOREIGN KEY (notification_id) REFERENCES notifications (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64986383B10 ON user (avatar_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649EF1A9D84 ON user (notification_id)');
    }
}
