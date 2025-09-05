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
        // S'assurer que la colonne accepte NULL
        $this->addSql('ALTER TABLE book CHANGE editor_id editor_id INT DEFAULT NULL');
        // Nettoyer les références orphelines avant d'ajouter la contrainte
        $this->addSql("UPDATE book b LEFT JOIN editor e ON b.editor_id = e.id SET b.editor_id = NULL WHERE b.editor_id IS NOT NULL AND e.id IS NULL");
        $this->addSql("UPDATE book SET editor_id = NULL WHERE editor_id = 0");
        // Contrainte et index
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A3316995AC4C FOREIGN KEY (editor_id) REFERENCES editor (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_CBE5A3316995AC4C ON book (editor_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A3316995AC4C');
        $this->addSql('DROP INDEX IDX_CBE5A3316995AC4C ON book');
    }
}
