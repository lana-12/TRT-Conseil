<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230208142623 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE candidate ADD city VARCHAR(100) NOT NULL');
        $this->addSql('DROP INDEX user_id_UNIQUE ON recruiter');
        $this->addSql('ALTER TABLE recruiter CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('ALTER TABLE messenger_messages CHANGE id id BIGINT AUTO_INCREMENT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE candidate DROP city');
        $this->addSql('ALTER TABLE messenger_messages CHANGE id id BIGINT NOT NULL');
        $this->addSql('ALTER TABLE recruiter CHANGE user_id user_id INT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX user_id_UNIQUE ON recruiter (user_id)');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
    }
}
