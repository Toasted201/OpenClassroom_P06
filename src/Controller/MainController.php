<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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

    /**
     * @Route("/tricks", name="tricks")
     */
    public function tricks(TrickRepository $trickRepo) : Response
    {
        $tricks = $trickRepo->findAll();
        return $this->render('main/tricks.html.twig', [
            'controller_name' => 'MainController',
            'tricks' => $tricks
        ]);
    }


    /**
     * @Route("/trick/{id}", name="trick_details")
     */
    public function trickDetail(Trick $trick): Response
    {
        return $this->render('main/trickDetails.html.twig', [
            'controller_name' => 'MainController',
            'trick' => $trick
        ]);
    }

    /**
     * @Route("/trick/edit/{id}", name="trick_edit")
     */
    public function trickEdit(Trick $trick): Response
    {
        return $this->render('main/trickEdit.html.twig', [
            'controller_name' => 'MainController',
            'trick' => $trick
        ]);
    }

}
