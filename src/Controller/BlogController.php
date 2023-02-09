<?php

namespace App\Controller;

//Utilisation des classes
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\PostRepository;
use App\Repository\CommentRepository;
use App\Entity\Post;
use App\Entity\Comment;
use App\Form\CommentFormType;
use Doctrine\ORM\EntityManagerInterface;

class BlogController extends AbstractController
{
    //Ajouter le constructeur pour pouvoir enregistrer le commentaire
    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    //Indique à Symfony l'url pour executer l'action définie, soit la méthode index()
    #[Route('/', name: 'app_blog', defaults: ['page' => '1'], methods: ['GET'])]
    //Indique $page sera automatiquement générée grâce au Regex
    #[Route('/page/{page<[1-9]\d{0,8}>}', name: 'app_blog_page', methods: ['GET'])]

    //Récupération des objets Request, PostRepository
    public function index(PostRepository $postRepository, int $page = 1): Response
    {

        $posts = $postRepository->getPostPaginator($page);

        return $this->render('blog/index.html.twig', [
            'publications' => $posts,
            'decalageSuivant' => $postRepository->getDecalageSuivant() ,
            'current_page' => $page

        ]);
    }

    #Une instance de Post soit injectée dans la méthode en se basant sur l'{id} passé dans le chemin de la requête
    #[Route('/publication/{id}', name: 'publication')]
    public function show(Request $request, Post $post, CommentRepository $commentRepository): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $commentRepository->getCommentPaginator($post, $offset);

        $comment = new Comment();
        //Méthode CreateForm() faisant partie d'AbstractController qui facilite la création de formulaires
        $form = $this->createForm(CommentFormType::class, $comment);

        //Traiter le formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $comment->setPost($post);
            $comment->setPublishedAt(new \DateTimeImmutable());
            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            return $this->redirectToRoute('publication', ['id' => $post->getId()]);
        }

        return $this->render('blog/show.html.twig', [
            'comments' => $paginator,
            'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
            'publication' => $post,
            'comment_form' => $form->createView(),
        ]);


    }


}
