<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\Post1Type;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/post')]
class PostController extends AbstractController
{
    #[Route('/', name: 'app_post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }
    
    #[Route('/my-posts', name: 'posts', methods: ['GET'])]
    public function posts(PostRepository $postRepository, Security $security): Response
    {
        $user = $security->getUser();
        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findBy(['author' => $user] ?? []),
        ]);
    }

    #[Route('/new', name: 'app_post_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $post = new Post();
        $form = $this->createForm(Post1Type::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_post_show', methods: ['GET'])]
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager, Security $security): Response
    {
        $user = $security->getUser();
        if (!$user instanceof User) {
            $this->addFlash('warning', 'Vous devez être connecté pour modifier le post.');
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }

        if ($post->getAuthor() === $user) {
            $form = $this->createForm(Post1Type::class, $post);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $post->setUpdatedAt(new \DateTimeImmutable);
                $entityManager->persist($post);
                $entityManager->flush();

                return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('post/edit.html.twig', [
                'post' => $post,
                'form' => $form,
            ]);
        } else {
            $this->addFlash('warning', 'La route que vous cherchez n\existe pas !');
            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    #[Route('/{id<\d+>}', name: 'app_post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
    }
}
