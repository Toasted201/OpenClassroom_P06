<?php

namespace App\Service;

use App\Entity\User;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserManager implements UserManagerInterface
{
    private $entityManager;
    private $mailerInterface;
    private $passwordEncoder;
    private $slugger;
    private $router;
    private $container;
 
    public function __construct(
        EntityManagerInterface $entityManager, 
        MailerInterface $mailerInterface, 
        UserPasswordEncoderInterface $passwordEncoder,
        SluggerInterface $slugger,
        UrlGeneratorInterface $router,
        ContainerInterface $container)

    {
        $this->entityManager = $entityManager;
        $this->mailerInterface = $mailerInterface;
        $this->passwordEncoder = $passwordEncoder;
        $this->slugger = $slugger;
        $this->router = $router;
        $this->container = $container;  
    }
 
 
    /**
     * Identifification user, génération token, enregistrement bdd, envoi mail
     * @throws Exception
     */
    public function emailResetPassword($form)
    {
        //on identifie le user selon l'email envoyé
        $email = $form->get('email')->getData();
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        //message d'erreur générique s'il n'y a pas de user associé à l'email reçu
        if (!$user) {
            throw new Exception("Utilisateur non trouvé");
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
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        
        $url = $this->router->generate('app_reset_password', ['resetToken'=>$resetToken], UrlGeneratorInterface::ABSOLUTE_URL);
        $to = $user;
        $subjectEmail = 'Votre demande de nouveau mot de passe';
        $htmlTemplate ='reset_password/email.html.twig';
        $context=[];
        $context['url']=$url;
        $context['resetLifeTime']=$resetLifeTime;
        $this->sendEmail($to, $subjectEmail, $htmlTemplate, $context);       
    }

    /**
     * Vérification péremption token
     * @throws Exception
     */
    public function isTokenPerempted($user)
    {
        $resetLifeTime=$user->getResetLifeTime();
        $now = new DateTime();
        if ($resetLifeTime < $now){
            throw new Exception("TokenPerempted");
        }
    }
    
    
    /**
     * Validation Email
     * @throws Exception
     */
    public function emailValidation($user)
    {       
        $user->setIsVerified(true);
        $this->entityManager->persist($user);
        $this->entityManager->flush(); 
    }


    /**
     * Création nouveau mot de passe
     */
    public function newPassword($form, $user)
    {
        $plainPassword = $form->get('plainPassword')->getData();
        $password = $this->passwordEncoder->encodePassword($user,$plainPassword);

        $user->setPassword($password);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    /**
     * Upload photo de profil
     * @throws Exception
     */
    public function uploadAvatar($form, $user)
    {
        $photoFile = $form->get('photo')->getData();
        $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename=$this->slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$photoFile->guessExtension();
        $photoFile->move(
                $this->container->getParameter('avatarAbsoluteDir'),
                $newFilename
            );
        $user->setUserFilename($newFilename);
        
        $this->entityManager->persist($user);
        $this->entityManager->flush();              
    }  

    /**
     * New User
     * @throws Exception
     */
    public function newUser($form, $user)
    {       
        $plainPassword = $form->get('plainPassword')->getData();
        $password = $this->passwordEncoder->encodePassword($user,$plainPassword);
        $user->setPassword($password);
                          
        $user->setCreatedAt(new DateTime());

        $user->setValidationToken(Uuid::v4());

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->uploadAvatar($form,$user);

        $url = $this->router->generate('app_verify_email', ['validationToken'=>$user->getValidationToken()], UrlGeneratorInterface::ABSOLUTE_URL);
        $to = $user;
        $subjectEmail = 'Confirmez votre Email';
        $htmlTemplate ='registration/confirmation_email.html.twig';
        $context=[];
        $context['url']=$url;
        $this->sendEmail($to, $subjectEmail, $htmlTemplate, $context);
    }
    
    public function sendEmail($to, $subjectEmail, $htmlTemplate, $context)
    {        
        $email = (new TemplatedEmail())
            ->from(new Address('julie@helixsi.com', 'SnowTricks'))
            ->to($to->getEmail())
            ->subject($subjectEmail)
            ->htmlTemplate($htmlTemplate)
            ->context($context)
        ;
        $this->mailerInterface->send($email);
    }    
}