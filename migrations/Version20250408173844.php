<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250408173844 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE tarea ADD equipo_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tarea ADD CONSTRAINT FK_3CA0536623BFBED FOREIGN KEY (equipo_id) REFERENCES equipo (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_3CA0536623BFBED ON tarea (equipo_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE tarea DROP FOREIGN KEY FK_3CA0536623BFBED
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_3CA0536623BFBED ON tarea
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tarea DROP equipo_id
        SQL);
    }
}
