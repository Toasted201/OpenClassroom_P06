<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Service\UserManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/** 
 * @Route("/reset-password")
 */
class ResetPasswordController extends AbstractController
{

    public function __construct()
    {
    }

    /**
     * Formulaire d'envoi de mail pour changer le mot de passe
     * @Route("", name="app_forgot_password_request")
     */
    public function request(Request $request, UserManagerInterface $userManager): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);      
        
        if ($form->isSubmitted() && $form->isValid()) {      
            try {
                $userManager->emailResetPassword($form);
            }
            catch (Exception $ex) { 
                $this->addFlash('error',$ex->getMessage() . 'Si un compte est associé à ce mail, un email de ré-initialisation vous a été envoyé');
                return $this->redirectToRoute('home');   
            }    
            
            $this->addFlash('success','Un email vous a été envoyé, contenant un lien sur lequel cliquer pour ré-initialiser votre mot de passe.');
            return $this->redirectToRoute('home');   
        }    

        return $this->render('reset_password/request.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }

    /**
     * Formulaire de modification du mot de passe
     * @Route("/reset/{resetToken}", name="app_reset_password")
     */
    public function reset(Request $request, User $user, UserManagerInterface $userManager): Response
    {       
        try {
            $userManager->isTokenPerempted($user);
        }
        catch (Exception $ex) {
            $this->addFlash('error','Le lien de réinitalisation a expiré. Vous devez refaire une demande');
            return $this->redirectToRoute('home');
        }    
          
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request); 
        
        if ($form->isSubmitted() && $form->isValid()) {   
            try {
                $userManager->newPassword($form, $user);
            }
            catch (Exception $ex) {
                $this->addFlash('error',$ex->getMessage());
                return $this->redirectToRoute('home');     
            }  
                
            $this->addFlash('success','Votre mot de passe a été modifié. Vous pouvez vous connecter');
            return $this->redirectToRoute('home');
        }   
        
        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form->createView(),
        ]);

    }
}