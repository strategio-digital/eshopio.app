<?php

declare(strict_types=1);

namespace App\Database\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200311152108 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE segment (id INT AUTO_INCREMENT NOT NULL, project_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_1881F565166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE segment_email_campaign (segment_id INT NOT NULL, email_campaign_id INT NOT NULL, INDEX IDX_23E4FE29DB296AAD (segment_id), INDEX IDX_23E4FE29E0F98BC3 (email_campaign_id), PRIMARY KEY(segment_id, email_campaign_id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE smtp_setting (id INT AUTO_INCREMENT NOT NULL, project_id INT DEFAULT NULL, host VARCHAR(255) NOT NULL, sender_email VARCHAR(255) NOT NULL, sender_name VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, secure VARCHAR(8) DEFAULT NULL, port INT NOT NULL, minute_limit INT NOT NULL, day_limit INT NOT NULL, reached_minute_limit INT DEFAULT 0 NOT NULL, reached_day_limit INT DEFAULT 0 NOT NULL, messages_count INT DEFAULT 0 NOT NULL, last_usage DATETIME DEFAULT NULL, INDEX IDX_325E2EEF166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE email_campaign (id INT AUTO_INCREMENT NOT NULL, smtp_setting_id INT DEFAULT NULL, project_id INT NOT NULL, secret_key VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, start_date DATETIME NOT NULL, start_time DATETIME NOT NULL, subject VARCHAR(128) NOT NULL, message VARCHAR(4096) NOT NULL, open_rate DOUBLE PRECISION DEFAULT \'0\' NOT NULL, click_rate DOUBLE PRECISION DEFAULT \'0\' NOT NULL, status SMALLINT DEFAULT 0 NOT NULL, INDEX IDX_14730D94432B2740 (smtp_setting_id), INDEX IDX_14730D94166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, project_id INT DEFAULT NULL, segment_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) DEFAULT NULL, phone VARCHAR(32) DEFAULT NULL, messages_count INT DEFAULT 0 NOT NULL, last_usage DATETIME DEFAULT NULL, INDEX IDX_4C62E638166D1F9C (project_id), INDEX IDX_4C62E638DB296AAD (segment_id), UNIQUE INDEX UNIQ_4C62E638166D1F9CE7927C74 (project_id, email), UNIQUE INDEX UNIQ_4C62E638166D1F9C444F97DD (project_id, phone), UNIQUE INDEX UNIQ_4C62E638166D1F9C444F97DDE7927C74 (project_id, phone, email), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, secret_key VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_user (project_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_B4021E51166D1F9C (project_id), INDEX IDX_B4021E51A76ED395 (user_id), PRIMARY KEY(project_id, user_id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE excluded_day (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(3) NOT NULL, UNIQUE INDEX UNIQ_290FF4125E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE excluded_day_email_campaign (excluded_day_id INT NOT NULL, email_campaign_id INT NOT NULL, INDEX IDX_F6B69925998C8B2F (excluded_day_id), INDEX IDX_F6B69925E0F98BC3 (email_campaign_id), PRIMARY KEY(excluded_day_id, email_campaign_id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sent_email (id INT AUTO_INCREMENT NOT NULL, email_campaign_id INT NOT NULL, contact_id INT DEFAULT NULL, secret_key VARCHAR(36) NOT NULL, sent_time DATETIME DEFAULT NULL, last_open DATETIME DEFAULT NULL, last_click DATETIME DEFAULT NULL, open_count INT DEFAULT 0 NOT NULL, last_browser VARCHAR(255) DEFAULT NULL, last_device VARCHAR(255) DEFAULT NULL, last_ip VARCHAR(64) DEFAULT NULL, INDEX IDX_E92EE5FCE0F98BC3 (email_campaign_id), INDEX IDX_E92EE5FCE7A1254A (contact_id), INDEX IDX_E92EE5FC7F4741F5 (secret_key), UNIQUE INDEX UNIQ_E92EE5FCE0F98BC3E7A1254A (email_campaign_id, contact_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE segment ADD CONSTRAINT FK_1881F565166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE segment_email_campaign ADD CONSTRAINT FK_23E4FE29DB296AAD FOREIGN KEY (segment_id) REFERENCES segment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE segment_email_campaign ADD CONSTRAINT FK_23E4FE29E0F98BC3 FOREIGN KEY (email_campaign_id) REFERENCES email_campaign (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE smtp_setting ADD CONSTRAINT FK_325E2EEF166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE email_campaign ADD CONSTRAINT FK_14730D94432B2740 FOREIGN KEY (smtp_setting_id) REFERENCES smtp_setting (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE email_campaign ADD CONSTRAINT FK_14730D94166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT FK_4C62E638166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT FK_4C62E638DB296AAD FOREIGN KEY (segment_id) REFERENCES segment (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE project_user ADD CONSTRAINT FK_B4021E51166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_user ADD CONSTRAINT FK_B4021E51A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE excluded_day_email_campaign ADD CONSTRAINT FK_F6B69925998C8B2F FOREIGN KEY (excluded_day_id) REFERENCES excluded_day (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE excluded_day_email_campaign ADD CONSTRAINT FK_F6B69925E0F98BC3 FOREIGN KEY (email_campaign_id) REFERENCES email_campaign (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sent_email ADD CONSTRAINT FK_E92EE5FCE0F98BC3 FOREIGN KEY (email_campaign_id) REFERENCES email_campaign (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sent_email ADD CONSTRAINT FK_E92EE5FCE7A1254A FOREIGN KEY (contact_id) REFERENCES contact (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE segment_email_campaign DROP FOREIGN KEY FK_23E4FE29DB296AAD');
        $this->addSql('ALTER TABLE contact DROP FOREIGN KEY FK_4C62E638DB296AAD');
        $this->addSql('ALTER TABLE email_campaign DROP FOREIGN KEY FK_14730D94432B2740');
        $this->addSql('ALTER TABLE segment_email_campaign DROP FOREIGN KEY FK_23E4FE29E0F98BC3');
        $this->addSql('ALTER TABLE excluded_day_email_campaign DROP FOREIGN KEY FK_F6B69925E0F98BC3');
        $this->addSql('ALTER TABLE sent_email DROP FOREIGN KEY FK_E92EE5FCE0F98BC3');
        $this->addSql('ALTER TABLE project_user DROP FOREIGN KEY FK_B4021E51A76ED395');
        $this->addSql('ALTER TABLE sent_email DROP FOREIGN KEY FK_E92EE5FCE7A1254A');
        $this->addSql('ALTER TABLE segment DROP FOREIGN KEY FK_1881F565166D1F9C');
        $this->addSql('ALTER TABLE smtp_setting DROP FOREIGN KEY FK_325E2EEF166D1F9C');
        $this->addSql('ALTER TABLE email_campaign DROP FOREIGN KEY FK_14730D94166D1F9C');
        $this->addSql('ALTER TABLE contact DROP FOREIGN KEY FK_4C62E638166D1F9C');
        $this->addSql('ALTER TABLE project_user DROP FOREIGN KEY FK_B4021E51166D1F9C');
        $this->addSql('ALTER TABLE excluded_day_email_campaign DROP FOREIGN KEY FK_F6B69925998C8B2F');
        $this->addSql('DROP TABLE segment');
        $this->addSql('DROP TABLE segment_email_campaign');
        $this->addSql('DROP TABLE smtp_setting');
        $this->addSql('DROP TABLE email_campaign');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE project_user');
        $this->addSql('DROP TABLE excluded_day');
        $this->addSql('DROP TABLE excluded_day_email_campaign');
        $this->addSql('DROP TABLE sent_email');
    }
}
