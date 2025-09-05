<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Author;
use App\Entity\Book;
use PHPUnit\Framework\TestCase;

class AuthorTest extends TestCase
{
    private Author $author;

    protected function setUp(): void
    {
        $this->author = new Author();
    }

    public function testAuthorCreation(): void
    {
        $this->assertInstanceOf(Author::class, $this->author);
        $this->assertNull($this->author->getId());
        $this->assertInstanceOf(\Doctrine\Common\Collections\Collection::class, $this->author->getBook());
        $this->assertCount(0, $this->author->getBook());
    }

    public function testSetAndGetFirstName(): void
    {
        $firstName = 'J.R.R.';
        $result = $this->author->setFirstName($firstName);
        
        $this->assertInstanceOf(Author::class, $result);
        $this->assertEquals($firstName, $this->author->getFirstName());
    }

    public function testSetAndGetLastName(): void
    {
        $lastName = 'Tolkien';
        $result = $this->author->setLastName($lastName);
        
        $this->assertInstanceOf(Author::class, $result);
        $this->assertEquals($lastName, $this->author->getLastName());
    }

    public function testSetAndGetCountry(): void
    {
        $country = 'Royaume-Uni';
        $result = $this->author->setCountry($country);
        
        $this->assertInstanceOf(Author::class, $result);
        $this->assertEquals($country, $this->author->getCountry());
    }

    public function testAddBook(): void
    {
        $book = new Book();
        $book->setTitle('Le Hobbit');

        $result = $this->author->addBook($book);

        $this->assertInstanceOf(Author::class, $result);
        $this->assertCount(1, $this->author->getBook());
        $this->assertTrue($this->author->getBook()->contains($book));
        $this->assertEquals($this->author, $book->getAuthor());
    }

    public function testRemoveBook(): void
    {
        $book = new Book();
        $book->setTitle('Le Hobbit');

        $this->author->addBook($book);
        $this->assertCount(1, $this->author->getBook());

        $result = $this->author->removeBook($book);

        $this->assertInstanceOf(Author::class, $result);
        $this->assertCount(0, $this->author->getBook());
        $this->assertFalse($this->author->getBook()->contains($book));
        $this->assertNull($book->getAuthor());
    }

    public function testFluentInterface(): void
    {
        $result = $this->author
            ->setFirstName('George')
            ->setLastName('Orwell')
            ->setCountry('Royaume-Uni');

        $this->assertSame($this->author, $result);
    }
}