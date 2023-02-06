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
    public function index(Request $request, int $page, PostRepository $posts): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $PostRepository->getPostRepository($post, $offset);

        $latestPosts = $posts->findAll();
        return $this->render('blog/index.html.twig', [
            'publication'=>$post,
            'comments'=>$paginator,
            'previous'=>$offset - PostRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + PostRepository::PAGINATOR_PER_PAGE),
        ]);
    }

    #[Route('/publication/{id}', name: 'publication')]
    public function show(Request $request, Post $post, CommentRepository $commentRepository): Response{
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
