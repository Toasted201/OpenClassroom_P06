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
        //liste de tricks publiés, triés par date de création décroissante
        $tricksPublished = $trickRepo->findBy(['publish' => '1'], ['createdAt' => 'DESC']);
        $tricksPublishedHome = $trickRepo->findBy(['publish' => '1'], ['createdAt' => 'DESC'], 15, 0);
        
        //nb total de tricks publiés
        $nbTricksPublished = count($tricksPublished);

        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'tricks' => $tricksPublishedHome,
            'nbTricks' => $nbTricksPublished
        ]);
    }

    /**
     *  @Route("/blockTricks", name="blockTricks", methods={"GET"})
     */
    
    public function blockTricks(TrickRepository $trickRepo): Response
    {
        $numTrick = $_GET['numTrick'];
        $tricks = $trickRepo->findBy(['publish' => '1'], ['createdAt' => 'DESC'], 15, $numTrick);

        return $this->render('main/_blockTricks.html.twig', [
            'tricks' => $tricks
        ]);
    }

}
