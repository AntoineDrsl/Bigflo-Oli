<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->render('main/index.html.twig', [
            'onPage' => 'home', //pour navbar.html.twig
        ]);
    }

    /**
     * @Route("/biographie", name="bio")
     */
    public function bio()
    {
        return $this->render('main/bio.html.twig', [
            'onPage' => 'bio'
        ]);
    }

    /**
     * @Route("/articles", name="articles")
     */
    public function articles()
    {
        // On utilise une fonction qu'on définit dans ArticleRepository.php
        $articles = $this->articleRepository->findAllByNew();

        return $this->render('main/articles.html.twig', [
            'onPage' => 'articles',
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/article/{id}", name="article")
     */
    public function article($id, Request $request, EntityManagerInterface $entityManager)
    {
        $article = $this->articleRepository->find($id);
        $user = $this->getUser();
        $comments = $article->getComments();
        $newComment = new Comment();
        dump($comments);

        $form = $this->createForm(CommentType::class, $newComment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $newComment = $form->getData();
            $newComment->setArticle($article);
            $newComment->setUser($user);

            $entityManager->persist($newComment);
            $entityManager->flush();

            return $this->redirectToRoute('article', ['id' => $article->getId()]);
        }

        return $this->render('main/article.html.twig', [
            'onPage' => '',
            'article' => $article,
            'commentForm' => $form->createView(),
            'comments' => $comments
        ]);
    }
}
