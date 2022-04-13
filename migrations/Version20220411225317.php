<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220411225317 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE dynamic_form_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE dynamic_form_input_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE dynamic_form (id INT NOT NULL, name VARCHAR(255) NOT NULL, sort INT DEFAULT 0 NOT NULL, display_rule VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE dynamic_form_input (id INT NOT NULL, form_id INT NOT NULL, name VARCHAR(255) NOT NULL, label VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, options JSON DEFAULT NULL, step INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D252392E5FF69B7D ON dynamic_form_input (form_id)');
        $this->addSql('ALTER TABLE dynamic_form_input ADD CONSTRAINT FK_D252392E5FF69B7D FOREIGN KEY (form_id) REFERENCES dynamic_form (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dynamic_form_input DROP CONSTRAINT FK_D252392E5FF69B7D');
        $this->addSql('DROP SEQUENCE dynamic_form_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE dynamic_form_input_id_seq CASCADE');
        $this->addSql('DROP TABLE dynamic_form');
        $this->addSql('DROP TABLE dynamic_form_input');
    }
}
