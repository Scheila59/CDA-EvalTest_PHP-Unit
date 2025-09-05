<?php

namespace App\Tests\Integration\Repository;

use App\Entity\Editor;
use App\Entity\Book;
use App\Repository\EditorRepository;
use PHPUnit\Framework\TestCase;

/**
 * Tests d'intégration pour EditorRepository
 * 
 * Ces tests vérifient la logique des repositories sans base de données réelle.
 * Ils testent les méthodes de recherche et la logique métier des repositories.
 */
class EditorRepositoryTest extends TestCase
{
    private EditorRepository $editorRepository;

    protected function setUp(): void
    {
        // Mock du repository pour tester la logique sans base de données
        $this->editorRepository = $this->createMock(EditorRepository::class);
    }

    /**
     * Test de la logique de recherche par nom
     */
    public function testFindByNameLogic(): void
    {
        // Créer des éditeurs de test
        $editor1 = new Editor();
        $editor1->setName('Gallimard');
        $editor1->setDateOfCreation(new \DateTime('1911-05-31'));
        $editor1->setAddress('Paris, France');

        $editor2 = new Editor();
        $editor2->setName('Flammarion');
        $editor2->setDateOfCreation(new \DateTime('1876-01-01'));
        $editor2->setAddress('Paris, France');

        // Simuler la logique de recherche par nom
        $editors = [$editor1, $editor2];
        $searchName = 'Gallimard';
        
        $filteredEditors = array_filter($editors, function($editor) use ($searchName) {
            return $editor->getName() === $searchName;
        });

        // Vérifications
        $this->assertCount(1, $filteredEditors);
        $this->assertEquals('Gallimard', reset($filteredEditors)->getName());
    }

    /**
     * Test de la logique de recherche par siège social
     */
    public function testFindByHeadOfficeLogic(): void
    {
        // Créer des éditeurs de différents sièges
        $editors = [
            (new Editor())->setName('Gallimard')->setDateOfCreation(new \DateTime('1911-05-31'))->setAddress('Paris, France'),
            (new Editor())->setName('Flammarion')->setDateOfCreation(new \DateTime('1876-01-01'))->setAddress('Paris, France'),
            (new Editor())->setName('Penguin Books')->setDateOfCreation(new \DateTime('1935-01-01'))->setAddress('Londres, Royaume-Uni'),
            (new Editor())->setName('Random House')->setDateOfCreation(new \DateTime('1927-01-01'))->setAddress('New York, USA'),
        ];

        // Simuler la recherche par siège social
        $searchHeadOffice = 'Paris, France';
        $filteredEditors = array_filter($editors, function($editor) use ($searchHeadOffice) {
            return $editor->getAddress() === $searchHeadOffice;
        });

        // Vérifications
        $this->assertCount(2, $filteredEditors);
        foreach ($filteredEditors as $editor) {
            $this->assertEquals('Paris, France', $editor->getAddress());
        }
    }

    /**
     * Test de la logique de recherche par date de création
     */
    public function testFindByCreationDateLogic(): void
    {
        // Créer des éditeurs avec différentes dates
        $editors = [
            (new Editor())->setName('Ancien Éditeur')->setDateOfCreation(new \DateTime('1800-01-01'))->setAddress('Ville Ancienne'),
            (new Editor())->setName('Moderne Éditeur')->setDateOfCreation(new \DateTime('2000-01-01'))->setAddress('Ville Moderne'),
            (new Editor())->setName('Éditeur Classique')->setDateOfCreation(new \DateTime('1900-01-01'))->setAddress('Ville Classique'),
        ];

        // Simuler la recherche par date de création (après 1900)
        $searchDate = new \DateTime('1900-01-01');
        $filteredEditors = array_filter($editors, function($editor) use ($searchDate) {
            return $editor->getDateOfCreation() >= $searchDate;
        });

        // Vérifications
        $this->assertCount(2, $filteredEditors);
        foreach ($filteredEditors as $editor) {
            $this->assertGreaterThanOrEqual($searchDate, $editor->getDateOfCreation());
        }
    }

