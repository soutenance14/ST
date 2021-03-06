<?php

namespace App\Controller;

use App\Form\User\UserEditPasswordType;
use App\Form\User\UserResetPasswordType;
use App\Form\User\UserForgottenPasswordType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;

/**
 * @Route("/user")
 */
class UserController extends CustomController
{

    /**
     * @Route("/show", name="user_show", methods={"GET"})
     */
    public function show(): Response
    {
        $user = $this->getUser();
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/edit/password", name="user_edit_password", methods={"GET","POST"})
     */
    public function editPassword(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserEditPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit_password.html.twig', [
            'user' => $user,
            'title' => "RĂ©initialiser le mot de passe",
            'form' => $form,
        ]);
    }
    
    /**
     * @Route("/forgotten/password", name="user_forgotten_password", methods={"GET","POST"})
     */
    public function forgottenPassword(Request $request, UserRepository $userRepository, MailerInterface $mailer): Response
    {
        $error_message ='';
        $form = $this->createForm(UserForgottenPasswordType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $email = $form->get('email')->getData();
            $user = $userRepository->findOneByEmail($email);
            if($user === null)
            {
                $error_message = 'Cet utilisateur n\'existe pas';
            }
            else
            {
                //generate and give a unique token to user
                $user->setToken(md5(uniqid($user->getUsername(), true)));
                $this->getDoctrine()->getManager()->flush();
                try
                {   
                    $this->sendEmail($mailer, $user);
                    $error_message = 'Un email a Ă©tĂ© envoyĂ© avec un lien pour rĂ©initialiser le mot de passe.';
                    // return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
                }
                catch(TransportException $e)
                {
                    $error_message = 'Impossible d\'envoyer le mail, veuillez rĂ©essayez ultĂ©rieurement.';
                }
            }
        }

        return $this->renderForm('user/forgotten_password.html.twig', [
            // 'user' => $this->getUser(),
            'title' => "Mot de passe oubliĂ©",
            'form' => $form,
            'error_message' => $error_message,
        ]);
    }


    /**
     * @Route("/reset/password/{email}/{token}", name="reset_password", methods={"GET","POST"})
     */
    public function resetPassword(Request $request, UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder, $email, $token): Response
    {
        $form = $this->createForm(UserResetPasswordType::class);
        $form->handleRequest($request);
        
        $error_message = "";
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $user = $userRepository->findOneByEmail($email);
            if($user !== null)
            {
                if($user->getToken() === $token)
                {
                    $user->setPassword(
                        $passwordEncoder->encodePassword(
                            $user,
                            $form->get('plainPassword')->getData()
                        )
                    );
                    $user->setToken(null);
                    $this->getDoctrine()->getManager()->flush();
                    $error_message = "Le mot de passe a bien Ă©tĂ© modifiĂ©, veuillez vous connecter.";
                    // return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
                }
                else
                {
                    $error_message = "Impossible d'effectuer le changement pour cause de mauvais token.";
                }
            }
        }
        return $this->renderForm('user/reset_password_from_link.html.twig', [
            //  'user' => $this->getUser(),
             'error_message' => $error_message,
             'title' => "RĂ©initialiser le mot de passe",
             'form' => $form,
        ]);
    }

    private function sendEmail(MailerInterface $mailerInterface, $user)
    {
        $emailFrom = $this->getParameter('app.admin_email');
       
        $email=(new TemplatedEmail())
        ->from($emailFrom)
        ->to($user->getEmail())
        ->subject('Link to reset password for Snow Tricks!')
        
        // path of the Twig template to render
        ->htmlTemplate('email/reset_password.html.twig', [
            'user' => $user,
            'title' => "Modifier mot de passe"
        ])

        // pass variables (name => value) to the template
        ->context([
            'user' => $user,
            'headerImage' => "home",
            'title' => "Modifier mot de passe"
        ]);
        $mailerInterface->send($email);
    }
}
