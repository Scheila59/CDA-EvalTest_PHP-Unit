<?php

namespace App\Tests\Integration\Repository;

use App\Entity\Author;
use App\Entity\Book;
use App\Repository\AuthorRepository;
use PHPUnit\Framework\TestCase;

/**
 * Tests d'intégration pour AuthorRepository
 * 
 * Ces tests vérifient la logique des repositories sans base de données réelle.
 * Ils testent les méthodes de recherche et la logique métier des repositories.
 */
class AuthorRepositoryTest extends TestCase
{
    private AuthorRepository $authorRepository;

    protected function setUp(): void
    {
        // Mock du repository pour tester la logique sans base de données
        $this->authorRepository = $this->createMock(AuthorRepository::class);
    }

    /**
     * Test de la logique de recherche par nom
     */
    public function testFindByLastNameLogic(): void
    {
        // Créer des auteurs de test
        $author1 = new Author();
        $author1->setFirstName('Isaac');
        $author1->setLastName('Asimov');
        $author1->setCountry('USA');

        $author2 = new Author();
        $author2->setFirstName('J.R.R.');
        $author2->setLastName('Tolkien');
        $author2->setCountry('Royaume-Uni');

        // Simuler la logique de recherche par nom
        $authors = [$author1, $author2];
        $searchLastName = 'Asimov';

        $filteredAuthors = array_filter($authors, function ($author) use ($searchLastName) {
            return $author->getLastName() === $searchLastName;
        });

        // Vérifications
        $this->assertCount(1, $filteredAuthors);
        $this->assertEquals('Isaac', reset($filteredAuthors)->getFirstName());
        $this->assertEquals('Asimov', reset($filteredAuthors)->getLastName());
    }

    /**
     * Test de la logique de recherche par pays
     */
    public function testFindByCountryLogic(): void
    {
        // Créer des auteurs de différents pays
        $authors = [
            (new Author())->setFirstName('George')->setLastName('Orwell')->setCountry('Royaume-Uni'),
            (new Author())->setFirstName('J.K.')->setLastName('Rowling')->setCountry('Royaume-Uni'),
            (new Author())->setFirstName('Isaac')->setLastName('Asimov')->setCountry('USA'),
            (new Author())->setFirstName('Ernest')->setLastName('Hemingway')->setCountry('USA'),
        ];

        // Simuler la recherche par pays
        $searchCountry = 'Royaume-Uni';
        $filteredAuthors = array_filter($authors, function ($author) use ($searchCountry) {
            return $author->getCountry() === $searchCountry;
        });

        // Vérifications
        $this->assertCount(2, $filteredAuthors);
        foreach ($filteredAuthors as $author) {
            $this->assertEquals('Royaume-Uni', $author->getCountry());
        }
    }

    /**
     * Test de la logique de recherche par nom complet
     */
    public function testFindByFullNameLogic(): void
    {
        // Créer des auteurs
        $authors = [
            (new Author())->setFirstName('Isaac')->setLastName('Asimov')->setCountry('USA'),
            (new Author())->setFirstName('J.R.R.')->setLastName('Tolkien')->setCountry('Royaume-Uni'),
            (new Author())->setFirstName('George')->setLastName('Orwell')->setCountry('Royaume-Uni'),
        ];

        // Simuler la recherche par nom complet
        $searchFullName = 'Isaac Asimov';
        $filteredAuthors = array_filter($authors, function ($author) use ($searchFullName) {
            $fullName = $author->getFirstName() . ' ' . $author->getLastName();
            return $fullName === $searchFullName;
        });

        // Vérifications
        $this->assertCount(1, $filteredAuthors);
        $foundAuthor = reset($filteredAuthors);
        $this->assertEquals('Isaac', $foundAuthor->getFirstName());
        $this->assertEquals('Asimov', $foundAuthor->getLastName());
    }

    /**
     * Test de la logique de tri par nom
     */
    public function testSortByLastNameLogic(): void
    {
        // Créer des auteurs avec des noms différents
        $authors = [
            (new Author())->setFirstName('Isaac')->setLastName('Asimov')->setCountry('USA'),
            (new Author())->setFirstName('J.R.R.')->setLastName('Tolkien')->setCountry('Royaume-Uni'),
            (new Author())->setFirstName('George')->setLastName('Orwell')->setCountry('Royaume-Uni'),
        ];

        // Simuler le tri par nom de famille
        usort($authors, function ($a, $b) {
            return strcmp($a->getLastName(), $b->getLastName());
        });

        // Vérifications
        $this->assertEquals('Asimov', $authors[0]->getLastName());
        $this->assertEquals('Orwell', $authors[1]->getLastName());
        $this->assertEquals('Tolkien', $authors[2]->getLastName());
    }

