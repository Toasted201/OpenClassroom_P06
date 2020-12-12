<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use App\Security\UserAuthenticator;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;


class RegistrationController extends AbstractController
{
    public function __construct()
    {
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, MailerInterface $mailer): Response
    {
        $user = new User();
        $date = new \DateTime();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
               
            );
            $user ->setCreatedAt($date);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $userId = $user->getId();

            //contenu e-mail
            $email = (new TemplatedEmail())
                    ->from(new Address('julie@helixsi.com', 'SnowTricks'))
                    ->to($user->getEmail())
                    ->subject('Confirmez votre Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            ;

            //génrérer l'url
            $uri = $this->generateUrl('app_verify_email', ['userId'=>$userId], UrlGeneratorInterface::ABSOLUTE_URL);

            //contenu du mail
            $context = $email->getContext();
            $context['signedUrl'] = $uri;
            $email->context($context);
            $mailer->send($email);

            // do anything else you need here, like send an email
            $this->addFlash('success','Un email de validation vous a été envoyé');

            return $this->redirectToRoute('home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/verify/email", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request, GuardAuthenticatorHandler $guardHandler, UserAuthenticator $authenticator): Response
    {
        $userId = $request->query->get('userId'); // récupère le userId de l'URL
        // for security : used the Doctrine generator to help autogenerate UUID values for the entity primary keys
        if (!empty($userId)) {
            $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([ //retrouve le user associé
                'id' => $userId,
            ]);
            $isVerified = $user->isVerified();
            
            // TODO simplifier le double if
            if ($isVerified) {   
                return $this->redirectToRoute('login');
            } 
            else
            {    
            $user->setIsVerified(true); // valide l'email
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();      
            
            return $guardHandler->authenticateUserAndHandleSuccess( // connecte le user
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
            }
        }
        else {
            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', 'Votre mail a été validé. Vous êtes connecté');

        return $this->redirectToRoute('home');
    }
}
