<?php

namespace App\Service;

use App\Entity\Trick;
use App\Form\TrickType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class NewTrickHandler
{
    protected $requestStack;
    protected $form;
    protected $mailer;
    protected $formFactory;
    protected $currentUSer;
    protected $tokenStorageInterface;
 
    /**
     * @param RequestStack $requestStack
     * @param SluggerInterface $slugger
     * @param EntityManagerInterface $entityManager
     * @param FormFactoryInterface $formFactory;
     * @param TokenStorageInterface $tokenStorageInterface;
     *
     */
    public function __construct(RequestStack $requestStack, TokenStorageInterface $tokenStorageInterface, FormFactoryInterface $formFactory, SluggerInterface $slugger, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->currentUser = $tokenStorageInterface->getToken()->getUser();
        $this->entityManager = $entityManager;
        $this->slugger = $slugger;
        $this->formFactory = $formFactory;
    }
 
    /**
     * Process form
     *
     * @return boolean
     */
    public function process()
    {
        $trick = new Trick;
        $form = $this->formFactory->create(TrickType::class, $trick);
        $this->form = $form;

        $this->form->handleRequest($this->requestStack->getCurrentRequest());
 
        if ($this->form->isSubmitted() && $this->form->isValid()) {
            $this->onSuccess();
            return true;
        }
 
        return false;
    }
 
    public function setForm(Form $form)
    {
        $this->form = $form;
    }

    /**
     * Get form
     *
     * @return
     */
    public function getForm()
    {
        return $this->form;
    }
 
    /**
     * Persist on success
     */
    protected function onSuccess()
    {
        $trick=$this->form->getData();
        $trick->setCreatedAt(new DateTime());
        $trick->setUser($this->currentUser);
        $safeTitle=$this->slugger->slug($trick->getTitle());
        $trick->setSafeTitle($safeTitle);
        
        $this->entityManager->persist($this->form->getData()).
        $this->entityManager->flush();
    }
}