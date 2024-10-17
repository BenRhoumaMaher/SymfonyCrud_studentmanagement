<?php

namespace App\DataFixtures;

use App\Entity\Classe;
use DateTimeImmutable;
use App\Entity\Student;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $classes = [];
        for ($i = 0; $i < 30; $i++) {
            $class = new Classe();
            $class->setName('Class '.$i);
            $manager->persist($class);
            $classes[] = $class;
        }

        for ($i = 0; $i < 30; $i++) {
            $student = new Student();
            $student->setName('Student '.$i);
            $student->setPlace('Place '.$i);
            $randomClass = $classes[array_rand($classes)];
            $student->setClasse($randomClass);
            $student->setPhone('Phone '.$i);
            $student->setDate(new DateTimeImmutable());
            $manager->persist($student);
        }

        $manager->flush();
    }
}
