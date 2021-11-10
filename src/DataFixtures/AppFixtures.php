<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private function makeUser($email, $username, $password): User
    {
        $user = new User();
        $user->setEmail($email)
                ->setPassword($this->encoder->encodePassword($user, $password))
                ->setUsername($username)
                ->setRoles(['ROLE_SUBSCRIBER']);
        return $user;
    }
    
    private function makeCategory($name): Category
    {
        $category = new Category();
        $category->setName($name);
        return $category;
    }
    
    private function makeTrick($user, $createdAt, $category, $title, $content): Trick
    {
        $trick = new Trick();
        $trick->setUser($user);
        $trick->setCreatedAt($createdAt);
        $trick->setTitle($title);
        $trick->setContent($content);
        $trick->setCategory($category);
        return $trick;
    }

    private function makeImage($name, $trick): Image
    {
        $image = new Image();
        $image->setName($name);
        $image->setTrick($trick);
        return $image;
    }

    private function makeVideo($url, $trick): Video
    {
        $video = new Video();
        $video->setUrl($url);
        $video->setTrick($trick);
        return $video;
    }

    public function load(ObjectManager $manager): void
    {
        $manager->persist($this->makeUser('soutenance14@gmail.com', 'soutenance14', 'password'));
        $manager->persist($this->makeUser('soutenance20@gmail.com', 'soutenance20', 'password'));
        $manager->persist($this->makeUser('asmr.recuperer@gmail.com', 'recuperer', 'password'));
        
        $manager->persist($this->makeCategory('Flip'));
        $manager->persist($this->makeCategory('Slide'));
        $manager->persist($this->makeCategory('Rotation'));
        
        // $user, $createdAt, $category, $title, $content

        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
