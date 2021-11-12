<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Video;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;
    private $manager;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    private function makeUser($email, $username, $password): User
    {
        $user = new User();
        $user->setEmail($email)
                ->setPassword($this->encoder->encodePassword($user, $password))
                ->setUsername($username)
                ->setRoles(['ROLE_SUBSCRIBER']);
        $this->manager->persist($user);
        return $user;
    }
    
    private function makeCategory($name): Category
    {
        $category = new Category();
        $category->setName($name);
        $this->manager->persist($category);
        return $category;
    }
    
    private function makeTrick($user, $createdAt, $category, $title, $content): Trick
    {
        $trick = new Trick();
        $trick->setUser($user);
        $trick->setCreatedAt($createdAt);
        $trick->setTitle($title);
        $trick->setSlug();
        $trick->setContent($content);
        $trick->setCategory($category);
        $this->manager->persist($trick);
        return $trick;
    }

    private function makeImage($name, $trick): Image
    {
        $image = new Image();
        $image->setName($name);
        $image->setTrick($trick);
        $this->manager->persist($image);
        return $image;
    }

    private function makeVideo($url, $trick): Video
    {
        $video = new Video();
        $video->setUrl($url);
        $video->setTrick($trick);
        $this->manager->persist($video);
        return $video;
    }

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;

        $user1 = $this->makeUser('soutenance14@gmail.com', 'soutenance14', 'password');
        // $user2 = $this->makeUser('soutenance20@gmail.com', 'soutenance20', 'password');
        // $user3 =$this->makeUser('test@gmail.com', 'test', 'password');
        
        $category1 = $this->makeCategory('Flip');
        $category2 = $this->makeCategory('Slide');
        // $category3 = $this->makeCategory('Rotation');

        $trick1 = $this->makeTrick($user1, new DateTimeImmutable(), $category1, "titre1", "content1");
        $this->makeImage("blog-icon-e194a09251443323394dbe88e571b064.png", $trick1);
        $this->makeImage("aa.png", $trick1);
        $this->makeVideo("https://www.youtube.com/embed/OK_JCtrrv-c", $trick1);
        
        $trick2 = $this->makeTrick($user1, new DateTimeImmutable(), $category1, "titre2", "content2");
        $this->makeImage("bb.png", $trick2);
        
        $trick3 = $this->makeTrick($user1, new DateTimeImmutable(), $category2, "titre3", "content3");
        $this->makeImage("cc.png", $trick3);
        $this->makeImage("dd.png", $trick3);
        $this->makeImage("ee.png", $trick3);
        $this->makeVideo("https://www.youtube.com/embed/OK_JCtrrv-c", $trick3);
        $this->makeVideo("https://www.youtube.com/embed/OK_JCtrrv-c", $trick3);
        
        $this->manager->flush();
    }
}
