<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Trick;
use App\Form\CommentType;
use App\Form\Trick\TrickEditType;
use App\Form\Trick\TrickNewType;
use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/trick")
 */
class TrickController extends CustomController
{
     /**
     * @Route("/page/{slug}", name="trick_show", methods={"GET", "POST"})
     */
    public function show(Request $request, TrickRepository $trickRepo, $slug): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $id_post = $form->get("id_post")->getData();    
            $comment->setUser($this->getUser());
            $comment->setTrick($trickRepo->findBySlugDesc($slug));
            $comment->setCreatedAt(new DateTimeImmutable());
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('trick_show', ["slug"=> $slug], Response::HTTP_SEE_OTHER);
        }

        // return $this->renderForm('comment/new.html.twig', [
        //     // 'comment' => $comment,
        //     'form' => $form,
        // ]);

        $trick = $trickRepo->findBySlugDesc($slug);
        return $this->renderForm('trick/show.html.twig', [
            'trick' => $trick,
            'offsetComment' => 0,
            'limitComment' => 2,
            'user' => $this->getUser(),
            'title' => $trick->getTitle(),
            'form' => $form,
        ]);
    }

    /**
     * @Route("/new", name="trick_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $trick = new Trick();
        $form = $this->createForm(TrickNewType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            // $trick->setTitle($form->get("title")->getData());
            $trick->setSlug();
            $trick->setUser($this->getUser());
            $trick->setCreatedAt(new DateTimeImmutable());
            $entityManager->persist($trick);
            
            // if slug not exists in db, trick can be save in db
            //first flush for trick
            $entityManager->flush();

            // images part
            // save image only if UniqueConstraintViolationException
            // is not generated
            $images = $form->get("images")->getData();     
            foreach($images as $fileImage)
            {
                $image = $this->createImageEntity($fileImage);
                $this->saveImageInServer($fileImage, $image);
                $image->setTrick($trick);
                $entityManager->persist($image);
            }

            //video part
            $videos = $form->get("videos")->getData();
            foreach($videos as $video)
            {
                if( null !== $video)
                {
                    $video->setTrick($trick);
                    $entityManager->persist($video);
                }
            }
            // second flush for image and video
            $entityManager->flush();
            return $this->redirectToRoute('trick_show', ["slug"=>$trick->getSlug() ], Response::HTTP_SEE_OTHER); 
        }
        return $this->renderForm('trick/new.html.twig', [
            // 'error_message' => $error_message,
            'trick' => $trick,
            'form' => $form,
            'title' => "Créer Trick",
        ]);
    }

    /**
     * @Route("/{id}/edit", name="trick_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Trick $trick): Response
    {
        if($trick->getUser()->getId() !== $this->getUser()->getId())
        {
            return $this->renderForm('error/errorAccess.html.twig', [
                'title' => 'Droit insuffisant',
                'message' => 'Trick recherché: '.$trick->getTitle(),
            ]);
        }
        $form = $this->createForm(TrickEditType::class, $trick);
        $form->handleRequest($request);
        // $title_save = $trick->getTitle();
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            //can use this to
            $trick->setTitle($form->get("title")->getData());
            $trick->setSlug();
            // $trick->setUser($this->getUser());
            $trick->setUpdatedAt(new DateTimeImmutable());
            $entityManager->persist($trick);
            
            // if slug not exists in db, trick can be save in db
            // if exists, UniqueConstraintViolationException is generated
            //first flush for trick
            $entityManager->flush();

            // Ipage part (above) only if UniqueConstraintViolationException is not generated

            // Add images part
            $new_images = $form->get("new_images")->getData();     
            foreach($new_images as $fileImage)
            {
                $image = $this->createImageEntity($fileImage);
                $this->saveImageInServer($fileImage, $image);
                $image->setTrick($trick);
                $entityManager->persist($image);
            }

            // Delete existing images part
            $existing_images = $form->get("images");
            
            foreach($existing_images as $existing_image)
            {
                $image = $existing_image->getData();
                $delete = $existing_image->get("delete")->getData();
                if($delete)
                {
                    $entityManager->remove($image);
                    $this->deleteImageInServer($image);
                }
            }
            
            // Delete or edit existing videos part
            // if url is updated, this will be saved
            $existing_videos = $form->get("videos");
            
            foreach($existing_videos as $existing_video)
            {
                $video = $existing_video->getData();
                $delete = $existing_video->get("delete")->getData();
                if($delete)
                {
                    $entityManager->remove($video);
                }
            }

            //new video part
            $new_videos = $form->get("new_videos")->getData();
            foreach($new_videos as $video)
            {
                if( null !== $video)
                {
                    $video->setTrick($trick);
                    $entityManager->persist($video);
                }
            }
            
            // second flush for images and video
            $entityManager->flush();
            return $this->redirectToRoute('trick_show', ["slug"=>$trick->getSlug() ], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form,
            'title' => $trick->getTitle(),
        ]);
    }

    /**
     * @Route("/{id}", name="trick_delete", methods={"POST"})
     */
    public function delete(Request $request, Trick $trick, CommentRepository $commentRepository): Response
    {
        if($trick->getUser()->getId() !== $this->getUser()->getId())
        {
            return $this->renderForm('error/errorAccess.html.twig', [
                'title' => 'Droit insuffisant',
                'message' => 'Trick recherché: '.$trick->getTitle(),
            ]);
        }
        if ($this->isCsrfTokenValid(
                    'delete'.$trick->getId(),
                     $request->request->get('_token'))
                    )
            {
            $entityManager = $this->getDoctrine()->getManager();
            
            foreach($trick->getImages() as $image)
            {
                $entityManager->remove($image);
                $this->deleteImageInServer($image);
            }

            foreach($trick->getVideos() as $video)
            {
                $entityManager->remove($video);
            }
            
            $comments = $commentRepository->findBy(["trick"=> $trick]);
            foreach($comments as $comment){
                $entityManager->remove($comment);
            }
            $entityManager->remove($trick);
            $entityManager->flush();
        }
        return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
    }
    
    // private functions
    private function createImageEntity($fileImage)
    {
        //ex name: rabbit.jpg
        $name_with_extension = $fileImage->getClientOriginalName();
        //name become: rabbit
        $name_without_extension = pathinfo($name_with_extension, PATHINFO_FILENAME);
        //generate unique name
        $fileName = $name_without_extension . "-" .md5(uniqid()) . '.' .$fileImage->guessExtension();
        
        $image = new Image();
        $image->setName($fileName);
        return $image;
    }
    
    private function saveImageInServer($fileImage, Image $image)
    {
        $fileImage->move(
            $this->getParameter('image_trick'), $image->getName());
    }

    private function deleteImageInServer(Image $image)
    {
        $fileSystem = new Filesystem();
        $fileSystem->remove($this->getParameter("image_trick") . '/' . $image->getName());
    }

}
