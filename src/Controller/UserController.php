<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserPasswordType;
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

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
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
        $form = $this->createForm(UserPasswordType::class, $user);
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
                    $this->sendEmail($mailer, $user->getEmail(), $user->getToken());
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

    private function sendEmail(MailerInterface $mailerInterface, $emailTo, $token)
    {
        $emailFrom = $this->getParameter('app.admin_email');
        $link = '<a href="google.com"></a>';
        $email=(new Email())
        ->from($emailFrom)
        ->to($emailTo)
        ->subject('Link to reset password for Snow Tricks!')
        ->text($link);
        $mailerInterface->send($email);
    }
}
