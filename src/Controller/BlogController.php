<?php

namespace App\Controller;

//Utilisation des classes
use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\PostRepository;
use App\Repository\CommentRepository;
use App\Repository\TagRepository;

class BlogController extends AbstractController
{
    //Indique à Symfony l'url pour executer l'action définie, soit la méthode index()
    #[Route('/', name: 'app_blog', defaults: ['page' => '1'], methods: ['GET'])]
    //Indique $page sera automatiquement générée grâce au Regex
    #[Route('/page/{page<[1-9]\d{0,8}>}', name: 'app_blog_page', methods: ['GET'])]

    //Récupération des objets Request, PostRepository
    public function index(Request $request, int $page,  PostRepository $postRepository): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $postRepository->getPostPaginator($offset);

        //retourne la vue avec la méthode render() et lui passe un array de $
        return $this->render('blog/index.html.twig', [

            'publications'=>$paginator,
            'previous'=>$offset - PostRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + PostRepository::PAGINATOR_PER_PAGE),
        ]);
    }

    #Une instance de Post soit injectée dans la méthode en se basant sur l'{id} passé dans le chemin de la requête
    #[Route('/publication/{id}', name: 'publication')]
    public function show(Request $request, Post $post, CommentRepository $commentRepository): Response{
        //Le contrôleur récupère la valeur du décalage offset depuis les paramètres de l'url ($request->query) sous forme d'entier (getInt())
        //Par défaut offset est à 0 si le paramètre n'est pas défini
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $commentRepository->getCommentPaginator($post, $offset);

        return $this->render('blog/show.html.twig',
        [
            'publication'=>$post,
            'comments'=>$paginator,
            'previous'=>$offset - CommentRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
            ]);
    }


}
