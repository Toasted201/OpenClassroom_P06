<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AvatarFormType;
use App\Form\RegistrationFormType;
use App\Security\UserAuthenticator;
use App\Service\UserService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
    public function __construct()
    {
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserService $userService): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {          
            try {
                $userService->newUser($form, $user);
            }
            catch (Exception $ex) { 
                $this->addFlash('error',$ex->getMessage() . 'Erreur Système : veuillez ré-essayer');
                return $this->redirectToRoute('home');   
            }    
            $this->addFlash('success','Un email de validation vous a été envoyé');
            return $this->redirectToRoute('home');  
        }    

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'user' => $user,
        ]);
    }

    /**
     * @Route("/avatar", name="app_avatar")
     */
    public function avatar(Request $request, UserService $userService): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $user = $this->getUser();
        $form = $this->createForm(AvatarFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $userService->uploadAvatar($form, $user);
            }
            catch (Exception $ex) { 
                $this->addFlash('error',$ex->getMessage() . 'Erreur Système : veuillez ré-essayer');
                return $this->redirectToRoute('home');   
            }    
            $this->addFlash('success','Votre nouvel avatar est enregistré');
            return $this->redirectToRoute('home');
        }

        return $this->render('registration/avatar.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/verify/email/{validationToken}", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request, UserService $userService, GuardAuthenticatorHandler $guardHandler, UserAuthenticator $authenticator, User $user): Response
    {
        $isVerified = $user->isVerified(); 

        if ($isVerified) {   
            return $this->redirectToRoute('login');
        } 
        else
        {
            try {
            $userService->emailValidation($user);
            }
            catch (Exception $ex) { 
                $this->addFlash('error',$ex->getMessage() . 'Erreur Système : veuillez ré-essayer');
                return $this->redirectToRoute('home');   
            } 
        }  
        
        return $guardHandler->authenticateUserAndHandleSuccess( // connecte le user
            $user,
            $request,
            $authenticator,
            'main' // firewall name in security.yaml
        );
        $this->addFlash('success', 'Votre mail a été validé. Vous êtes connecté');
        return $this->redirectToRoute('home');
    }    
}