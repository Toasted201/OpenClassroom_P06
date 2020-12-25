<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Video;
use App\Repository\CategoryRepository;
use App\Repository\TrickRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DemoFixtures extends Fixture
{    
    public function load(ObjectManager $manager)
    {
        //create categories
        $category=[];
        $categoriesTitle = ['grabs', 'rotations', 'flips', 'rotations désaxées', 'slides', 'one foot tricks', 'old school'];
        foreach ($categoriesTitle as $categoryTitle){
            $category = new Category();
            $category->setTitle($categoryTitle);
            $manager->persist($category);
        }
        $manager->flush();

        //add first user
        $userTest = new User();
        $userTest->setUsername('BobDoe')
        ->setEmail('bob@doe.com')
        ->setPassword('pass')
        ->setRoles([])
        ->setIsVerified('1')
        ->setCreatedAt(new \DateTime());
        $manager->persist($userTest);

        //add Tricks Demo
        $tricksData = [];
        $tricksData = [
            ['title' =>'mute',
            'description' => 'saisie de la carre frontside de la planche entre les deux pieds avec la main avant',
            'publish' => '1'],
            ['title' =>'sad',
            'description' => 'saisie de la carre backside de la planche, entre les deux pieds, avec la main avant',
            'publish'=> '1'],
            ['title' =>'indy',
            'description' => 'saisie de la carre frontside de la planche, entre les deux pieds, avec la main arrière',
            'publish'=> '1'],
            ['title' =>'stalefish',
            'description' => 'saisie de la carre backside de la planche entre les deux pieds avec la main arrière',
            'publish'=> '1'],            
            ['title' =>'tail grab',
            'description' => 'saisie de la partie arrière de la planche, avec la main arrière',
            'publish'=> '1'],
            ['title' =>'nose grab',
            'description' => 'saisie de la partie avant de la planche, avec la main avant',
            'publish'=> '1'],
            ['title' =>'japan',
            'description' => 'saisie de l\'avant de la planche, avec la main avant, du côté de la carre frontside.',
            'publish'=> '1'],
            ['title' =>'seat belt',
            'description' => 'saisie du carre frontside à l\'arrière avec la main avant',
            'publish'=> '1'],
            ['title' =>'truck driver',
            'description' => 'saisie du carre avant et carre arrière avec chaque main (comme tenir un volant de voiture)',
            'publish'=> '1'],
            ['title' =>'crail',
            'description' => 'La main arrière grab la carre front devant la fix avant. La jambe arrière doit etre tendue',
            'publish'=> '1'],
            ['title' =>'test',
            'description' => 'description test',
            'publish'=> '0'],
        ]; 		

        $trick = new Trick();
        
        /** @var CategoryRepository */
        $categoryRepository = $manager->getRepository(Category::class);
        $categoryGrab = $categoryRepository->findOneByTitle('grabs');
    
        $date = new \DateTime();
        
        foreach ($tricksData as $trickData){           
            $trick = new Trick;
            $trick->setTitle($trickData['title'])
                ->setDescription($trickData['description'])
                ->setPublish($trickData['publish'])
                ->setCreatedAt($date)
                ->setUser($userTest)
                ->setCategory($categoryGrab);
            $manager->persist($trick);
        }

        $manager->flush();

        //add tricks Test
        for ($i = 0; $i < 50; $i++) {
            $trickTest = new Trick();
            $trickTest->setTitle('Titre ' . $i)
                    ->setDescription('description ' .  $i)
                    ->setPublish('1')
                    ->setCreatedAt($date)
                    ->setUser($userTest)
                    ->setCategory($categoryGrab);
            $manager->persist($trickTest);
        }
        $manager->flush();

        //add video
        $videosData=[];
        $videosData=[
            ['title'=>'mute', 'url'=>'https://www.youtube.com/embed/M5NTCfdObfs'],
            ['title'=>'sad', 'url'=>'https://www.youtube.com/embed/51sQRIK-TEI'],
        ];

               foreach ($videosData as $videoData){
            $video = new Video;
            /** @var TrickRepository */
            $trickRepository = $manager->getRepository(Trick::class);
            $trick = $trickRepository->findOneByTitle($videoData['title']);
            $video->setTrick($trick)
                ->setUrl($videoData['url'])
                ->setCreatedAt(new \DateTime())
                ->setPublish('1');
            $manager->persist($video);    
        }
        $manager->flush();

        //add img
        $imgsData=[];
        $imgsData=[
            ['titleTrick'=>'mute',
            'src'=>'/img/01mute01.jpg'],
            ['titleTrick'=>'mute',
            'src'=>'/img/01mute02.png'],
            ['titleTrick'=>'indy',
            'src'=>'/img/03indy01.jpg'],
            ['titleTrick'=>'indy',
            'src'=>'/img/03indy02.png'],
            ['titleTrick'=>'stalefish',
            'src'=>'/img/04stalefish01.jpg'],
            ['titleTrick'=>'tail grab',
            'src'=>'/img/05tailgrab01.jpg'],
            ['titleTrick'=>'nose grab',
            'src'=>'/img/06nosegrab01.jpg'],
            ['titleTrick'=>'japan',
            'src'=>'/img/07japan01.jpg'],
        ];

        foreach ($imgsData as $imgData){
            $image = new Image;
            /** @var TrickRepository */
            $trickRepository = $manager->getRepository(Trick::class);
            $trick = $trickRepository->findOneByTitle($imgData['titleTrick']);
            $image->setTrick($trick)
                ->setCreatedAt(new \DateTime())
                ->setPublish('1')
                ->setSrc($imgData['src']);
            $manager->persist($image);    
        }
        $manager->flush();

        //add images Test
        for ($i = 0; $i < 50; $i++) {
            $image = new Image;
            /** @var TrickRepository */
            $trickRepository = $manager->getRepository(Trick::class);
            $trick = $trickRepository->findOneByTitle('mute');
            $image->setTrick($trick)
                ->setCreatedAt(new \DateTime())
                ->setPublish('1')
                ->setSrc('https://via.placeholder.com/150');
            $manager->persist($image);    
        }
        $manager->flush();
    }
}