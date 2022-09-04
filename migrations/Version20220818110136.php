<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220818110136 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE requests (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, city_id INT DEFAULT NULL, metro_id INT DEFAULT NULL, address VARCHAR(255) NOT NULL, convenient_time VARCHAR(100) NOT NULL, date DATE DEFAULT NULL, work_time JSON DEFAULT NULL, additional_information LONGTEXT DEFAULT NULL, created_time INT NOT NULL, updated_time INT DEFAULT NULL, status VARCHAR(100) NOT NULL, INDEX IDX_7B85D651A76ED395 (user_id), INDEX IDX_7B85D6518BAC62AF (city_id), INDEX IDX_7B85D6511EA60E4E (metro_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE requests_service (id INT AUTO_INCREMENT NOT NULL, request_id INT NOT NULL, service_id INT NOT NULL, INDEX IDX_435EA3D427EB8A5 (request_id), INDEX IDX_435EA3DED5CA9E6 (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE requests_specialists (id INT AUTO_INCREMENT NOT NULL, request_id INT NOT NULL, specialist_id INT NOT NULL, INDEX IDX_CD4FC76C427EB8A5 (request_id), INDEX IDX_CD4FC76C7B100C1A (specialist_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE requests ADD CONSTRAINT FK_7B85D651A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE requests ADD CONSTRAINT FK_7B85D6518BAC62AF FOREIGN KEY (city_id) REFERENCES cities (id)');
        $this->addSql('ALTER TABLE requests ADD CONSTRAINT FK_7B85D6511EA60E4E FOREIGN KEY (metro_id) REFERENCES metro_stations (id)');
        $this->addSql('ALTER TABLE requests_service ADD CONSTRAINT FK_435EA3D427EB8A5 FOREIGN KEY (request_id) REFERENCES requests (id)');
        $this->addSql('ALTER TABLE requests_service ADD CONSTRAINT FK_435EA3DED5CA9E6 FOREIGN KEY (service_id) REFERENCES service_price (id)');
        $this->addSql('ALTER TABLE requests_specialists ADD CONSTRAINT FK_CD4FC76C427EB8A5 FOREIGN KEY (request_id) REFERENCES requests (id)');
        $this->addSql('ALTER TABLE requests_specialists ADD CONSTRAINT FK_CD4FC76C7B100C1A FOREIGN KEY (specialist_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE requests_service DROP FOREIGN KEY FK_435EA3D427EB8A5');
        $this->addSql('ALTER TABLE requests_specialists DROP FOREIGN KEY FK_CD4FC76C427EB8A5');
        $this->addSql('DROP TABLE requests');
        $this->addSql('DROP TABLE requests_service');
        $this->addSql('DROP TABLE requests_specialists');
    }
}
