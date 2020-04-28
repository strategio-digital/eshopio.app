<?php

declare(strict_types=1);

namespace App\Database\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200312155215 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE excluded_day_email_campaign DROP FOREIGN KEY FK_F6B69925998C8B2F');
        $this->addSql('CREATE TABLE allowed_day (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(3) NOT NULL, UNIQUE INDEX UNIQ_165192FA5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE allowed_day_email_campaign (allowed_day_id INT NOT NULL, email_campaign_id INT NOT NULL, INDEX IDX_D448B6D18109BADA (allowed_day_id), INDEX IDX_D448B6D1E0F98BC3 (email_campaign_id), PRIMARY KEY(allowed_day_id, email_campaign_id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE allowed_day_email_campaign ADD CONSTRAINT FK_D448B6D18109BADA FOREIGN KEY (allowed_day_id) REFERENCES allowed_day (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE allowed_day_email_campaign ADD CONSTRAINT FK_D448B6D1E0F98BC3 FOREIGN KEY (email_campaign_id) REFERENCES email_campaign (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE excluded_day');
        $this->addSql('DROP TABLE excluded_day_email_campaign');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE allowed_day_email_campaign DROP FOREIGN KEY FK_D448B6D18109BADA');
        $this->addSql('CREATE TABLE excluded_day (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(3) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, UNIQUE INDEX UNIQ_290FF4125E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE excluded_day_email_campaign (excluded_day_id INT NOT NULL, email_campaign_id INT NOT NULL, INDEX IDX_F6B69925E0F98BC3 (email_campaign_id), INDEX IDX_F6B69925998C8B2F (excluded_day_id), PRIMARY KEY(excluded_day_id, email_campaign_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE excluded_day_email_campaign ADD CONSTRAINT FK_F6B69925998C8B2F FOREIGN KEY (excluded_day_id) REFERENCES excluded_day (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE excluded_day_email_campaign ADD CONSTRAINT FK_F6B69925E0F98BC3 FOREIGN KEY (email_campaign_id) REFERENCES email_campaign (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE allowed_day');
        $this->addSql('DROP TABLE allowed_day_email_campaign');
    }
}
