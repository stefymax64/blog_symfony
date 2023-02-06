<?php

namespace App\Controller;

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
    #[Route('/', name: 'app_blog', defaults: ['page' => '1'], methods: ['GET'])]
    #[Route('/page/{page<[1-9]\d{0,8}>}', name: 'app_blog_page', methods: ['GET'])]
    public function index(int $page, PostRepository $posts): Response
    {
        $latestPosts = $posts->findAll();
        return $this->render('blog/index.html.twig', [
            'publications' => $latestPosts,
        ]);
    }

    #[Route('/publication/{id}', name: 'publication')]
    public function show(Post $post, CommentRepository $commentRepository): Response{
        return $this->render('blog/show.html.twig',
        ['publication'=>$post,
            'comments'=>$commentRepository->findBy(['post'=>$post],
            ['publishedAt'=>'DESC']),
            ]);
    }


}
