<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250828120920 extends AbstractMigration
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
            // Si les tables n'existent pas encore, ne rien faire (migration précédente doit d'abord créer)
            return;
        }

        // Garantir qu'au moins un éditeur existe pour le backfill
        $this->addSql("INSERT INTO editor (name, date_of_creation, address)
            SELECT 'Default Editor', '1970-01-01', 'N/A'
            WHERE NOT EXISTS (SELECT 1 FROM editor)");

        // Assurer existence de la colonne editor_id
        $bookColumns = array_map(static function ($col) { return $col->getName(); }, $schemaManager->listTableColumns('book'));
        if (!in_array('editor_id', $bookColumns, true)) {
            $this->addSql('ALTER TABLE book ADD editor_id INT DEFAULT NULL');
        }

        // Backfill: attribuer un éditeur valide aux enregistrements NULL/0 avant NOT NULL
        $this->addSql("UPDATE book b JOIN (SELECT MIN(id) AS id FROM editor) e ON 1=1 SET b.editor_id = e.id WHERE b.editor_id IS NULL OR b.editor_id = 0");
        $this->addSql('ALTER TABLE book CHANGE editor_id editor_id INT NOT NULL');

        // Ajouter la FK si absente
        $fkExists = (bool) $this->connection->fetchOne(
            "SELECT COUNT(1) FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'book' AND CONSTRAINT_NAME = 'FK_CBE5A3316995AC4C'"
        );
        if (!$fkExists) {
            $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A3316995AC4C FOREIGN KEY (editor_id) REFERENCES editor (id) ON DELETE RESTRICT');
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        // Supprimer FK si elle existe
        $fkExists = (bool) $this->connection->fetchOne(
            "SELECT COUNT(1) FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'book' AND CONSTRAINT_NAME = 'FK_CBE5A3316995AC4C'"
        );
        if ($fkExists) {
            $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A3316995AC4C');
        }
        // Rendre la colonne nullable
        $this->addSql('ALTER TABLE book CHANGE editor_id editor_id INT DEFAULT NULL');
    }
}