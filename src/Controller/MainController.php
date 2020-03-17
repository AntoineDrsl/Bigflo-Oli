<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        $articles = $this->articleRepository->findAll();

        return $this->render('main/articles.html.twig', [
            'onPage' => 'articles',
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/article/{id}", name="article")
     */
    public function article($id)
    {
        $article = $this->articleRepository->find($id);

        return $this->render('main/article.html.twig', [
            'onPage' => '',
            'article' => $article
        ]);
    }
}
