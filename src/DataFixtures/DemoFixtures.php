<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Video;
use App\Repository\CategoryRepository;
use App\Repository\TrickRepository;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class DemoFixtures extends Fixture
{    
        protected $passwordEncoder;

 
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)

    {
        $this->passwordEncoder = $passwordEncoder;
    }
 
    
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
        $password = $this->passwordEncoder->encodePassword($userTest,'passpass');
        $userTest->setUsername('BobDoe')
        ->setEmail('bob@doe.com')
        ->setPassword($password)
        ->setRoles([])
        ->setIsVerified('1')
        ->setCreatedAt(new DateTime());
        $manager->persist($userTest);

        

        //add Tricks Demo
        $tricksData = [];
        $tricksData = [
            ['title' =>'mute',
            'safeTitle' => 'mute',
            'description' => 'Saisie de la carre frontside de la planche entre les deux pieds avec la main avant. Surtout ne jamais tendre la jambe avant lors de ce grab !!! (Mute nosebone), restez accroupi, ou tendez la jambe arrière lors d\'une rotation (Mute tailbone).',
            'publish' => '1',
            'category' => 'grabs'],

            ['title' =>'sad',
            'safeTitle' => 'sad',
            'description' => 'Saisie de la carre backside de la planche, entre les deux pieds, avec la main avant',
            'publish'=> '1',
            'category' => 'grabs'],

            ['title' =>'indy',
            'safeTitle' => 'indy',
            'description' => 'Saisie de la carre frontside de la planche, entre les deux pieds, avec la main arrière. C\'est le grab basique et stable par excellence. C\'est le premier grab à essayer, ses variantes sont nombreuses.',
            'publish'=> '1',
            'category' => 'grabs'],

            ['title' =>'stalefish',
            'safeTitle' => 'stalefish',
            'description' => 'Saisie de la carre backside de la planche entre les deux pieds avec la main arrière. Ce grab très à la mode peut s\'effectuer de nombreuses façons toutes étant souvent esthétiques.',
            'publish'=> '1',
            'category' => 'grabs'],

            ['title' =>'tail grabs',
            'safeTitle' => 'tailgrab',
            'description' => 'Saisie de la partie arrière de la planche, avec la main arrière. Grabez l\'arrière de sa planche, c\'est un grab difficile mais très esthétique.',
            'publish'=> '1',
            'category' => 'grabs'],

            ['title' =>'nose grabs',
            'safeTitle' => 'nosegrab',
            'description' => 'Saisie de la partie avant de la planche, avec la main avant. Grabez l\'avant de sa planche, très difficile à effectuer en rotation mais c\'est le grab qui revient à la mode (dans une rotation).',
            'publish'=> '1',
            'category' => 'grabs'],

            ['title' =>'japan',
            'safeTitle' => 'japan',
            'description' => 'Saisie de l\'avant de la planche, avec la main avant, du côté de la carre frontside. Grabez en mute et tirez votre planche la spatule vers le ciel (grab difficile à réaliser en rotation).',
            'publish'=> '1',
            'category' => 'grabs'],

            ['title' =>'seat belt',
            'safeTitle' => 'seatbelt',
            'description' => 'Saisie du carre frontside à l\'arrière avec la main avant',
            'publish'=> '1',
            'category' => 'grabs'],

            ['title' =>'truck driver',
            'safeTitle' => 'truckdriver',
            'description' => 'Saisie du carre avant et carre arrière avec chaque main (comme tenir un volant de voiture)',
            'publish'=> '1',
            'category' => 'grabs'],

            ['title' =>'crail',
            'safeTitle' => 'crail',
            'description' => 'La main arrière grab la carre front devant la fix avant. La jambe arrière doit etre tendue. C\'est un indy avec la jambe arrière tendue.',
            'publish'=> '1',
            'category' => 'grabs'],

            ['title' =>'Indy Nosebone',
            'safeTitle' => 'indynosebone',
            'description' => 'C\'est un indy avec la jambe avant tendue. (méfiance : trop tendue et la police du style te guète.',
            'publish'=> '1',
            'category' => 'grabs'],

            ['title' =>'Rotation backside',
            'safeTitle' => 'rotationbackside',
            'description' => 'Pour un goofy, dans le sens des aiguilles d\'une montre. Elle s\'effectue en lançant son épaule avant vers l\'arrière (rotations en vogue : 180° 540° et 720°).',
            'publish'=> '1',
            'category' => 'rotations'],

            ['title' =>'Rotation frontside',
            'safeTitle' => 'rotationfrontside',
            'description' => 'Pour un régular, dans le sens des aiguilles d\'une montre. Elle s\'effectue en lançant l\'épaule arrière vers l\'avant (rotations en vogue : 540° 720°).',
            'publish'=> '1',
            'category' => 'rotations'],

            ['title' =>'Rodéoback',
            'safeTitle' => 'rodeoback',
            'description' => 'c\'est la rotation la plus vue des 3 dernières années c\'est une rotation back mélangée avec un backflip impulsion sur les talons.',
            'publish'=> '1',
            'category' => 'rotations'],

            ['title' =>'Mistyflip',
            'safeTitle' => 'mistyflip',
            'description' => 'c\'est une rotation back mélangée avec un frontflip, effectuée dans un pipe, c\'est un mac-twist, l\'impulsion à lieu sur les pointes de pied.',
            'publish'=> '1',
            'category' => 'rotations'],

            ['title' =>'Rodéofront',
            'safeTitle' => 'rodeofront',
            'description' => 'Rotation front avec une impulsion très marquée sur les pointes de pieds',
            'publish'=> '1',
            'category' => 'rotations'],
        ]; 		

        $trick = new Trick();
        
        /** @var CategoryRepository */
        $categoryRepository = $manager->getRepository(Category::class);    
        $date = new \DateTime();
        
        foreach ($tricksData as $trickData){           
            $trick = new Trick;
            $category = $categoryRepository->findOneByTitle($trickData['category']);
            $trick->setTitle($trickData['title'])
                ->setSafeTitle($trickData['safeTitle'])
                ->setDescription($trickData['description'])
                ->setPublish($trickData['publish'])
                ->setCreatedAt($date)
                ->setUser($userTest)
                ->setCategory($category);
            $manager->persist($trick);
        }

        $manager->flush();

        /*add tricks Test
        for ($i = 0; $i < 20; $i++) {
            $trickTest = new Trick();
            $trickTest->setTitle('Titre ' . $i)
                    ->setSafeTitle('safeTitre' . $i)
                    ->setDescription('description ' .  $i)
                    ->setPublish('1')
                    ->setCreatedAt($date)
                    ->setUser($userTest)
                    ->setCategory($categoryGrab);
            $manager->persist($trickTest);
        }
        $manager->flush();*/

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
                ->setCreatedAt(new DateTime());
            $manager->persist($video);    
        }
        $manager->flush();

        //add img
        $imgsData=[];
        $imgsData=[
            ['titleTrick'=>'mute',
            'src'=>'/img/demo/01mute01.jpg'],
            ['titleTrick'=>'mute',
            'src'=>'/img/demo/01mute02.png'],
            ['titleTrick'=>'sad',
            'src'=>'/img/demo/02sad01.jpg'],
            ['titleTrick'=>'indy',
            'src'=>'/img/demo/03indy01.jpg'],
            ['titleTrick'=>'indy',
            'src'=>'/img/demo/03indy02.png'],
            ['titleTrick'=>'stalefish',
            'src'=>'/img/demo/04stalefish01.jpg'],
            ['titleTrick'=>'tail grabs',
            'src'=>'/img/demo/05tailgrab01.jpg'],
            ['titleTrick'=>'nose grabs',
            'src'=>'/img/demo/06nosegrab01.jpg'],
            ['titleTrick'=>'japan',
            'src'=>'/img/demo/07japan01.jpg'],
            ['titleTrick'=>'seat belt',
            'src'=>'/img/demo/08seatbelt01.jpg'],
            ['titleTrick'=>'truck driver',
            'src'=>'/img/demo/09truck01.jfif'],
            ['titleTrick'=>'rotation frontside',
            'src'=>'/img/demo/13rotationfront01.jfif'],
            ['titleTrick'=>'mistyflip',
            'src'=>'/img/demo/15mistyflip01.jfif'],
        ];

        foreach ($imgsData as $imgData){
            $image = new Image;
            /** @var TrickRepository */
            $trickRepository = $manager->getRepository(Trick::class);
            $trick = $trickRepository->findOneByTitle($imgData['titleTrick']);
            $image->setTrick($trick)
                ->setCreatedAt(new \DateTime())
                ->setSrc($imgData['src']);
            $manager->persist($image);    
        }
        $manager->flush();

        /*add images Test
        for ($i = 0; $i < 20; $i++) {
            $image = new Image;
            /** @var TrickRepository */ 
        /*    $trickRepository = $manager->getRepository(Trick::class);
            $trick = $trickRepository->findOneByTitle('mute');
            $image->setTrick($trick)
                ->setCreatedAt(new \DateTime())
                ->setSrc('https://via.placeholder.com/600');
            $manager->persist($image);    
        }
        $manager->flush();*/
    }
}