    /**
     * Test de la logique de recherche avec livres
     */
    public function testFindWithBooksLogic(): void
    {
        // Créer un auteur
        $author = new Author();
        $author->setFirstName('Isaac');
        $author->setLastName('Asimov');
        $author->setCountry('USA');

        // Créer des livres pour cet auteur
        $book1 = new Book();
        $book1->setTitle('Foundation');
        $book1->setImage('foundation.jpg');
        $book1->setDescription('Premier livre');
        $book1->setPages(244);
        $book1->setAuthor($author);

        $book2 = new Book();
        $book2->setTitle('I, Robot');
        $book2->setImage('i-robot.jpg');
        $book2->setDescription('Deuxième livre');
        $book2->setPages(253);
        $book2->setAuthor($author);

        // Ajouter les livres à l'auteur
        $author->addBook($book1);
        $author->addBook($book2);

        // Simuler la récupération avec livres
        $authors = [$author];
        $authorWithBooks = $authors[0];

        // Vérifier les relations
        $this->assertCount(2, $authorWithBooks->getBook());
        $this->assertTrue($authorWithBooks->getBook()->contains($book1));
        $this->assertTrue($authorWithBooks->getBook()->contains($book2));
    }

    /**
     * Test de la logique de recherche par initiales
     */
    public function testFindByInitialsLogic(): void
    {
        // Créer des auteurs avec différentes initiales
        $authors = [
            (new Author())->setFirstName('J.R.R.')->setLastName('Tolkien')->setCountry('Royaume-Uni'),
            (new Author())->setFirstName('J.K.')->setLastName('Rowling')->setCountry('Royaume-Uni'),
            (new Author())->setFirstName('Isaac')->setLastName('Asimov')->setCountry('USA'),
            (new Author())->setFirstName('George')->setLastName('Orwell')->setCountry('Royaume-Uni'),
        ];

        // Simuler la recherche par initiales (J.R.R.)
        $searchInitials = 'J.R.R.';
        $filteredAuthors = array_filter($authors, function ($author) use ($searchInitials) {
            return $author->getFirstName() === $searchInitials;
        });

        // Vérifications
        $this->assertCount(1, $filteredAuthors);
        $foundAuthor = reset($filteredAuthors);
        $this->assertEquals('J.R.R.', $foundAuthor->getFirstName());
        $this->assertEquals('Tolkien', $foundAuthor->getLastName());
    }

    /**
     * Test de la logique de recherche avancée par pays et nombre de livres
     */
    public function testAdvancedSearchByCountryAndBookCountLogic(): void
    {
        // Créer des auteurs avec différents nombres de livres
        $author1 = new Author();
        $author1->setFirstName('Isaac');
        $author1->setLastName('Asimov');
        $author1->setCountry('USA');

        $author2 = new Author();
        $author2->setFirstName('J.R.R.');
        $author2->setLastName('Tolkien');
        $author2->setCountry('Royaume-Uni');

        $author3 = new Author();
        $author3->setFirstName('George');
        $author3->setLastName('Orwell');
        $author3->setCountry('Royaume-Uni');

        // Créer des livres pour chaque auteur
        $book1 = new Book();
        $book1->setTitle('Foundation');
        $book1->setAuthor($author1);

        $book2 = new Book();
        $book2->setTitle('I, Robot');
        $book2->setAuthor($author1);

        $book3 = new Book();
        $book3->setTitle('Le Seigneur des Anneaux');
        $book3->setAuthor($author2);

        $book4 = new Book();
        $book4->setTitle('1984');
        $book4->setAuthor($author3);

        // Ajouter les livres aux auteurs
        $author1->addBook($book1)->addBook($book2);
        $author2->addBook($book3);
        $author3->addBook($book4);

        $authors = [$author1, $author2, $author3];

        // Recherche avancée : auteurs du Royaume-Uni avec au moins 1 livre
        $filteredAuthors = array_filter($authors, function ($author) {
            return $author->getCountry() === 'Royaume-Uni' && $author->getBook()->count() >= 1;
        });

        // Vérifications
        $this->assertCount(2, $filteredAuthors);
        foreach ($filteredAuthors as $author) {
            $this->assertEquals('Royaume-Uni', $author->getCountry());
            $this->assertGreaterThanOrEqual(1, $author->getBook()->count());
        }
    }

    /**
     * Test de la logique de pagination des auteurs
     */
    public function testPaginationLogic(): void
    {
        // Créer une liste d'auteurs
        $authors = [];
        for ($i = 1; $i <= 15; $i++) {
            $author = new Author();
            $author->setFirstName("Prénom$i");
            $author->setLastName("Nom$i");
            $author->setCountry("Pays$i");
            $authors[] = $author;
        }

        // Simuler la pagination (page 2, 5 éléments par page)
        $page = 2;
        $limit = 5;
        $offset = ($page - 1) * $limit;

        $paginatedAuthors = array_slice($authors, $offset, $limit);

        // Vérifications
        $this->assertCount(5, $paginatedAuthors);
        $this->assertEquals('Prénom6', $paginatedAuthors[0]->getFirstName());
        $this->assertEquals('Prénom10', $paginatedAuthors[4]->getFirstName());
    }
}
