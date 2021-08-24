<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserEditPasswordType;
use App\Form\UserResetPasswordType;
use App\Form\ForgottenPasswordType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
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

            return $this->redirectToRoute('_profiler_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit_password.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
    
    /**
     * @Route("/forgotten/password", name="user_forgotten_password", methods={"GET","POST"})
     */
    public function forgottenPassword(Request $request, UserRepository $userRepository, MailerInterface $mailer): Response
    {
        $error_message ='';
        $form = $this->createForm(ForgottenPasswordType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $email = $form->get('email')->getData();
            $user = $userRepository->findOneByEmail($email);
            if($user === null)
            {
                $error_message = 'Cet utilisateur n\'existe pas';
                // $this->addFlash(
                //     'danger',
                //     "Cet utilisateur n\'existe pas"
                // );
            }
            else
            {
                //generate and give a unique token to user
                $user->setToken(md5(uniqid($user->getUsername(), true)));
                $this->getDoctrine()->getManager()->flush();
                try
                {   
                    $this->sendEmail($mailer, $user);
                    return $this->redirectToRoute('_profiler_home', [], Response::HTTP_SEE_OTHER);
                }
                catch(TransportException $e)
                {
                    $error_message = 'Impossible d\'envoyer le mail, veuillez réessayez ultérieurement.';
                }
            }
        }

        return $this->renderForm('user/forgotten_password.html.twig', [
            // 'user' => $user,
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
                    $this->getDoctrine()->getManager()->flush();
                    return $this->redirectToRoute('_profiler_home', [], Response::HTTP_SEE_OTHER);
                }
                else
                {
                    $error_message = "Impossible d\'effectuer le changement pour cause de mauvais token";
                }
            }
        }
        return $this->renderForm('user/reset_password_from_link.html.twig', [
             'error_message' => $error_message,
             'form' => $form,
        ]);
    }

    // /**
    //  * @Route("/delete", name="user_delete", methods={"POST"})
    //  */
    // public function delete(Request $request, User $user): Response
    // {
    //     if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
    //         $entityManager = $this->getDoctrine()->getManager();
    //         $entityManager->remove($user);
    //         $entityManager->flush();
    //     }

    //     return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
    // }

    private function sendEmail(MailerInterface $mailerInterface, $user)
    {
        $emailFrom = $this->getParameter('app.admin_email');
       
        $email=(new TemplatedEmail())
        ->from($emailFrom)
        ->to($user->getEmail())
        ->subject('Link to reset password for Snow Tricks!')
        
        // path of the Twig template to render
        ->htmlTemplate('email/reset_password.html.twig', [
            'user' => $user
        ])

        // pass variables (name => value) to the template
        ->context([
            'user' => $user
        ]);
        $mailerInterface->send($email);
    }
}
