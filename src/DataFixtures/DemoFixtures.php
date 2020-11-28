<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Entity\Trick;
use App\Entity\User;
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

        
        $userTest = new User();
        $userTest->setName('BobDoe')
        ->setEmail('bob@doe.com')
        ->setPassword('pass')
        ->setRoles([])
        ->setIsVerified('1')
        ->setCreatedAt(new \DateTime());
        $manager->persist($userTest);

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
    }

}
