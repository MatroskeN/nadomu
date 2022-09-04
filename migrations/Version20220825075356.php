<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220825075356 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE requests ADD specialist_selected_id INT DEFAULT NULL, ADD flag_closed TINYINT(1) DEFAULT NULL, DROP status');
        $this->addSql('ALTER TABLE requests ADD CONSTRAINT FK_7B85D65159CF1E38 FOREIGN KEY (specialist_selected_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_7B85D65159CF1E38 ON requests (specialist_selected_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE requests DROP FOREIGN KEY FK_7B85D65159CF1E38');
        $this->addSql('DROP INDEX IDX_7B85D65159CF1E38 ON requests');
        $this->addSql('ALTER TABLE requests ADD status VARCHAR(100) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, DROP specialist_selected_id, DROP flag_closed');
    }
}
