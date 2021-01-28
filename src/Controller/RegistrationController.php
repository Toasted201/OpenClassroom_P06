<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AvatarFormType;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use App\Security\UserAuthenticator;
use App\Service\AvatarService;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Uid\Uuid;



class RegistrationController extends AbstractController
{
    public function __construct()
    {
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, MailerInterface $mailer, SluggerInterface $slugger,  AvatarService $avatarService): Response
    {
        $user = new User();
        $date = new \DateTime();
        $validationToken = Uuid::v4();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            //upload photo
            /** @var UploadedFile $photoFile */
            $photoFile = $form->get('photo')->getData();
            $newFilename = $avatarService->uploadAvatar($photoFile);
            $user->setUserFilename($newFilename);
                       
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )               
            );
            
            $user->setCreatedAt($date);
            $user->setValidationToken($validationToken);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            //contenu e-mail
            $email = (new TemplatedEmail())
                    ->from(new Address('julie@helixsi.com', 'SnowTricks'))
                    ->to($user->getEmail())
                    ->subject('Confirmez votre Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            ;

            //génrérer l'url
            $url = $this->generateUrl('app_verify_email', ['validationToken'=>$validationToken], UrlGeneratorInterface::ABSOLUTE_URL);

            //contenu du mail
            $context = $email->getContext();
            $context['url'] = $url;
            $email->context($context);
            $mailer->send($email);

            // flash
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
    public function avatar(Request $request, AvatarService $avatarService): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $form = $this->createForm(AvatarFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $photoFile */
            $photoFile = $form->get('photo')->getData();
            $newFilename = $avatarService->uploadAvatar($photoFile);
            $user->setUserFilename($newFilename);

            $this->getDoctrine()->getManager()->flush();
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
    public function verifyUserEmail(Request $request, GuardAuthenticatorHandler $guardHandler, UserAuthenticator $authenticator, User $user): Response
    {
        $isVerified = $user->isVerified();           
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
        $this->addFlash('success', 'Votre mail a été validé. Vous êtes connecté');
        return $this->redirectToRoute('home');
        }
    }    
}