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
        // On cherche tous les articles validés (fonction définit dans ArticleRepository.php)
        $articles = $this->articleRepository->findAllValidated();

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

        //On affiche la page si l'article existe et qu'il est validé, ou si il existe et que le user est un admin
        if(($article && $article->getState()) || ($article && $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))) {

            $user = $this->getUser();
            $comments = $article->getComments();
            $newComment = new Comment();

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
        
        } else {

            return $this->redirectToRoute('articles');

        }
    }

    /**
     * @Route("/condition-utilisation", name="infos")
     */
    public function infos()
    {
        return $this->render('main/infos.html.twig', [
            'onPage' => ''
        ]);
    }
}
