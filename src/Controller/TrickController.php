<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Form\CommentFormType;
use App\Form\TrickType;
use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use App\Service\TrickManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/trick")
 */
class TrickController extends AbstractController
{
    /**
     * Liste des figures
     * @Route("/", name="trick_index", methods={"GET"})
     */
    public function index(TrickRepository $trickRepository): Response
    {
        return $this->render('trick/index.html.twig', [
            'tricks' => $trickRepository->findBy([],['createdAt' => 'desc']),
        ]);
    }

    /**
     * Création d'une nouvelle figure
     * @Route("/new", name="trick_new", methods={"GET","POST"})
     */
    public function new(Request $request, TrickManagerInterface $trickManager): Response
    { 
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $trick= new Trick;
        $user = $this->getUser();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {          
            try {
                $trickManager->newTrick($form, $trick, $user);
            }
            catch (Exception $ex) { 
                $this->addFlash('error',$ex->getMessage() . 'Erreur Système : veuillez ré-essayer');
                return $this->redirectToRoute('home');   
            }    
            $this->addFlash('success','Votre trick a bien été créé');
            return $this->redirectToRoute('trick_index');  
        }    

        return $this->render('trick/new.html.twig', [
            'trickForm' => $form->createView(),
            'trick' => $trick,
        ]);
    }

    /**
     * Affichage de la fiche d'une figure
     * @Route("/show/{safeTitle}", name="trick_show", methods={"GET", "POST"}, requirements={"safeTitle"="[a-zA-Z0-9\-]*"} )
     */
    public function show(Request $request, TrickManagerInterface $trickManager, CommentRepository $commentRepo, Trick $trick): Response
    {
        if (!$trick->getPublish()) {
        return $this->redirectToRoute('home'); /*Retour sur la page d'accueil si le trick demandé n'est pas publié*/
        }
        
        $user = $this->getUser();
        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $user) {
            try {
                $trickManager->newComment($form, $trick, $user);
            }
            catch (Exception $ex) {
                $this->addFlash('error',$ex->getMessage() . 'Erreur Système : veuillez ré-essayer');
                return $this->redirectToRoute('home');  
            }
            return $this->redirectToRoute('trick_show', [
                'safeTitle' => $trick->getSafeTitle()
            ]);
        }
        
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $commentRepo->getCommentPaginator($trick, $offset);

        return $this->render('trick/show.html.twig', [
                'trick' => $trick,
                'commentForm' => $form->createView(),
                'comments' => $paginator,
                'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
                'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
            ]);
    }

    /**
     * Modification d'une figure
     * @Route("/edit/{safeTitle}", name="trick_edit", methods={"GET","POST"}, requirements={"safeTitle"="[a-zA-Z0-9\-]*"})
     */
    public function edit(Request $request, Trick $trick, TrickManagerInterface $trickManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $trickForm = $this->createForm(TrickType::class, $trick);
        $trickForm->handleRequest($request);

        if ($trickForm->isSubmitted() && $trickForm->isValid()) {
            try {
               $trickManager->editTrick($trickForm, $trick);
            }
            catch (Exception $ex) { 
                $this->addFlash('error',$ex->getMessage() . 'Erreur Système : veuillez ré-essayer');
                return $this->redirectToRoute('home');   
            }    
            $this->addFlash('success','Votre trick a bien été modifié');
            return $this->redirectToRoute('trick_edit',['safeTitle' => $trick->getSafeTitle()]); 
        }    

        return $this->render('trick/edit.html.twig', [
            'trick' => $trick,
            'trickForm' => $trickForm->createView(),
        ]);
    }

    /**
     * Supression d'une figure
     * @Route("/delete/{safeTitle}", name="trick_delete", methods={"DELETE"}, requirements={"safeTitle"="[a-zA-Z0-9\-]*"})
     */
    public function delete(Request $request, Trick $trick): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (!$this->isCsrfTokenValid('delete'.$trick->getId(), $request->request->get('_token'))) {
            throw new Exception("TokenUnvalid");
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($trick);
        $entityManager->flush();
        $this->addFlash('success','Le trick a bien été supprimé');
        return $this->redirectToRoute('home');
    }
}        
