<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Author;
use App\Entity\Editor;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $author1 = new Author();
        $author1->setFirstName('Antoine');
        $author1->setLastName('de Saint Exupéry');
        $author1->setCountry('France');
        $manager->persist($author1);
        $author2 = new Author();
        $author2->setFirstName('George');
        $author2->setLastName('Orwell');
        $author2->setCountry('Royaume-Uni');
        $manager->persist($author2);
        $author3 = new Author();
        $author3->setFirstName('Joanne');
        $author3->setLastName('Rowling');
        $author3->setCountry('Royaume-Uni');
        $manager->persist($author3);

        // Création des editeurs

        $editor1 = new Editor();
        $editor1->setName('Gallimard');
        $editor1->setDateOfCreation(new \DateTime('1911-01-01'));
        $editor1->setAddress('5 rue Sébastien-Bottin, 75007 Paris');
        $manager->persist($editor1);

        $editor2 = new Editor();
        $editor2->setName('Seuil');
        $editor2->setDateOfCreation(new \DateTime('1935-01-01'));
        $editor2->setAddress('27 rue Jacob, 75006 Paris');
        $manager->persist($editor2);

        $editor3 = new Editor();
        $editor3->setName('Bloomsbury Publishing');
        $editor3->setDateOfCreation(new \DateTime('1986-01-01'));
        $editor3->setAddress('50 Bedford Square, London');
        $manager->persist($editor3);

        // Création des livres
        $book1 = new Book();
        $book1->setTitle('Le Petit Prince');
        $book1->setDescription('L\'histoire d\'un petit prince qui voyage de planète en planète.');
        $book1->setPages(96);
        $book1->setImage('https://encryptedtbn0.gstatic.com/images?q=tbn:ANd9GcSfLtRjalUT26tXdZ3RHH8VRMzD0S0pT-tFDg&s');
        $book1->setAuthor($author1);
        $book1->setEditor($editor1);
        $manager->persist($book1);

        $book2 = new Book();
        $book2->setTitle('1984');
        $book2->setDescription('Un roman dystopique sur la surveillance de masse.');
        $book2->setPages(368);
        $book2->setImage('https://encryptedtbn0.gstatic.com/images?q=tbn:ANd9GcSfLtRjalUT26tXdZ3RHH8VRMzD0S0pT-tFDg&s');
        $book2->setAuthor($author2);
        $book2->setEditor($editor2);
        $manager->persist($book2);

        $book3 = new Book();
        $book3->setTitle('Harry Potter à l\'école des sorciers');
        $book3->setDescription('Le début des aventures du célèbre sorcier.');
        $book3->setPages(320);
        $book3->setImage('https://encryptedtbn0.gstatic.com/images?q=tbn:ANd9GcSfLtRjalUT26tXdZ3RHH8VRMzD0S0pT-tFDg&s');
        $book3->setAuthor($author3);
        $book3->setEditor($editor3);
        $manager->persist($book3);

        $manager->flush();
    }
};
