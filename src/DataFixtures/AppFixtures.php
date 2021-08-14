<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Group;
use App\Entity\Media;
use App\Entity\Picture;
use App\Entity\Trick;
use Bezhanov\Faker\Provider\Commerce;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    protected $hasher;
    protected $slugger;

    public function __construct(UserPasswordHasherInterface $hasher, SluggerInterface $slugger)
    {
        $this->hasher = $hasher;
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager)
    {

        $faker = Factory::create('fr_FR');
        $faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));

        $users = [];
        for ($u = 0; $u < 5; $u++) {
            $user = new User();
            $hash = $this->hasher->hashPassword($user, "password");
            $user->setEmail("user$u@gmail.com")
                ->setPassword($hash)
                ->setNickname($faker->name())
                ->setRoles(['ROLE_USER']);

            $users[] = $user;

            $manager->persist($user);
        }


        for ($g = 0; $g < 5; $g++) {
            $group = new Group;
            $type = ['grabs', 'rotations', 'flips', 'slides', 'one foot tricks'];
            $group->setName($faker->randomElement($type));

            $manager->persist($group);

            for ($t = 0; $t < mt_rand(3, 8); $t++) {
                $trick = new Trick;
                $trick->setName($faker->productName)
                    ->setDescription($faker->text())
                    ->setType($group)
                    ->setOwner($faker->randomElement($users))
                    ->setCreatedAt($faker->dateTimeBetween('-10 months', '-6 months'))
                    ->setUpdatedAt($faker->dateTimeBetween('-5 months'))
                    ->setSlug($this->slugger->slug($trick->getName()));



                for ($m = 0; $m < mt_rand(2, 4); $m++) {
                    $media = new Media;
                    $media
                        ->setAddedBy($faker->randomElement($users))
                        ->setTrick($trick)
                        ->setType('Youtube')
                        ->setLink('https://www.youtube.com/embed/gbHU6J6PRRw');
                    $manager->persist($media);
                }

                for ($p = 0; $p < mt_rand(3, 5); $p++) {
                    $picture = new Picture;
                    $picture
                        ->setAddedBy($faker->randomElement($users))
                        ->setTrick($trick)
                        ->setPath(mt_rand(1, 7) . '.jpg');
                    $manager->persist($picture);
                    if ($p === 0) {
                        $trick->setMainPicture($picture);
                    }
                }

                for ($c = 0; $c < mt_rand(5, 9); $c++) {
                    $comment = new Comment;
                    $comment->setContent($faker->text())
                        ->setTrick($trick)
                        ->setAuthor($faker->randomElement($users))
                        ->setCreatedAt($faker->dateTimeBetween('-6 months'));

                    $manager->persist($comment);
                }
                $manager->persist($trick);
            }
        }



        $manager->flush();
    }
}
