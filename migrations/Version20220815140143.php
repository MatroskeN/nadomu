<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220815140143 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE service_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, city_id INT DEFAULT NULL, metro_id INT DEFAULT NULL, address VARCHAR(255) NOT NULL, service JSON NOT NULL, convenient_time VARCHAR(50) NOT NULL, date DATE DEFAULT NULL, work_time JSON DEFAULT NULL, additional_information LONGTEXT DEFAULT NULL, created_time INT NOT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_F413DD03A76ED395 (user_id), INDEX IDX_F413DD038BAC62AF (city_id), INDEX IDX_F413DD031EA60E4E (metro_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_request_specialists (id INT AUTO_INCREMENT NOT NULL, request_id INT NOT NULL, specialist_id INT NOT NULL, INDEX IDX_4B77B38B427EB8A5 (request_id), INDEX IDX_4B77B38B7B100C1A (specialist_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE service_request ADD CONSTRAINT FK_F413DD03A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE service_request ADD CONSTRAINT FK_F413DD038BAC62AF FOREIGN KEY (city_id) REFERENCES cities (id)');
        $this->addSql('ALTER TABLE service_request ADD CONSTRAINT FK_F413DD031EA60E4E FOREIGN KEY (metro_id) REFERENCES metro_stations (id)');
        $this->addSql('ALTER TABLE service_request_specialists ADD CONSTRAINT FK_4B77B38B427EB8A5 FOREIGN KEY (request_id) REFERENCES service_request (id)');
        $this->addSql('ALTER TABLE service_request_specialists ADD CONSTRAINT FK_4B77B38B7B100C1A FOREIGN KEY (specialist_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE service_request_specialists DROP FOREIGN KEY FK_4B77B38B427EB8A5');
        $this->addSql('DROP TABLE service_request');
        $this->addSql('DROP TABLE service_request_specialists');
    }
}
