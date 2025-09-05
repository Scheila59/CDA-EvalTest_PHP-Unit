<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250828115833 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $schemaManager = $this->connection->createSchemaManager();
        $hasBookTable = $schemaManager->tablesExist(['book']);
        $hasEditorTable = $schemaManager->tablesExist(['editor']);

        if (!$hasBookTable || !$hasEditorTable) {
            // Si les tables n'existent pas (base fraîche), on ne fait rien ici.
            return;
        }

        // Assurer que la colonne editor_id existe avant de la modifier
        $bookColumns = array_map(static function ($col) { return $col->getName(); }, $schemaManager->listTableColumns('book'));
        if (!in_array('editor_id', $bookColumns, true)) {
            $this->addSql('ALTER TABLE book ADD editor_id INT DEFAULT NULL');
        }

        // S'assurer que la colonne accepte NULL
        $this->addSql('ALTER TABLE book CHANGE editor_id editor_id INT DEFAULT NULL');

        // Nettoyer les références orphelines avant d'ajouter la contrainte
        $this->addSql("UPDATE book b LEFT JOIN editor e ON b.editor_id = e.id SET b.editor_id = NULL WHERE b.editor_id IS NOT NULL AND e.id IS NULL");
        $this->addSql("UPDATE book SET editor_id = NULL WHERE editor_id = 0");

        // Ajouter l'index si absent
        $indexExists = (bool) $this->connection->fetchOne(
            "SELECT COUNT(1) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'book' AND INDEX_NAME = 'IDX_CBE5A3316995AC4C'"
        );
        if (!$indexExists) {
            $this->addSql('CREATE INDEX IDX_CBE5A3316995AC4C ON book (editor_id)');
        }

        // Ajouter la contrainte si absente
        $fkExists = (bool) $this->connection->fetchOne(
            "SELECT COUNT(1) FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'book' AND CONSTRAINT_NAME = 'FK_CBE5A3316995AC4C'"
        );
        if (!$fkExists) {
            $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A3316995AC4C FOREIGN KEY (editor_id) REFERENCES editor (id) ON DELETE SET NULL');
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        // Supprimer la contrainte si elle existe
        $fkExists = (bool) $this->connection->fetchOne(
            "SELECT COUNT(1) FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'book' AND CONSTRAINT_NAME = 'FK_CBE5A3316995AC4C'"
        );
        if ($fkExists) {
            $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A3316995AC4C');
        }
        // Supprimer l'index si il existe
        $indexExists = (bool) $this->connection->fetchOne(
            "SELECT COUNT(1) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'book' AND INDEX_NAME = 'IDX_CBE5A3316995AC4C'"
        );
        if ($indexExists) {
            $this->addSql('DROP INDEX IDX_CBE5A3316995AC4C ON book');
        }
    }
}
