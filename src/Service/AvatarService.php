<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AvatarService extends AbstractController
{
    private $slugger;
    
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }
    
    public function uploadAvatar($photoFile)
    {
        if ($photoFile) {
            $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename=$this->slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$photoFile->guessExtension();
            try {
                $photoFile->move(
                    $this->getParameter('avatarAbsoluteDir'),
                    $newFilename
                );
                return $newFilename;
            } catch (FileException $e) {
                $this->addFlash('error','Votre nouvel avatar n\'a pas été enregistré');
                return $this->redirectToRoute('home');
            }
        }
    }    
}
