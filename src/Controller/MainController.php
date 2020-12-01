<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(TrickRepository $trickRepo): Response
    {
        $tricks = $trickRepo->findAll();
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'tricks' => $tricks
        ]);
    }
}
