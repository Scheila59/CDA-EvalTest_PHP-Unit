<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Book;
use App\Entity\Author;
use App\Entity\Editor;
use PHPUnit\Framework\TestCase;

class BookTest extends TestCase
{
  private Book $book;

  protected function setUp():void{
    // Prépare un livre frais pour chaque test 
    $this->book = new Book();
  }
  /**
   * Test de création basique d'un livre
   */
  public function testBookCreation():void{
    $this->assertInstanceOf(Book::class, $this->book);
    $this->assertNull($this->book->getId());
  }

  /**
   * Test des setters et getters pour le titre
   */
  Public function testSetAndGetTitle():void {
    $title = 'Le Seigneur des Anneaux';
    $result = $this->book->setTitle($title);

    // Vérifier la fluent interface (retour de $this)
    $this->assertInstanceOf(Book::class, $result);
    $this->assertEquals($title, $this->book->getTitle());
  }

  /**
   * Test des setters et getters pour l'image
   */
  public function testSetAndGetImage():void{
    $image = 'tolkien-seigneur-anneaux.jpg';

    $result = $this->book->setImage($image);

    $this->assertInstanceOf(Book::class, $result);
    $this->assertEquals($image, $this->book->getImage());
  }

  /**
   * Test des setters et getters pour la description
   */
  public function testSetAndGetDescription():void{
    $description = 'Un hobbit part à l\'aventure pour détruire un anneau maléfique...';

    $result = $this->book->setDescription($description);

    $this->assertInstanceOf(Book::class, $result);
    $this->assertEquals($description, $this->book->getDescription());
  }

  /**
   * Test des setters et getters pour le nombre de pages 
   */
  public function testSetAndGetPages():void{
    $pages = 423 ;
    $result = $this->book->setPages($pages);

    $this->assertInstanceOf(Book::class, $result);
    $this->assertEquals($pages, $this->book->getPages());
  }

  /**
   * Test de la relation avec Author
   */
  public function testSetAndGetAuthor(): void
  {
    $author = new Author();
    $author->setFirstName('J.R.R.');
    $author->setLastName('Tolkien');
    $result = $this->book->setAuthor($author);
    $this->assertInstanceOf(Book::class, $result);
    $this->assertEquals($author, $this->book->getAuthor());
  }

  /**
   * Test qu'on peut mettre l'auteur à null
   */
  public function testSetAuthorToNull(): void
  {
  // D'abord on met un auteur
  $author = new Author();
  $this->book->setAuthor($author);
  // Puis on le remet à null
  $this->book->setAuthor(null);
  $this->assertNull($this->book->getAuthor());
  }

  /**
   * Test complet: créer un livre avec toutes ses propriétés
   */
  public function testCompleteBookSetup():void{
    $author = new Author();
    $author->setFirstName('George R.R.');
    $author->setLastName('Martin');

    $this->book
    ->setTitle('Game of Thrones')
    ->setImage('got-cover.jpg')
    ->setDescription('Winter is coming...')
    ->setPages(694)
    ->setAuthor($author);

    $this->assertEquals('Game of Thrones', $this->book->getTitle());
    $this->assertEquals('got-cover.jpg', $this->book->getImage());
    $this->assertEquals('Winter is coming...', $this->book->getDescription());
    $this->assertEquals(694, $this->book->getPages());
    $this->assertEquals($author, $this->book->getAuthor());
    $this->assertEquals('George R.R. Martin', $this->book->getAuthor()->getFirstName() . ' ' . $this->book->getAuthor()->getLastName());
  }

    /**
   * Test avec des données vides/nulles
   */
  public function testInitialNullValues(): void
  {
  $book = new Book();
  $this->assertNull($book->getTitle());
  $this->assertNull($book->getImage());
  $this->assertNull($book->getDescription());
  $this->assertNull($book->getPages());
  $this->assertNull($book->getAuthor());
  }

  /**
   * Test aves des valeurs limites pour les pages
   */
  public function testPagesEdgeCases(): void
  {
  // Test avec 1 page (minimum)
  $this->book->setPages(1);
  $this->assertEquals(1, $this->book->getPages());
  // Test avec un très gros livre
  $this->book->setPages(9999);
  $this->assertEquals(9999, $this->book->getPages());
  }

  /**
   * Test avec des caractères spéciaux dans le titre
   */
  public function testTitleWithSpecialCharacters(): void
  {
  $specialTitle = "L'Être & l'Néant: Essai d'ontologie phénoménologique";
  $this->book->setTitle($specialTitle);
  $this->assertEquals($specialTitle, $this->book->getTitle());
  }
  
  /**
   * Test avec une longue description
   */
  public function testLongDescription(): void
  {
  $longDescription = str_repeat('Lorem ipsum dolor sit amet. ', 100);
  $this->book->setDescription($longDescription);
  $this->assertEquals($longDescription, $this->book->getDescription());
  $this->assertGreaterThan(1000, strlen($this->book->getDescription()));
  }
}