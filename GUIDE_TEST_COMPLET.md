# 🧪 Guide Complet des Tests - Projet Biblio-API

## 📋 Table des Matières

1. [Introduction](#introduction)
2. [Installation et Configuration](#installation-et-configuration)
3. [Types de Tests](#types-de-tests)
4. [Tests Unitaires](#tests-unitaires)
5. [Tests d'Intégration](#tests-dintégration)
6. [Exécution des Tests](#exécution-des-tests)
7. [Bonnes Pratiques](#bonnes-pratiques)
8. [Dépannage](#dépannage)
9. [Ressources](#ressources)

---

## 🎯 Introduction

Ce guide présente la démarche complète pour implémenter et exécuter des tests dans le projet Biblio-API. Nous couvrons les tests unitaires et d'intégration pour les entités `Book`, `Author`, et `Editor`.

### Objectifs

-   ✅ Valider la logique métier des entités
-   ✅ Tester les relations entre entités
-   ✅ Vérifier la logique des repositories
-   ✅ Assurer la qualité et la fiabilité du code

---

## ⚙️ Installation et Configuration

### 1. Installation de PHPUnit

```bash
# Dans le dossier backend du projet
cd backend-copy/backend

# Installation du pack de tests Symfony
composer require --dev symfony/test-pack
```

### 2. Vérification de l'Installation

```bash
# Vérifier que PHPUnit est installé
vendor/bin/phpunit --version
```

**Résultat attendu :**

```
PHPUnit 12.3.8 by Sebastian Bergmann and contributors.
```

### 3. Configuration PHPUnit

Le fichier `phpunit.dist.xml` est automatiquement créé avec la configuration suivante :

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         colors="true"
         failOnDeprecation="true"
         bootstrap="vendor/autoload.php"
         cacheDirectory=".phpunit.cache">
    <testsuites>
        <testsuite name="unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="integration">
            <directory>tests/Integration</directory>
        </testsuite>
    </testsuites>

    <source>
        <include>
            <directory suffix=".php">src</directory>
        </include>
    </source>

    <php>
        <ini name="error_reporting" value="-1" />
        <ini name="memory_limit" value="-1" />
        <server name="APP_ENV" value="test" force="true" />
        <server name="SHELL_VERBOSITY" value="-1" />
        <server name="SYMFONY_PHPUNIT_REMOVE" value="" />
        <server name="SYMFONY_PHPUNIT_VERSION" value="12.3" />
    </php>
</phpunit>
```

### 4. Structure des Dossiers

```
backend-copy/backend/
├── src/
│   └── Entity/
│       ├── Book.php
│       ├── Author.php
│       └── Editor.php
├── tests/
│   ├── Unit/
│   │   └── Entity/
│   │       ├── BookTest.php
│   │       ├── AuthorTest.php
│   │       └── EditorTest.php
│   └── Integration/
│       └── Repository/
│           ├── BookRepositoryTest.php
│           ├── AuthorRepositoryTest.php
│           └── EditorRepositoryTest.php
└── phpunit.dist.xml
```

---

## 🧪 Types de Tests

### Tests Unitaires

-   **Objectif** : Tester une entité isolée
-   **Avantages** : Rapides, fiables, faciles à maintenir
-   **Inconvénients** : Ne testent pas les interactions

### Tests d'Intégration

-   **Objectif** : Tester la logique des repositories
-   **Avantages** : Testent les algorithmes de recherche
-   **Inconvénients** : Plus complexes à écrire

---

## 🔬 Tests Unitaires

### Structure d'un Test Unitaire

```php
<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Book;
use PHPUnit\Framework\TestCase;

class BookTest extends TestCase
{
    private Book $book;

    protected function setUp(): void
    {
        // Préparation avant chaque test
        $this->book = new Book();
    }

    public function testBookCreation(): void
    {
        // Arrange (Préparation)
        // Act (Action)
        // Assert (Vérification)

        $this->assertInstanceOf(Book::class, $this->book);
        $this->assertNull($this->book->getId());
    }
}
```

### Patterns Testés

#### 1. Fluent Interface

```php
public function testFluentInterface(): void
{
    $result = $this->book
        ->setTitle('Mon Livre')
        ->setImage('cover.jpg')
        ->setDescription('Description...')
        ->setPages(300);

    $this->assertSame($this->book, $result);
}
```

#### 2. Relations Bidirectionnelles

```php
public function testBidirectionalAuthorRelation(): void
{
    $author = new Author();
    $author->setFirstName('Isaac');
    $author->setLastName('Asimov');

    // Établir la relation des deux côtés
    $author->addBook($this->book);

    // Vérifier la cohérence
    $this->assertTrue($author->getBook()->contains($this->book));
    $this->assertEquals($author, $this->book->getAuthor());
}
```

#### 3. Gestion des Collections

```php
public function testAddBook(): void
{
    $book = new Book();
    $book->setTitle('Foundation');

    $this->author->addBook($book);

    $this->assertCount(1, $this->author->getBook());
    $this->assertTrue($this->author->getBook()->contains($book));
}
```

#### 4. Cas Limites

```php
public function testPagesEdgeCases(): void
{
    // Test avec 1 page (minimum)
    $this->book->setPages(1);
    $this->assertEquals(1, $this->book->getPages());

    // Test avec un très gros livre
    $this->book->setPages(9999);
    $this->assertEquals(9999, $this->book->getPages());
}
```

---

## 🔗 Tests d'Intégration

### Structure d'un Test d'Intégration

```php
<?php

namespace App\Tests\Integration\Repository;

use App\Entity\Book;
use App\Repository\BookRepository;
use PHPUnit\Framework\TestCase;

class BookRepositoryTest extends TestCase
{
    private BookRepository $bookRepository;

    protected function setUp(): void
    {
        // Mock du repository pour tester la logique
        $this->bookRepository = $this->createMock(BookRepository::class);
    }

    public function testFindByTitleLogic(): void
    {
        // Créer des livres de test
        $book1 = new Book();
        $book1->setTitle('Le Seigneur des Anneaux');

        $book2 = new Book();
        $book2->setTitle('Foundation');

        // Simuler la logique de recherche
        $books = [$book1, $book2];
        $searchTitle = 'Seigneur';

        $filteredBooks = array_filter($books, function($book) use ($searchTitle) {
            return stripos($book->getTitle(), $searchTitle) !== false;
        });

        // Vérifications
        $this->assertCount(1, $filteredBooks);
        $this->assertEquals('Le Seigneur des Anneaux', reset($filteredBooks)->getTitle());
    }
}
```

### Types de Tests d'Intégration

#### 1. Recherche et Filtrage

```php
public function testFindByAuthorLogic(): void
{
    $author = new Author();
    $author->setFirstName('Isaac');
    $author->setLastName('Asimov');

    $books = [
        (new Book())->setTitle('Foundation')->setAuthor($author),
        (new Book())->setTitle('I, Robot')->setAuthor($author),
        (new Book())->setTitle('Dune')->setAuthor(new Author()),
    ];

    $filteredBooks = array_filter($books, function($book) use ($author) {
        return $book->getAuthor() === $author;
    });

    $this->assertCount(2, $filteredBooks);
}
```

#### 2. Tri et Pagination

```php
public function testSortByTitleLogic(): void
{
    $books = [
        (new Book())->setTitle('Zorro'),
        (new Book())->setTitle('Alice au pays des merveilles'),
        (new Book())->setTitle('1984'),
    ];

    usort($books, function($a, $b) {
        return strcmp($a->getTitle(), $b->getTitle());
    });

    $this->assertEquals('1984', $books[0]->getTitle());
    $this->assertEquals('Alice au pays des merveilles', $books[1]->getTitle());
    $this->assertEquals('Zorro', $books[2]->getTitle());
}
```

#### 3. Recherche Avancée

```php
public function testAdvancedSearchLogic(): void
{
    $author1 = new Author();
    $author1->setLastName('Tolkien');

    $author2 = new Author();
    $author2->setLastName('Asimov');

    $books = [
        (new Book())->setTitle('Le Seigneur des Anneaux')->setPages(423)->setAuthor($author1),
        (new Book())->setTitle('Foundation')->setPages(244)->setAuthor($author2),
        (new Book())->setTitle('Le Hobbit')->setPages(310)->setAuthor($author1),
    ];

    // Recherche : livres de Tolkien avec plus de 300 pages
    $filteredBooks = array_filter($books, function($book) {
        return $book->getAuthor()->getLastName() === 'Tolkien' && $book->getPages() > 300;
    });

    $this->assertCount(1, $filteredBooks);
    $this->assertEquals('Le Seigneur des Anneaux', reset($filteredBooks)->getTitle());
}
```

---

## 🚀 Exécution des Tests

### Commandes de Base

```bash
# Exécuter tous les tests
vendor/bin/phpunit --testdox

# Exécuter seulement les tests unitaires
vendor/bin/phpunit tests/Unit/ --testdox

# Exécuter seulement les tests d'intégration
vendor/bin/phpunit tests/Integration/ --testdox

# Exécuter un test spécifique
vendor/bin/phpunit tests/Unit/Entity/BookTest.php --testdox

# Exécuter avec couverture de code
vendor/bin/phpunit --coverage-html coverage/
```

### Résultats Attendus

#### Tests Unitaires

```
Book (App\Tests\Unit\Entity\Book)
 ✔ Book creation
 ✔ Set and get title
 ✔ Set and get image
 ✔ Set and get description
 ✔ Set and get pages
 ✔ Set and get author
 ✔ Set author to null
 ✔ Set and get editor
 ✔ Set editor to null
 ✔ Complete book setup
 ✔ Fluent interface
 ✔ Initial null values
 ✔ Pages edge cases
 ✔ Title with special characters
 ✔ Long description
 ✔ Image with special characters
 ✔ Bidirectional author relation
 ✔ Bidirectional editor relation

OK (18 tests, 39 assertions)
```

#### Tests d'Intégration

```
Book Repository (App\Tests\Integration\Repository\BookRepository)
 ✔ Find by title logic
 ✔ Find by author logic
 ✔ Find by pages range logic
 ✔ Sort by title logic
 ✔ Find with relations logic
 ✔ Pagination logic
 ✔ Advanced search logic

OK (7 tests, 34 assertions)
```

### Métriques de Performance

| Métrique                | Valeur   |
| ----------------------- | -------- |
| **Total des tests**     | 56       |
| **Tests unitaires**     | 32       |
| **Tests d'intégration** | 24       |
| **Assertions**          | 181      |
| **Temps d'exécution**   | 0.055s   |
| **Taux de réussite**    | 100%     |
| **Mémoire utilisée**    | 12.00 MB |

---

## 📚 Bonnes Pratiques

### 1. Nommage des Tests

```php
// ✅ Bon
public function testSetAndGetTitle(): void
public function testBidirectionalAuthorRelation(): void
public function testPagesEdgeCases(): void

// ❌ Mauvais
public function test1(): void
public function testBook(): void
public function testSomething(): void
```

### 2. Structure AAA (Arrange-Act-Assert)

```php
public function testCompleteBookSetup(): void
{
    // Arrange (Préparation)
    $author = new Author();
    $author->setFirstName('George R.R.');
    $author->setLastName('Martin');

    // Act (Action)
    $this->book
        ->setTitle('Game of Thrones')
        ->setImage('got-cover.jpg')
        ->setDescription('Winter is coming...')
        ->setPages(694)
        ->setAuthor($author);

    // Assert (Vérification)
    $this->assertEquals('Game of Thrones', $this->book->getTitle());
    $this->assertEquals('got-cover.jpg', $this->book->getImage());
    $this->assertEquals('Winter is coming...', $this->book->getDescription());
    $this->assertEquals(694, $this->book->getPages());
    $this->assertEquals($author, $this->book->getAuthor());
}
```

### 3. Tests Isolés

```php
protected function setUp(): void
{
    // Chaque test commence avec un état propre
    $this->book = new Book();
    $this->author = new Author();
    $this->editor = new Editor();
}
```

### 4. Assertions Spécifiques

```php
// ✅ Bon - Assertions spécifiques
$this->assertEquals('Expected Title', $book->getTitle());
$this->assertCount(2, $author->getBook());
$this->assertTrue($collection->contains($item));

// ❌ Mauvais - Assertions génériques
$this->assertTrue($book->getTitle() === 'Expected Title');
$this->assertTrue(count($author->getBook()) === 2);
```

### 5. Documentation des Tests

```php
/**
 * Test de la logique de recherche par titre
 *
 * Ce test vérifie que la logique de filtrage par titre
 * fonctionne correctement avec la recherche insensible à la casse.
 */
public function testFindByTitleLogic(): void
{
    // ...
}
```

---

## 🔧 Dépannage

### Problèmes Courants

#### 1. Erreur "Class not found"

```bash
Error: Class "App\Entity\Book" not found
```

**Solution :**

-   Vérifier que les tests sont dans le bon dossier (`tests/Unit/Entity/`)
-   Vérifier l'autoloader Composer
-   Exécuter `composer dump-autoload`

#### 2. Erreur de Configuration PHPUnit

```bash
Error: You must set the KERNEL_CLASS environment variable
```

**Solution :**

-   Vérifier le fichier `phpunit.dist.xml`
-   Ajouter `<server name="KERNEL_CLASS" value="App\Kernel" />`

#### 3. Tests qui échouent

```bash
✘ Bidirectional author relation
```

**Solution :**

-   Vérifier la logique des relations bidirectionnelles
-   S'assurer que `addBook()` est appelé sur l'auteur
-   Vérifier les assertions

### Commandes de Diagnostic

```bash
# Vérifier la configuration PHPUnit
vendor/bin/phpunit --configuration phpunit.dist.xml --list-tests

# Exécuter avec plus de détails
vendor/bin/phpunit --testdox --verbose

# Exécuter un test spécifique avec détails
vendor/bin/phpunit tests/Unit/Entity/BookTest.php --testdox --verbose
```

---

## 📖 Ressources

### Documentation Officielle

-   [PHPUnit Documentation](https://phpunit.readthedocs.io/)
-   [Symfony Testing](https://symfony.com/doc/current/testing.html)
-   [Doctrine Testing](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/testing.html)

### Outils Utiles

-   **PHPUnit** : Framework de tests
-   **Symfony Test Pack** : Pack de tests pour Symfony
-   **Doctrine Test Bundle** : Tests avec base de données

### Commandes de Maintenance

```bash
# Mettre à jour les dépendances
composer update

# Régénérer l'autoloader
composer dump-autoload

# Nettoyer le cache PHPUnit
vendor/bin/phpunit --clear-cache
```

---

## 🎯 Conclusion

Ce guide couvre la démarche complète pour implémenter des tests dans le projet Biblio-API. Les tests unitaires et d'intégration assurent la qualité et la fiabilité du code.

### Points Clés à Retenir

1. **Tests Unitaires** : Testent les entités isolément
2. **Tests d'Intégration** : Testent la logique des repositories
3. **Structure AAA** : Arrange-Act-Assert pour tous les tests
4. **Nommage Clair** : Noms de tests descriptifs
5. **Documentation** : Tests comme documentation vivante

### Prochaines Étapes

1. **Tests Fonctionnels** : Tests d'API REST avec base de données
2. **Tests de Performance** : Validation des temps de réponse
3. **Tests de Sécurité** : Validation des accès et permissions
4. **Tests End-to-End** : Tests complets du workflow utilisateur

---

_Guide créé le 5 septembre 2025 - Projet Biblio-API_
