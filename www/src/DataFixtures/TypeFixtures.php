<?php

namespace App\DataFixtures;

use App\Entity\Type;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TypeFixtures extends Fixture
{
    const TYPES = [
        [
            'id' => '1',
            'name' => 'normal',
        ],
        [
            'id' => '2',
            'name' => 'fighting',
        ],
        [
            'id' => '3',
            'name' => 'flying',
        ],
        [
            'id' => '4',
            'name' => 'poison',
        ],
        [
            'id' => '5',
            'name' => 'ground',
        ],
        [
            'id' => '6',
            'name' => 'rock',
        ],
        [
            'id' => '7',
            'name' => 'bug',
        ],
        [
            'id' => '8',
            'name' => 'ghost',
        ],
        [
            'id' => '9',
            'name' => 'steel',
        ],
        [
            'id' => '10',
            'name' => 'fire',
        ],
        [
            'id' => '11',
            'name' => 'water',
        ],
        [
            'id' => '12',
            'name' => 'grass',
        ],
        [
            'id' => '13',
            'name' => 'electric',
        ],
        [
            'id' => '14',
            'name' => 'psychic',
        ],
        [
            'id' => '15',
            'name' => 'ice',
        ],
        [
            'id' => '16',
            'name' => 'dragon',
        ],
        [
            'id' => '17',
            'name' => 'dark',
        ],
        [
            'id' => '18',
            'name' => 'fairy',
        ],
        [
            'id' => '10001',
            'name' => 'unknown',
        ],
        [
            'id' => '10002',
            'name' => 'shadow',
        ],
    ];

    public function load(ObjectManager $manager)
    {
        foreach (self::TYPES as $type) {
            $typeFixture = new Type();
            $typeFixture->setId($type['id']);
            $typeFixture->setName($type['name']);
            $manager->persist($typeFixture);
        }
        $manager->flush();
    }
}
