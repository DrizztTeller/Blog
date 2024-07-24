<?php
namespace App\Controller\Admin\Blog;

use App\Entity\Post;
use App\Form\PostType;
use App\Form\Post1Type;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

//préfixe pour nos routes
#[Route("/admin/blog", name: 'admin_blog_')]
final class BlogController extends AbstractController

    //final pour dire que cette classe ne peut pas être héritée

    //Repository : pour la lecture, accéder à la BDD pour lire uniquement
{

    // admin_blog_index 
    // #[Route('/', name: 'index')]
    // public function index(
    //     PostRepository $postRepository,
    //     // UserRepository $userRepository, 
    //     // EntityManagerInterface $entityMI
    // ): Response {
    //     // $author = $userRepository->find(1);
    //     // dd($author);
    //     // $author = new User();
    //     // $post = new Post();
    //     // $post->setTitle("New Post")
    //     //     ->setSlug("#new")
    //     //     ->setSummary("New Post summary")
    //     //     ->setContent("new post content")
    //     //     ->setPublishedAt(new \DateTimeImmutable)
    //     //     ->setAuthor($author);
    //     // $entityMI->persist($post);
    //     // $entityMI->flush();
    //     $posts = $postRepository->findAll();
    //     return $this->render('admin/blog/index.html.twig', ['posts' => $posts]);
    // }

    #[Route('/', name: 'index')]
    public function index(
        PostRepository $postRepository,
    ): Response {
        $posts = $postRepository->findAll();
        return $this->render('admin/blog/index.html.twig', ['posts' => $posts]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request,EntityManagerInterface $entityMI, Security $security): Response 
    {
        $user = $security->getUser();
        $post = new Post();
        $form = $this->createForm(Post1Type::class, $post);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post->setPublishedAt(new \DateTimeImmutable)
                 ->setAuthor($user);
            $entityMI->persist($post);
            $entityMI->flush();

            return $this->redirectToRoute('admin_blog_index', status: Response::HTTP_SEE_OTHER);
        }
        return $this->render('admin/blog/new.html.twig', ["form" => $form]);
    }
    
    
    // :: pour utiliser une méthode ou une propiété abstraite sans besoin d'instencier la classe
    // #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    // public function new(
    //     UserRepository $userRepository, 
    //     Request $request,
    //     EntityManagerInterface $entityMI
    // ): Response {

    //     // methode brute
    //     // $author = $userRepository->find(1);
    //     // // dd($author);
    //     // $post = new Post();
    //     // $post->setTitle("New Test2")
    //     //     ->setSlug("#tes")
    //     //     ->setSummary("New Test summary")
    //     //     ->setContent("new Test content")
    //     //     ->setPublishedAt(new \DateTimeImmutable)
    //     //     ->setAuthor($author);
    //     // $entityMI->persist($post);
    //     // $entityMI->flush();
    //     // return $this->render('admin/blog/new.html.twig');


    //     // methode dynamique avec formulaire

    //     $post = new Post();
    //     $form = $this->createForm(PostType::class, $post);

    //     $form->handleRequest($request);
    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $author = $userRepository->find(1);
    //         // dd($author);
    //         $post->setPublishedAt(new \DateTimeImmutable)
    //             ->setAuthor($author);
    //         $entityMI->persist($post);
    //         $entityMI->flush();

    //         // :: pour utiliser une méthode ou une propiété abstraite sans besoin d'instencier la classe
    //         return $this->redirectToRoute('admin_blog_index', status: Response::HTTP_SEE_OTHER);
    //     }
    //     return $this->render('admin/blog/new.html.twig', ["form" => $form]);


    // }


    // <\> : expression régulière ici d  pour dire que c'est un chiffre(digit)  et le + pour dire qu'il y a un ou plusieurs chifffres    w pour dire que c'est un mot(word)
    #[Route('/{id<\d+>}', name: 'show', methods: ['GET'])]
    public function show(Post $post, PostRepository $postRepository): Response
    {
        $post = $postRepository->find($post->getId());
        return $this->render('admin/blog/show.html.twig', ['post' => $post]);
    }



    #[Route('/edit/{id<\d+>}', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Post $post, PostRepository $postRepository, EntityManagerInterface $entityMI, Request $request): Response
    {
        $post = $postRepository->find($post->getId());
        // dd($post);
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $post->setUpdatedAt(new \DateTimeImmutable);
            $entityMI->persist($post);
            $entityMI->flush();

            // :: pour utiliser une méthode ou une propiété abstraite sans besoin d'instencier la classe
            return $this->redirectToRoute('admin_blog_index', status: Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/blog/edit.html.twig', ['form' => $form, 'post' => $post]);
    }


    #[Route('/del/{id<\d+>}', name: 'del', methods: ['GET', 'POST'])]
    public function del(Post $post, PostRepository $postRepository, EntityManagerInterface $entityMI): Response
    {
        $post = $postRepository->find($post->getId());
        // dd($post);
        $entityMI->remove($post);
        $entityMI->flush();
        return $this->redirectToRoute('admin_blog_index', status: Response::HTTP_SEE_OTHER);
    }
}
