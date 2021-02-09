<?php

namespace App\Service;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class TrickManager implements TrickManagerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, SluggerInterface $slugger)
    {
        $this->entityManager = $entityManager;
        $this->slugger = $slugger;
    }

    /**
     * Création d'une figure
     * @throws Exception
     */
    public function newTrick($form, $trick, $user)
    {
        $trick=$form->getData();
        $trick->setCreatedAt(new DateTime());
        $trick->setUser($user);
        $safeTitle=$this->slugger->slug($trick->getTitle())->folded();
        $trick->setSafeTitle($safeTitle);

        $this->entityManager->persist($trick);
        $this->entityManager->flush();
    }

    /**
     * Création d'un commentaire
     * @throws Exception
     */
    public function newComment($form, $trick, $user)
    {
        $comment=$form->getData();
        $comment->setTrick($trick);
        $comment->setUser($user);
        $comment->setCreatedAt(new DateTime());
        
        $this->entityManager->persist($form->getData()).
        $this->entityManager->flush();
    }

    /**
     * Modification d'une figure
     * @throws Exception
     */
    public function editTrick($form, $trick)
    {
        $trick=$form->getData();
        $safeTitle=$this->slugger->slug($trick->getTitle())->folded();
        $trick->setSafeTitle($safeTitle);

        $this->entityManager->persist($trick);
        $this->entityManager->flush();
    }
}    