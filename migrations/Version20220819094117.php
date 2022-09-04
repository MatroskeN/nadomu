<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220819094117 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE requests_service DROP FOREIGN KEY FK_435EA3DED5CA9E6');
        $this->addSql('ALTER TABLE requests_service ADD price INT NOT NULL');
        $this->addSql('ALTER TABLE requests_service ADD CONSTRAINT FK_435EA3DED5CA9E6 FOREIGN KEY (service_id) REFERENCES services (id)');
        $this->addSql('ALTER TABLE service_info CHANGE time_range time_range TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE requests_service DROP FOREIGN KEY FK_435EA3DED5CA9E6');
        $this->addSql('ALTER TABLE requests_service DROP price');
        $this->addSql('ALTER TABLE requests_service ADD CONSTRAINT FK_435EA3DED5CA9E6 FOREIGN KEY (service_id) REFERENCES service_price (id)');
        $this->addSql('ALTER TABLE service_info CHANGE time_range time_range TINYINT(1) NOT NULL');
    }
}
