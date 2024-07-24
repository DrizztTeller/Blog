<?php
namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{

    #[Route('/', name: 'index')]
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findAll();
        // dd($posts);


        // $post = $postRepository->find(1);
        // dd($post);


        return $this->render('main/index.html.twig', ['posts' => $posts ?? []]);
    }

    // {id} : veut dire que c'est un paramètre et symfony est intelligent et c'est que l'id vient du post    methods = pour la sécurité pour dire que sur cette page on ne peut recevoir que du GET et pas de POST donc mm si un hacker crée un formulaire sur notre page, on est secure

    // #[Route('/{id}', name: 'show', methods: ['GET'])]
//methode php classique on met $id en paramètre
    // public function show($id, PostRepository $postRepository): Response
    // {
    //     $post = $postRepository->find($id);
    //     return $this->render('main/show.html.twig', ['post' => $post]);
    // }


    #[Route('/{id<\d+>}', name: 'show', methods: ['GET'])]
    //methode php native quand on met UserRepository $userRepository pas besoin car symfony est intelligent et récupère directement l'user grâce à author_id
    public function show(
        Post $post,
        PostRepository $postRepository,
        // UserRepository $userRepository
    ): Response {
        // $user = $userRepository->find($post->getAuthor());
        $post = $postRepository->find($post->getId());
        return $this->render('main/show.html.twig', [
            'post' => $post,
            // "user" => $user
        ]);
    }
}