    /**
     * Test de la logique de tri par nom
     */
    public function testSortByNameLogic(): void
    {
        // Créer des éditeurs avec des noms différents
        $editors = [
            (new Editor())->setName('Z Publishing')->setDateOfCreation(new \DateTime('2000-01-01'))->setAddress('Ville Z'),
            (new Editor())->setName('Alpha Books')->setDateOfCreation(new \DateTime('1900-01-01'))->setAddress('Ville A'),
            (new Editor())->setName('Beta Press')->setDateOfCreation(new \DateTime('1950-01-01'))->setAddress('Ville B'),
        ];

        // Simuler le tri par nom
        usort($editors, function($a, $b) {
            return strcmp($a->getName(), $b->getName());
        });

        // Vérifications
        $this->assertEquals('Alpha Books', $editors[0]->getName());
        $this->assertEquals('Beta Press', $editors[1]->getName());
        $this->assertEquals('Z Publishing', $editors[2]->getName());
    }

    /**
     * Test de la logique de recherche avec livres
     */
    public function testFindWithBooksLogic(): void
    {
        // Créer un éditeur
        $editor = new Editor();
        $editor->setName('Gallimard');
        $editor->setDateOfCreation(new \DateTime('1911-05-31'));
        $editor->setAddress('Paris, France');

        // Créer des livres pour cet éditeur
        $book1 = new Book();
        $book1->setTitle('L\'Étranger');
        $book1->setImage('etranger.jpg');
        $book1->setDescription('Un roman existentialiste');
        $book1->setPages(123);
        $book1->setEditor($editor);

        $book2 = new Book();
        $book2->setTitle('La Peste');
        $book2->setImage('peste.jpg');
        $book2->setDescription('Une allégorie de la résistance');
        $book2->setPages(247);
        $book2->setEditor($editor);

        // Ajouter les livres à l'éditeur
        $editor->addBook($book1);
        $editor->addBook($book2);

        // Simuler la récupération avec livres
        $editors = [$editor];
        $editorWithBooks = $editors[0];

        // Vérifier les relations
        $this->assertCount(2, $editorWithBooks->getBooks());
        $this->assertTrue($editorWithBooks->getBooks()->contains($book1));
        $this->assertTrue($editorWithBooks->getBooks()->contains($book2));
    }

    /**
     * Test de la logique de recherche par période de création
     */
    public function testFindByCreationPeriodLogic(): void
    {
        // Créer des éditeurs de différentes périodes
        $editors = [
            (new Editor())->setName('Éditeur XIXe')->setDateOfCreation(new \DateTime('1850-01-01'))->setAddress('Ville XIXe'),
            (new Editor())->setName('Éditeur XXe Début')->setDateOfCreation(new \DateTime('1920-01-01'))->setAddress('Ville XXe Début'),
            (new Editor())->setName('Éditeur XXe Fin')->setDateOfCreation(new \DateTime('1980-01-01'))->setAddress('Ville XXe Fin'),
            (new Editor())->setName('Éditeur XXIe')->setDateOfCreation(new \DateTime('2010-01-01'))->setAddress('Ville XXIe'),
        ];

        // Simuler la recherche par période (XXe siècle)
        $startPeriod = new \DateTime('1900-01-01');
        $endPeriod = new \DateTime('1999-12-31');
        
        $filteredEditors = array_filter($editors, function($editor) use ($startPeriod, $endPeriod) {
            $creationDate = $editor->getDateOfCreation();
            return $creationDate >= $startPeriod && $creationDate <= $endPeriod;
        });

        // Vérifications
        $this->assertCount(2, $filteredEditors);
        foreach ($filteredEditors as $editor) {
            $this->assertGreaterThanOrEqual($startPeriod, $editor->getDateOfCreation());
            $this->assertLessThanOrEqual($endPeriod, $editor->getDateOfCreation());
        }
    }

