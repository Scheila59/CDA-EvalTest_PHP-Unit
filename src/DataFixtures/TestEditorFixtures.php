<?php

namespace App\DataFixtures;

use App\Entity\Editor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class TestEditorFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['test'];
    }

    public function load(ObjectManager $manager): void
    {
        $defaultEditor = new Editor();
        $defaultEditor->setName('Default Editor');
        $defaultEditor->setDateOfCreation(new \DateTime('1970-01-01'));
        $defaultEditor->setAddress('N/A');
        $manager->persist($defaultEditor);
        $manager->flush();
    }
}