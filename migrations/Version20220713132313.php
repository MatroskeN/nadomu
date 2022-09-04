<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220713132313 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE auth_code (id INT AUTO_INCREMENT NOT NULL, promo_id INT DEFAULT NULL, code VARCHAR(6) NOT NULL, time INT NOT NULL, attempts INT DEFAULT NULL, ip VARCHAR(32) NOT NULL, phone VARCHAR(15) NOT NULL, is_completed TINYINT(1) NOT NULL, is_dialed TINYINT(1) DEFAULT NULL, INDEX IDX_5933D02CD0C07AFF (promo_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE auth_token (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, refresh_token_id INT NOT NULL, token VARCHAR(64) NOT NULL, expiration_time INT NOT NULL, INDEX IDX_9315F04EA76ED395 (user_id), UNIQUE INDEX UNIQ_9315F04EF765F60E (refresh_token_id), INDEX token_validation (user_id, expiration_time, token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cities (id INT AUTO_INCREMENT NOT NULL, region_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_D95DB16B98260155 (region_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE email_confirmation (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, code VARCHAR(32) NOT NULL, is_confirmed TINYINT(1) NOT NULL, create_time INT NOT NULL, confirm_time INT NOT NULL, INDEX IDX_1D2EF46FA76ED395 (user_id), INDEX code_validation (user_id, is_confirmed, code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE files (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, support_message_id INT DEFAULT NULL, create_time INT NOT NULL, file_path VARCHAR(255) NOT NULL, is_deleted TINYINT(1) NOT NULL, delete_time INT DEFAULT NULL, filetype VARCHAR(255) NOT NULL, INDEX IDX_6354059A76ED395 (user_id), INDEX IDX_635405971CED70B (support_message_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE metro_stations (id INT AUTO_INCREMENT NOT NULL, region_id INT NOT NULL, station VARCHAR(255) NOT NULL, line VARCHAR(255) NOT NULL, adm_area VARCHAR(255) NOT NULL, district VARCHAR(255) NOT NULL, line_color VARCHAR(16) DEFAULT NULL, INDEX IDX_3790C59B98260155 (region_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE promocodes (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, code VARCHAR(128) NOT NULL, action VARCHAR(64) NOT NULL, phone VARCHAR(16) NOT NULL, UNIQUE INDEX UNIQ_F211125077153098 (code), INDEX IDX_F21112507E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE refresh_token (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, token VARCHAR(64) NOT NULL, expiration_time INT NOT NULL, INDEX IDX_C74F2195A76ED395 (user_id), INDEX token_validation (user_id, expiration_time, token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE regions (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(128) NOT NULL, slug VARCHAR(64) NOT NULL, is_main TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_education (id INT AUTO_INCREMENT NOT NULL, service_info_id INT DEFAULT NULL, university VARCHAR(255) NOT NULL, year_from INT NOT NULL, year_to INT NOT NULL, INDEX IDX_839B6498920F9364 (service_info_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_images (id INT AUTO_INCREMENT NOT NULL, profile_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_DED8BB5CCCFA12B8 (profile_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_info (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, region_id INT DEFAULT NULL, images_id INT DEFAULT NULL, experience INT DEFAULT NULL, UNIQUE INDEX UNIQ_5CA85BBA76ED395 (user_id), INDEX IDX_5CA85BB98260155 (region_id), UNIQUE INDEX UNIQ_5CA85BBD44F05E5 (images_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_info_metro_stations (service_info_id INT NOT NULL, metro_stations_id INT NOT NULL, INDEX IDX_FF64B85920F9364 (service_info_id), INDEX IDX_FF64B85D1852259 (metro_stations_id), PRIMARY KEY(service_info_id, metro_stations_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_info_cities (service_info_id INT NOT NULL, cities_id INT NOT NULL, INDEX IDX_D81AC891920F9364 (service_info_id), INDEX IDX_D81AC891CAC75398 (cities_id), PRIMARY KEY(service_info_id, cities_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_price (id INT AUTO_INCREMENT NOT NULL, service_id INT NOT NULL, service_info_id INT DEFAULT NULL, price INT NOT NULL, INDEX IDX_63BACF3EED5CA9E6 (service_id), INDEX IDX_63BACF3E920F9364 (service_info_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_work_time (id INT AUTO_INCREMENT NOT NULL, service_info_id INT DEFAULT NULL, day INT NOT NULL, hour INT NOT NULL, INDEX IDX_CEC61337920F9364 (service_info_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE services (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, icon VARCHAR(255) NOT NULL, sort INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE support_message (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, ticket_id INT NOT NULL, message LONGTEXT NOT NULL, is_deleted TINYINT(1) NOT NULL, is_edited TINYINT(1) NOT NULL, create_time INT NOT NULL, is_support TINYINT(1) NOT NULL, INDEX IDX_B883883A76ED395 (user_id), INDEX IDX_B883883700047D2 (ticket_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE support_ticket (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, title VARCHAR(255) NOT NULL, is_closed TINYINT(1) NOT NULL, create_time INT NOT NULL, close_time INT NOT NULL, INDEX IDX_1F5A4D53A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, invited_id INT DEFAULT NULL, phone VARCHAR(180) NOT NULL, email VARCHAR(180) DEFAULT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_confirmed TINYINT(1) NOT NULL, is_blocked TINYINT(1) NOT NULL, create_time INT NOT NULL, last_visit_time INT NOT NULL, first_name VARCHAR(128) DEFAULT NULL, last_name VARCHAR(128) DEFAULT NULL, patronymic_name VARCHAR(128) DEFAULT NULL, gender VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649444F97DD (phone), INDEX IDX_8D93D649C2ED4747 (invited_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utm (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, utm_source VARCHAR(255) NOT NULL, utm_medium VARCHAR(255) NOT NULL, utm_campaign VARCHAR(255) NOT NULL, utm_term VARCHAR(255) NOT NULL, utm_content VARCHAR(255) NOT NULL, action VARCHAR(64) NOT NULL, visit_time INT NOT NULL, INDEX IDX_D52BD2BEA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE auth_code ADD CONSTRAINT FK_5933D02CD0C07AFF FOREIGN KEY (promo_id) REFERENCES promocodes (id)');
        $this->addSql('ALTER TABLE auth_token ADD CONSTRAINT FK_9315F04EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE auth_token ADD CONSTRAINT FK_9315F04EF765F60E FOREIGN KEY (refresh_token_id) REFERENCES refresh_token (id)');
        $this->addSql('ALTER TABLE cities ADD CONSTRAINT FK_D95DB16B98260155 FOREIGN KEY (region_id) REFERENCES regions (id)');
        $this->addSql('ALTER TABLE email_confirmation ADD CONSTRAINT FK_1D2EF46FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE files ADD CONSTRAINT FK_6354059A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE files ADD CONSTRAINT FK_635405971CED70B FOREIGN KEY (support_message_id) REFERENCES support_message (id)');
        $this->addSql('ALTER TABLE metro_stations ADD CONSTRAINT FK_3790C59B98260155 FOREIGN KEY (region_id) REFERENCES regions (id)');
        $this->addSql('ALTER TABLE promocodes ADD CONSTRAINT FK_F21112507E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE refresh_token ADD CONSTRAINT FK_C74F2195A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE service_education ADD CONSTRAINT FK_839B6498920F9364 FOREIGN KEY (service_info_id) REFERENCES service_info (id)');
        $this->addSql('ALTER TABLE service_images ADD CONSTRAINT FK_DED8BB5CCCFA12B8 FOREIGN KEY (profile_id) REFERENCES files (id)');
        $this->addSql('ALTER TABLE service_info ADD CONSTRAINT FK_5CA85BBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE service_info ADD CONSTRAINT FK_5CA85BB98260155 FOREIGN KEY (region_id) REFERENCES regions (id)');
        $this->addSql('ALTER TABLE service_info ADD CONSTRAINT FK_5CA85BBD44F05E5 FOREIGN KEY (images_id) REFERENCES service_images (id)');
        $this->addSql('ALTER TABLE service_info_metro_stations ADD CONSTRAINT FK_FF64B85920F9364 FOREIGN KEY (service_info_id) REFERENCES service_info (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_info_metro_stations ADD CONSTRAINT FK_FF64B85D1852259 FOREIGN KEY (metro_stations_id) REFERENCES metro_stations (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_info_cities ADD CONSTRAINT FK_D81AC891920F9364 FOREIGN KEY (service_info_id) REFERENCES service_info (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_info_cities ADD CONSTRAINT FK_D81AC891CAC75398 FOREIGN KEY (cities_id) REFERENCES cities (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_price ADD CONSTRAINT FK_63BACF3EED5CA9E6 FOREIGN KEY (service_id) REFERENCES services (id)');
        $this->addSql('ALTER TABLE service_price ADD CONSTRAINT FK_63BACF3E920F9364 FOREIGN KEY (service_info_id) REFERENCES service_info (id)');
        $this->addSql('ALTER TABLE service_work_time ADD CONSTRAINT FK_CEC61337920F9364 FOREIGN KEY (service_info_id) REFERENCES service_info (id)');
        $this->addSql('ALTER TABLE support_message ADD CONSTRAINT FK_B883883A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE support_message ADD CONSTRAINT FK_B883883700047D2 FOREIGN KEY (ticket_id) REFERENCES support_ticket (id)');
        $this->addSql('ALTER TABLE support_ticket ADD CONSTRAINT FK_1F5A4D53A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649C2ED4747 FOREIGN KEY (invited_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE utm ADD CONSTRAINT FK_D52BD2BEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE service_info_cities DROP FOREIGN KEY FK_D81AC891CAC75398');
        $this->addSql('ALTER TABLE service_images DROP FOREIGN KEY FK_DED8BB5CCCFA12B8');
        $this->addSql('ALTER TABLE service_info_metro_stations DROP FOREIGN KEY FK_FF64B85D1852259');
        $this->addSql('ALTER TABLE auth_code DROP FOREIGN KEY FK_5933D02CD0C07AFF');
        $this->addSql('ALTER TABLE auth_token DROP FOREIGN KEY FK_9315F04EF765F60E');
        $this->addSql('ALTER TABLE cities DROP FOREIGN KEY FK_D95DB16B98260155');
        $this->addSql('ALTER TABLE metro_stations DROP FOREIGN KEY FK_3790C59B98260155');
        $this->addSql('ALTER TABLE service_info DROP FOREIGN KEY FK_5CA85BB98260155');
        $this->addSql('ALTER TABLE service_info DROP FOREIGN KEY FK_5CA85BBD44F05E5');
        $this->addSql('ALTER TABLE service_education DROP FOREIGN KEY FK_839B6498920F9364');
        $this->addSql('ALTER TABLE service_info_metro_stations DROP FOREIGN KEY FK_FF64B85920F9364');
        $this->addSql('ALTER TABLE service_info_cities DROP FOREIGN KEY FK_D81AC891920F9364');
        $this->addSql('ALTER TABLE service_price DROP FOREIGN KEY FK_63BACF3E920F9364');
        $this->addSql('ALTER TABLE service_work_time DROP FOREIGN KEY FK_CEC61337920F9364');
        $this->addSql('ALTER TABLE service_price DROP FOREIGN KEY FK_63BACF3EED5CA9E6');
        $this->addSql('ALTER TABLE files DROP FOREIGN KEY FK_635405971CED70B');
        $this->addSql('ALTER TABLE support_message DROP FOREIGN KEY FK_B883883700047D2');
        $this->addSql('ALTER TABLE auth_token DROP FOREIGN KEY FK_9315F04EA76ED395');
        $this->addSql('ALTER TABLE email_confirmation DROP FOREIGN KEY FK_1D2EF46FA76ED395');
        $this->addSql('ALTER TABLE files DROP FOREIGN KEY FK_6354059A76ED395');
        $this->addSql('ALTER TABLE promocodes DROP FOREIGN KEY FK_F21112507E3C61F9');
        $this->addSql('ALTER TABLE refresh_token DROP FOREIGN KEY FK_C74F2195A76ED395');
        $this->addSql('ALTER TABLE service_info DROP FOREIGN KEY FK_5CA85BBA76ED395');
        $this->addSql('ALTER TABLE support_message DROP FOREIGN KEY FK_B883883A76ED395');
        $this->addSql('ALTER TABLE support_ticket DROP FOREIGN KEY FK_1F5A4D53A76ED395');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649C2ED4747');
        $this->addSql('ALTER TABLE utm DROP FOREIGN KEY FK_D52BD2BEA76ED395');
        $this->addSql('DROP TABLE auth_code');
        $this->addSql('DROP TABLE auth_token');
        $this->addSql('DROP TABLE cities');
        $this->addSql('DROP TABLE email_confirmation');
        $this->addSql('DROP TABLE files');
        $this->addSql('DROP TABLE metro_stations');
        $this->addSql('DROP TABLE promocodes');
        $this->addSql('DROP TABLE refresh_token');
        $this->addSql('DROP TABLE regions');
        $this->addSql('DROP TABLE service_education');
        $this->addSql('DROP TABLE service_images');
        $this->addSql('DROP TABLE service_info');
        $this->addSql('DROP TABLE service_info_metro_stations');
        $this->addSql('DROP TABLE service_info_cities');
        $this->addSql('DROP TABLE service_price');
        $this->addSql('DROP TABLE service_work_time');
        $this->addSql('DROP TABLE services');
        $this->addSql('DROP TABLE support_message');
        $this->addSql('DROP TABLE support_ticket');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE utm');
    }
}
