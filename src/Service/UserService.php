<?php

namespace App\Service;

use App\Entity\Comment;
use App\Entity\User;
use App\Form\CommentFormType;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService extends AbstractController
{
    protected $requestStack;
    protected $form;
    protected $mailerInterface;
    protected $formFactory;
    protected $currentUser;
    protected $tokenStorageInterface;
    protected $passwordEncoder;
 
    public function __construct(
        RequestStack $requestStack, 
        TokenStorageInterface $tokenStorageInterface, 
        FormFactoryInterface $formFactory, 
        EntityManagerInterface $entityManager, 
        MailerInterface $mailerInterface, 
        UserPasswordEncoderInterface $passwordEncoder)

    {
        $this->requestStack = $requestStack;
        $this->tokenStorageInterface = $tokenStorageInterface;
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->mailerInterface = $mailerInterface;
        $this->passwordEncoder = $passwordEncoder;
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
        $this->mailerInterface->send($email);
    }

    public function isTokenPerempted($user)
    {
        $resetLifeTime=$user->getResetLifeTime();
        $now = new DateTime();
        if ($resetLifeTime < $now){
            throw new Exception("TokenPerempted");
        }
    }    

    public function newPassword($form, $user)
    {
        $plainPassword = $form->get('plainPassword')->getData();
        $password = $this->passwordEncoder->encodePassword($user,$plainPassword);

        $user->setPassword($password);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}