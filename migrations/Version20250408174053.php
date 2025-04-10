<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250408174053 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE tarea ADD tecnico_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tarea ADD CONSTRAINT FK_3CA05366841DB1E7 FOREIGN KEY (tecnico_id) REFERENCES usuario (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_3CA05366841DB1E7 ON tarea (tecnico_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE tarea DROP FOREIGN KEY FK_3CA05366841DB1E7
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_3CA05366841DB1E7 ON tarea
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tarea DROP tecnico_id
        SQL);
    }
}
