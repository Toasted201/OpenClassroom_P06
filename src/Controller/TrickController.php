<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\User;
use App\Form\CommentFormType;
use App\Form\TrickType;
use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use App\Service\NewCommentHandler;
use App\Service\NewTrickHandler;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Twig\Environment;

/**
 * @Route("/trick")
 */
class TrickController extends AbstractController
{
    /**
     * @Route("/", name="trick_index", methods={"GET"})
     */
    public function index(TrickRepository $trickRepository): Response
    {
        return $this->render('trick/index.html.twig', [
            'tricks' => $trickRepository->findBy([],['createdAt' => 'desc']),
        ]);
    }

    /**
     * @Route("/new", name="trick_new", methods={"GET","POST"})
     */
    public function new(NewTrickHandler $newTrickHandler): Response
    { 
        $trick= new Trick;
        if ($newTrickHandler->process()) {
            return $this->redirectToRoute('trick_index');
        }
 
        return $this->render('trick/new.html.twig', [
            'trick' => $trick,
            'formTrick' => $newTrickHandler->getForm()->createView(),
        ]);
    }

    /**
     * @Route("/{safeTitle}", name="trick_show", methods={"GET", "POST"})
     */
    public function show(Request $request, NewCommentHandler $newCommentHandler, CommentRepository $commentRepo, Trick $trick): Response
    {
        if (!$trick->getPublish()) {
        return $this->redirectToRoute('home'); /*Retour sur la page d'accueil si le trick demandé n'est pas publié*/
        }
        
        if ($newCommentHandler->process($trick)) {
            return $this->redirectToRoute('trick_show', [
                'safeTitle' => $trick->getSafeTitle()
            ]);
        }
        
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $commentRepo->getCommentPaginator($trick, $offset);

        return $this->render('trick/show.html.twig', [
                'trick' => $trick,
                'commentForm' => $newCommentHandler->getForm()->createView(),
                'comments' => $paginator,
                'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
                'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
            ]);
    }


    /**
     * @Route("/{safeTitle}/edit", name="trick_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Trick $trick, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $formTrick = $this->createForm(TrickType::class, $trick);
        $formTrick->handleRequest($request);

        if ($formTrick->isSubmitted() && $formTrick->isValid()) {
            $safeTitle=$slugger->slug($trick->getTitle());
            $trick->setSafeTitle($safeTitle);

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('trick_edit',['safeTitle' => $trick->getSafeTitle()]);
        }

        return $this->render('trick/edit.html.twig', [
            'trick' => $trick,
            'formTrick' => $formTrick->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="trick_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Trick $trick): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($this->isCsrfTokenValid('delete'.$trick->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($trick);
            $entityManager->flush();
        }

        $this->addFlash('success','Trick supprimé');

        return $this->redirectToRoute('home');
    }
}
