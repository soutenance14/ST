<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/comment")
 */
class CommentController extends AbstractController
{
    /**
     * @Route("/", name="comment_index", methods={"GET"})
     */
    public function index(CommentRepository $commentRepository): Response
    {
        return $this->render('comment/index.html.twig', [
            'comments' => $commentRepository->findAll(),
        ]);
    }
    
    // /**
    //  * @Route("/{trickId}/{limit}/{offset}", name="comment_more", methods={"GET", "POST"})
    //  */
    // public function more(CommentRepository $commentRepository, $trickId, $limit, $offset): Response
    // {
    //     $fields = array('trick' => $trickId);
    //     $orderBy = array('createdAt' => 'DESC');
    //     return $this->render('comment/index.html.twig', [
    //         'comments' => $commentRepository->findBy(
    //             $fields, $orderBy, $limit, $offset),
    //     ]);
        
    // }
    
    /**
     * @Route("/{trickId}/{limit}/{offset}", name="comment_more", methods={"POST"})
     */
    public function more(CommentRepository $commentRepository, $trickId, $limit, $offset)
    {
        
        $data = $commentRepository->findComments(
        $trickId, $limit, $offset);
        
        return new Response(json_encode([
            "message"=>"success",
            "data"=>$data]));
    }

    /**
     * @Route("/new", name="comment_new", methods={"GET","POST"})
     */
    public function new(Request $request, TrickRepository $trickRepository): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $id_post = $form->get("id_post")->getData();    
            $comment->setUser($this->getUser());
            // $comment->setTrick($trickRepository->findOneById($id_post));
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('comment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('comment/new.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="comment_show", methods={"GET"})
     */
    public function show(Comment $comment): Response
    {
        return $this->render('comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="comment_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Comment $comment): Response
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('comment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="comment_delete", methods={"POST"})
     */
    public function delete(Request $request, Comment $comment): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('comment_index', [], Response::HTTP_SEE_OTHER);
    }
}
