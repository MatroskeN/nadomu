<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220728153309 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE notifications (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, email_expert_answers TINYINT(1) NOT NULL, email_new_requests TINYINT(1) NOT NULL, sms_expert_answers TINYINT(1) NOT NULL, sms_new_requests TINYINT(1) NOT NULL, sms_users_response TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_6000B0D39D86650F (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D39D86650F FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD birthday DATE DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE notifications');
        $this->addSql('ALTER TABLE user DROP birthday');
    }
}
