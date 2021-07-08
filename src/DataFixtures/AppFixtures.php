<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    protected $hasher;
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager)
    {

        $faker = Factory::create('fr_FR');

        for ($u = 0; $u < 5; $u++) {
            $user = new User();
            $hash = $this->hasher->hashPassword($user, "password");
            $user->setEmail("user$u@gmail.com")
                ->setPassword($hash)
                ->setNickname($faker->name())
                ->setProfilePic($faker->imageUrl);

            $manager->persist($user);
        }
        $manager->flush();
    }
}