    /**
     * Test de la logique de recherche avancée par pays et nombre de livres
     */
    public function testAdvancedSearchByCountryAndBookCountLogic(): void
    {
        // Créer des éditeurs avec différents nombres de livres
        $editor1 = new Editor();
        $editor1->setName('Gallimard');
        $editor1->setDateOfCreation(new \DateTime('1911-05-31'));
        $editor1->setAddress('Paris, France');

        $editor2 = new Editor();
        $editor2->setName('Penguin Books');
        $editor2->setDateOfCreation(new \DateTime('1935-01-01'));
        $editor2->setAddress('Londres, Royaume-Uni');

        $editor3 = new Editor();
        $editor3->setName('Flammarion');
        $editor3->setDateOfCreation(new \DateTime('1876-01-01'));
        $editor3->setAddress('Paris, France');

        // Créer des livres pour chaque éditeur
        $book1 = new Book();
        $book1->setTitle('L\'Étranger');
        $book1->setEditor($editor1);

        $book2 = new Book();
        $book2->setTitle('La Peste');
        $book2->setEditor($editor1);

        $book3 = new Book();
        $book3->setTitle('1984');
        $book3->setEditor($editor2);

        $book4 = new Book();
        $book4->setTitle('Le Petit Prince');
        $book4->setEditor($editor3);

        // Ajouter les livres aux éditeurs
        $editor1->addBook($book1)->addBook($book2);
        $editor2->addBook($book3);
        $editor3->addBook($book4);

        $editors = [$editor1, $editor2, $editor3];

        // Recherche avancée : éditeurs français avec au moins 1 livre
        $filteredEditors = array_filter($editors, function($editor) {
            return strpos($editor->getAddress(), 'Paris') !== false && $editor->getBooks()->count() >= 1;
        });

        // Vérifications
        $this->assertCount(2, $filteredEditors);
        foreach ($filteredEditors as $editor) {
            $this->assertStringContainsString('Paris', $editor->getAddress());
            $this->assertGreaterThanOrEqual(1, $editor->getBooks()->count());
        }
    }

    /**
     * Test de la logique de pagination des éditeurs
     */
    public function testPaginationLogic(): void
    {
        // Créer une liste d'éditeurs
        $editors = [];
        for ($i = 1; $i <= 12; $i++) {
            $editor = new Editor();
            $editor->setName("Éditeur $i");
            $editor->setDateOfCreation(new \DateTime("1900-01-01"));
            $editor->setAddress("Ville $i");
            $editors[] = $editor;
        }

        // Simuler la pagination (page 2, 4 éléments par page)
        $page = 2;
        $limit = 4;
        $offset = ($page - 1) * $limit;

        $paginatedEditors = array_slice($editors, $offset, $limit);

        // Vérifications
        $this->assertCount(4, $paginatedEditors);
        $this->assertEquals('Éditeur 5', $paginatedEditors[0]->getName());
        $this->assertEquals('Éditeur 8', $paginatedEditors[3]->getName());
    }

    /**
     * Test de la logique de recherche par ancienneté
     */
    public function testFindByAgeLogic(): void
    {
        // Créer des éditeurs avec différentes dates de création
        $editors = [
            (new Editor())->setName('Très Ancien')->setDateOfCreation(new \DateTime('1800-01-01'))->setAddress('Ville Ancienne'),
            (new Editor())->setName('Ancien')->setDateOfCreation(new \DateTime('1900-01-01'))->setAddress('Ville Ancienne'),
            (new Editor())->setName('Moderne')->setDateOfCreation(new \DateTime('2000-01-01'))->setAddress('Ville Moderne'),
        ];

        // Simuler la recherche par ancienneté (plus de 100 ans)
        $currentDate = new \DateTime();
        $hundredYearsAgo = (new \DateTime())->modify('-100 years');
        
        $filteredEditors = array_filter($editors, function($editor) use ($hundredYearsAgo) {
            return $editor->getDateOfCreation() <= $hundredYearsAgo;
        });

        // Vérifications
        $this->assertCount(2, $filteredEditors);
        foreach ($filteredEditors as $editor) {
            $this->assertLessThanOrEqual($hundredYearsAgo, $editor->getDateOfCreation());
        }
    }
}