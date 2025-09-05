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
        // Aucune contrainte FK existante à supprimer (constaté en base)
        // Garantir qu'au moins un éditeur existe pour le backfill
        $this->addSql("INSERT INTO editor (name, date_of_creation, address)
            SELECT 'Default Editor', '1970-01-01', 'N/A'
            WHERE NOT EXISTS (SELECT 1 FROM editor)");
        // Backfill: attribuer un éditeur valide aux enregistrements NULL/0 avant NOT NULL
        $this->addSql("UPDATE book b JOIN (SELECT MIN(id) AS id FROM editor) e ON 1=1 SET b.editor_id = e.id WHERE b.editor_id IS NULL OR b.editor_id = 0");
        $this->addSql('ALTER TABLE book CHANGE editor_id editor_id INT NOT NULL');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A3316995AC4C FOREIGN KEY (editor_id) REFERENCES editor (id) ON DELETE RESTRICT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY <NOM_TROUVÉ>');
        $this->addSql('ALTER TABLE book CHANGE editor_id editor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A3316995AC4C FOREIGN KEY (editor_id) REFERENCES editor (id) ON UPDATE NO ACTION ON DELETE SET NULL');
    }
}
