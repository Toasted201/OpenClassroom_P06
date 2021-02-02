<?php

namespace App\Service;

use App\Entity\Comment;
use App\Form\CommentFormType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class NewCommentHandler
{
    protected $requestStack;
    protected $form;
    protected $mailer;
    protected $formFactory;
    protected $currentUser;
    protected $tokenStorageInterface;
 
    /**
     * @param RequestStack $requestStack;
     * @param EntityManagerInterface $entityManager;
     * @param FormFactoryInterface $formFactory;
     * @param TokenStorageInterface $tokenStorageInterface;
     *
     */
    public function __construct(RequestStack $requestStack, TokenStorageInterface $tokenStorageInterface, FormFactoryInterface $formFactory, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->tokenStorageInterface = $tokenStorageInterface;
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
    }
 
    /**
     * Process form
     *
     * @return boolean
     */
    public function process($trick)
    {
        $comment = new Comment;
        $form = $this->formFactory->create(CommentFormType::class, $comment);
        $this->form = $form;

        $this->form->handleRequest($this->requestStack->getCurrentRequest());
 
        if ($this->form->isSubmitted() && $this->form->isValid()) {
            $this->onSuccess($trick);
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
    protected function onSuccess($trick)
    {
        $currentUser = $this->tokenStorageInterface->getToken()->getUser();
        $comment=$this->form->getData();
        $comment->setTrick($trick);
        $comment->setUser($currentUser);
        $comment->setCreatedAt(new DateTime());
        
        $this->entityManager->persist($this->form->getData()).
        $this->entityManager->flush();
    }
}