<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220316215604 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meeting ADD note TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE schedule_rule ALTER rule TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE schedule_rule ALTER rule DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE schedule_rule ALTER rule TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE schedule_rule ALTER rule DROP DEFAULT');
        $this->addSql('ALTER TABLE meeting DROP note');
    }
}
