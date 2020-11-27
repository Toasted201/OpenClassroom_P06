<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    /**
     * @Route("/security", name="security")
     */
    public function index(): Response
    {
        return $this->render('security/index.html.twig', [
            'controller_name' => 'SecurityController',
        ]);
    }

        /**
     * @Route("/registration", name="registration")
     */
    public function registration(): Response
    {
        return $this->render('security/registration.html.twig', [
            'controller_name' => 'SecurityController',
        ]);
    }
    
    /**
     * @Route("/connexion", name="connexion")
     */
    public function connexion(): Response
    {
        return $this->render('security/connexion.html.twig', [
            'controller_name' => 'SecurityController',
        ]);
    }

    /**
     * @Route("/forgot", name="forgot_pass")
     */
    public function forgotPass(): Response
    {
        return $this->render('security/forgot.html.twig', [
            'controller_name' => 'SecurityController',
        ]);
    }

    /**
     * @Route("/reset", name="reset_pass")
     */
    public function resetPass(): Response
    {
        return $this->render('security/reset.html.twig', [
            'controller_name' => 'SecurityController',
        ]);
    }
}