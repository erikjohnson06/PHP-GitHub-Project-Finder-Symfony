<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220928020038 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE git_hub_repository_record (id INT AUTO_INCREMENT NOT NULL, repository_id INT NOT NULL, name VARCHAR(255) NOT NULL, html_url VARCHAR(255) NOT NULL, description VARCHAR(1000) DEFAULT NULL, stargazers_count INT NOT NULL, created_at DATETIME NOT NULL, pushed_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE github_projects');
        //$this->addSql('DROP TABLE github_projects_request_manager');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE github_projects (repository_id INT NOT NULL, name VARCHAR(250) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, html_url VARCHAR(250) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, description VARCHAR(1000) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, stargazers_count INT DEFAULT 0, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, pushed_at DATETIME NOT NULL, UNIQUE INDEX repository_id (repository_id), PRIMARY KEY(repository_id)) DEFAULT CHARACTER SET latin1 COLLATE `latin1_swedish_ci` ENGINE = InnoDB COMMENT = \'\' ');
        //$this->addSql('CREATE TABLE github_projects_request_manager (is_running TINYINT(1) NOT NULL, start_time DATETIME DEFAULT NULL, end_time DATETIME DEFAULT NULL, error_msg VARCHAR(250) CHARACTER SET latin1 DEFAULT NULL COLLATE `latin1_swedish_ci`) DEFAULT CHARACTER SET latin1 COLLATE `latin1_swedish_ci` ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('DROP TABLE git_hub_repository_record');
    }
}
