<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220929013301 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE git_hub_projects_request_manager (id INT AUTO_INCREMENT NOT NULL, is_running TINYINT(1) NOT NULL, start_time DATETIME DEFAULT NULL, end_time DATETIME DEFAULT NULL, error_msg VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        //$this->addSql('DROP TABLE github_projects_request_manager');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        //$this->addSql('CREATE TABLE github_projects_request_manager (is_running TINYINT(1) NOT NULL, start_time DATETIME DEFAULT NULL, end_time DATETIME DEFAULT NULL, error_msg VARCHAR(250) CHARACTER SET latin1 DEFAULT NULL COLLATE `latin1_swedish_ci`) DEFAULT CHARACTER SET latin1 COLLATE `latin1_swedish_ci` ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('DROP TABLE git_hub_projects_request_manager');
    }
}
