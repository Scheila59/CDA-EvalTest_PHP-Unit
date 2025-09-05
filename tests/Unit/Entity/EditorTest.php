<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Editor;
use App\Entity\Book;
use PHPUnit\Framework\TestCase;

class EditorTest extends TestCase
{
    private Editor $editor;

    protected function setUp(): void
    {
        $this->editor = new Editor();
    }

    public function testEditorCreation(): void
    {
        $this->assertInstanceOf(Editor::class, $this->editor);
        $this->assertNull($this->editor->getId());
        $this->assertInstanceOf(\Doctrine\Common\Collections\Collection::class, $this->editor->getBooks());
        $this->assertCount(0, $this->editor->getBooks());
    }

    public function testSetAndGetName(): void
    {
        $name = 'Gallimard';
        $result = $this->editor->setName($name);
        
        $this->assertInstanceOf(Editor::class, $result);
        $this->assertEquals($name, $this->editor->getName());
    }

    public function testSetAndGetCreationDate(): void
    {
        $creationDate = new \DateTime('1911-05-31');
        $result = $this->editor->setDateOfCreation($creationDate);
        
        $this->assertInstanceOf(Editor::class, $result);
        $this->assertEquals($creationDate, $this->editor->getDateOfCreation());
    }

    public function testSetAndGetHeadOffice(): void
    {
        $headOffice = 'Paris, France';
        $result = $this->editor->setAddress($headOffice);
        
        $this->assertInstanceOf(Editor::class, $result);
        $this->assertEquals($headOffice, $this->editor->getAddress());
    }

    public function testAddBook(): void
    {
        $book = new Book();
        $book->setTitle('Le Petit Prince');

        $result = $this->editor->addBook($book);

        $this->assertInstanceOf(Editor::class, $result);
        $this->assertCount(1, $this->editor->getBooks());
        $this->assertTrue($this->editor->getBooks()->contains($book));
        $this->assertEquals($this->editor, $book->getEditor());
    }

    public function testRemoveBook(): void
    {
        $book = new Book();
        $book->setTitle('Le Petit Prince');

        $this->editor->addBook($book);
        $this->assertCount(1, $this->editor->getBooks());

        $result = $this->editor->removeBook($book);

        $this->assertInstanceOf(Editor::class, $result);
        $this->assertCount(0, $this->editor->getBooks());
        $this->assertFalse($this->editor->getBooks()->contains($book));
        $this->assertNull($book->getEditor());
    }

    public function testFluentInterface(): void
    {
        $creationDate = new \DateTime('1945-01-01');
        
        $result = $this->editor
            ->setName('Flammarion')
            ->setDateOfCreation($creationDate)
            ->setAddress('Paris, France');

        $this->assertSame($this->editor, $result);
    }
}