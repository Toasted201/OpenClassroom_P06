<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use DateInterval;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Uid\Uuid;

/** 
 * @Route("/reset-password")
 */
class ResetPasswordController extends AbstractController
{

    public function __construct()
    {
    }

    /**
     * Formulaire de demande et envoi de mail
     * 
     * @Route("", name="app_forgot_password_request")
     */
    public function request(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class); //Création du formulaire
        $form->handleRequest($request); //Récupération des données du formulaire
        if ($form->isSubmitted() && $form->isValid()) {

            //on identifie le user selon l'email envoyé
            $email = $form->get('email')->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            //message d'erreur générique s'il n'y a pas de user associé à l'email reçu
            if (!$user) {
                $this->addFlash('error','Si un compte est associé à ce mail, un email de ré-initialisation vous a été envoyé');
                return $this->redirectToRoute('home');   
            }
            
            //création d'un token
            $resetToken = Uuid::v4();

            //création d'une date d'expiration du token
            $resetLifeTime = new \DateTime();
            $i = DateInterval::createFromDateString('1 day');
            $resetLifeTime->add($i);
            
            //Token et LifeTime enregistré dans le user
            $user->setResetToken($resetToken);
            $user->setResetLifeTime($resetLifeTime);
            
            //enregistrement en bdd
            $entityManager->persist($user);
            $entityManager->flush();

            //Création d'une url contenant le token    
            $url = $this->generateUrl('app_reset_password', ['resetToken'=>$resetToken], UrlGeneratorInterface::ABSOLUTE_URL);

            //envoie de l'e-mail
            $email = (new TemplatedEmail())
            ->from(new Address('julie@helixsi.com', 'SnowTricks'))
            ->to($user->getEmail())
            ->subject('Votre demande de nouveau mot de passe')
            ->htmlTemplate('reset_password/email.html.twig')
            ->context([
                'url' => $url,
                'resetLifeTime' => $resetLifeTime,
            ])
            ;
            $mailer->send($email);

            //affichage de la page de confirmation d'email
            $this->addFlash('success','Un email vous a été envoyé, contenant un lien sur lequel cliquer pour ré-initialiser votre mot de passe.');
            return $this->redirectToRoute('home');   
        }

        return $this->render('reset_password/request.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }

    /**
     * formulaire de modification de mot de passe
     * @Route("/reset/{resetToken}", name="app_reset_password")
     */
    public function reset(Request $request, UserPasswordEncoderInterface $passwordEncoder, User $user): Response
    {
         // Faire des tests de token. Si ok
         $form = $this->createForm(ChangePasswordFormType::class);
         $form->handleRequest($request);
 
         if ($form->isSubmitted() && $form->isValid()) {
 
             // Encode the plain password, and set it.
             $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
               
            );
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
 
            $this->addFlash('success','Votre mot de passe a été modifié. Vous pouvez vous connecter');
            return $this->redirectToRoute('home');
        }
        
        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form->createView(),
        ]);

    }
}