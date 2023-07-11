<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230711095534 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__movie AS SELECT id, title, plot, release_date FROM movie');
        $this->addSql('DROP TABLE movie');
        $this->addSql('CREATE TABLE movie (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, plot CLOB DEFAULT NULL, released_at DATE DEFAULT NULL)');
        $this->addSql('INSERT INTO movie (id, title, plot, released_at) SELECT id, title, plot, release_date FROM __temp__movie');
        $this->addSql('DROP TABLE __temp__movie');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__movie AS SELECT id, title, plot, released_at FROM movie');
        $this->addSql('DROP TABLE movie');
        $this->addSql('CREATE TABLE movie (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, plot CLOB DEFAULT NULL, release_date DATE DEFAULT NULL)');
        $this->addSql('INSERT INTO movie (id, title, plot, release_date) SELECT id, title, plot, released_at FROM __temp__movie');
        $this->addSql('DROP TABLE __temp__movie');
    }
}
