<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
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
    private $users;
    private $commentsContent;

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
    
    private function makeTrick($category, $title, $content): Trick
    {
        $trick = new Trick();
        $trick->setUser($this->randUser());
        $month = rand(1, 10);
        $day = rand(1, 29);// for not probleme with february
        $trick->setCreatedAt(new DateTimeImmutable("2021-".$month."-".$day));
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
    
    private function makeRandComment($trick): Comment
    {
        $comment = new Comment();
        $comment->setContenu($this->randCommentContent());
        $comment->setUser($this->randUser());
        $comment->setTrick($trick);
        $month = rand(1, 10);
        $day = rand(1, 29);// for not probleme with february
        $comment->setCreatedAt(new DateTimeImmutable("2021-".$month."-".$day));
        $this->manager->persist($comment);
        return $comment;
    }

    private function randUser(): User
    {
        return $this->users[rand(0, count($this->users)-1)];
    }
    
    private function randCommentContent(): String
    {
        return $this->commentsContent[rand(0, count($this->commentsContent)-1)];
    }

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        /**USER */
        $user1 = $this->makeUser('soutenance14@gmail.com', 'soutenance14', 'password');
        $user2 = $this->makeUser('soutenance20@gmail.com', 'soutenance20', 'password');
        $user3 =$this->makeUser('test@gmail.com', 'test', 'password');
        $user4 =$this->makeUser('test2@gmail.com', 'test2', 'password');
        $user5 =$this->makeUser('test3@gmail.com', 'test3', 'password');
        //instanciate array users
        $this->users = [$user1, $user2, $user3, $user4, $user5];
       
        /**CATEGORY */
        $category1 = $this->makeCategory('Grabs');
        $category2 = $this->makeCategory('Flip');
        $category3 = $this->makeCategory('Slide');

        /**TRICK + MEDIA */
        $trick1 = $this->makeTrick($category1, "Stalefish", "Saisie de la carre backside de la planche entre les deux pieds avec la main arrière.");
        $this->makeImage("stalefish1.jpg", $trick1);
        $this->makeImage("stalefish2.jpg", $trick1);
        $this->makeVideo("https://www.youtube.com/embed/0Oez89EoE_c", $trick1);
        
        $trick2 = $this->makeTrick($category1, "backside rodeo 1080", "Trois tours avec une rotation désaxée (Rodeo).");
        $this->makeImage("backside1080.jpg", $trick2);
        $this->makeVideo("https://www.youtube.com/embed/vquZvxGMJT0", $trick2);
        
        $trick3 = $this->makeTrick($category2, "Frontflip", "Rotation en avant.");
        $this->makeImage("frontflip1.jpg", $trick3);
        $this->makeImage("frontflip2.jpg", $trick3);
        $this->makeImage("frontflip3.jpg", $trick3);

        $this->makeVideo("https://www.youtube.com/embed/eGJ8keB1-JM", $trick3);
        $this->makeVideo("https://www.youtube.com/embed/xhvqu2XBvI0", $trick3);
        
        $trick4 = $this->makeTrick($category2, "Backflip", "Rotation en arrière.");
        $this->makeImage("backflip1.jpg", $trick4);
        $this->makeImage("backflip2.jpg", $trick4);

        $this->makeVideo("https://www.youtube.com/embed/SlhGVnFPTDE", $trick4);
        $this->makeVideo("https://www.youtube.com/embed/arzLq-47QFA", $trick4);
        
        $trick5 = $this->makeTrick($category3, "Cork", "Un cork est une rotation horizontale plus ou moins désaxée, 
        selon un mouvement d'épaules effectué juste au moment du saut.");
        $this->makeImage("cork1.jpg", $trick5);
        $this->makeImage("cork2.jpg", $trick5);
        $this->makeVideo("https://www.youtube.com/embed/FMHiSF0rHF8", $trick5);
        
        $trick6 = $this->makeTrick($category2, "Rodeo", "Le rodeo est une rotation désaxée, qui se reconnaît par son aspect vrillé.");
        $this->makeImage("rodeo1.jpg", $trick6);
        $this->makeVideo("https://www.youtube.com/embed/vf9Z05XY79A", $trick6);
        
        $trick7 = $this->makeTrick($category3, "Nose Slide", "Un nose slide consiste à glisser sur une barre de slide avec l'avant de la planche sur la barre.");
        $this->makeImage("noseslide.jpg", $trick7);
       
        $trick8 = $this->makeTrick($category3, "Tail Slide", "Un tail slide consiste à glisser sur une barre de slide avec l'arrière de la planche sur la barre.");
        $this->makeImage("tailslide.jpg", $trick8);
        
        $trick9 = $this->makeTrick($category1, "Truck Driver", "Saisie du carre avant et carre arrière avec chaque main (comme tenir un volant de voiture)");
        $this->makeImage("truckdriver.jpg", $trick9);
        
        $trick10 = $this->makeTrick($category1, "Mute", "Saisie de la carre frontside de la planche entre les deux pieds avec la main avant");
        // $this->makeImage("mute.jpg", $trick10);
        
        $trick11 = $this->makeTrick($category2, "360", "Un tour complet en effectuant une rotation horizontale pendant le saut");
        $this->makeImage("360.jpg", $trick11);
        
        $trick12 = $this->makeTrick($category2, "720", "Deux tours complets en effectuant une rotation horizontale pendant le saut");
        $this->makeImage("720.jpg", $trick12);
        
        $trick13 = $this->makeTrick($category2, "1080", "Trois tours complets en effectuant une rotation horizontale pendant le saut");
        $this->makeImage("1080.jpg", $trick13);
        
        $trick14 = $this->makeTrick($category1, "Rocket air", "Before you challenge this trick, 
        be sure to check the following points in advance.");
        $this->makeImage("rocketair.jpg", $trick14);
        $this->makeVideo("ySVGdt_hom4", $trick14);

        $this->commentsContent = [
            "C'est jolie", "Pas mal", "Beau mouvement", "Très instructif",
            "Je ne suis pas d'accord", "Pourquoi pas", "Etonnant", "Remarquable",
            "Le meilleur des mouvements", "Plus qu'intéressant", "Je ne connaissais pas ce trick", "merci beaucoup pour cet article",
            "Magnifique", "Très difficile", "De bonnse sensations", "J'aime bien",
            "Splendide", "Très physique", "Le matériel requis coûte cher", "C'est quoi ça?",
        ];

        $this->makeRandComment($trick1);
        $this->makeRandComment($trick1);
        $this->makeRandComment($trick1);
        $this->makeRandComment($trick1);
        // trick2 backside rodeo 1080 pas de commentaire
        $this->makeRandComment($trick3);
        $this->makeRandComment($trick4);
        $this->makeRandComment($trick4);
        $this->makeRandComment($trick4);
        $this->makeRandComment($trick5);
        $this->makeRandComment($trick5);
        $this->makeRandComment($trick6);
        $this->makeRandComment($trick6);
        $this->makeRandComment($trick6);
        $this->makeRandComment($trick6);
        $this->makeRandComment($trick6);
        $this->makeRandComment($trick6);
        $this->makeRandComment($trick7);
        $this->makeRandComment($trick7);
        $this->makeRandComment($trick8);
        $this->makeRandComment($trick9);
        $this->makeRandComment($trick9);
        $this->makeRandComment($trick9);
        $this->makeRandComment($trick9);
        $this->makeRandComment($trick10);
        $this->makeRandComment($trick11);
        $this->makeRandComment($trick11);
        $this->makeRandComment($trick12);
        $this->makeRandComment($trick12);
        $this->makeRandComment($trick12);
        $this->makeRandComment($trick13);
        $this->makeRandComment($trick13);
        $this->makeRandComment($trick13);
        $this->makeRandComment($trick13);
        $this->makeRandComment($trick14);
        $this->makeRandComment($trick14);
       
        $this->manager->flush();
    }
}
