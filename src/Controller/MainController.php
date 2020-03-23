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
    public function index() //Page d'acceuil
    {
        // On rend le template en faisant passer la variable onPage qui définie sur quel onglet de la navbar on se trouve
        return $this->render('main/index.html.twig', [
            'onPage' => 'home',
        ]);
    }

    /**
     * @Route("/biographie", name="bio")
     */
    public function bio() //Page de biographie
    {
        return $this->render('main/bio.html.twig', [
            'onPage' => 'bio'
        ]);
    }

    /**
     * @Route("/articles", name="articles")
     */
    public function articles() //Page listant tous les articles
    {
        // On cherche tous les articles validés (fonction définit dans ArticleRepository.php)
        $articles = $this->articleRepository->findAllValidated();

        // On passe tous les articles à la vue
        return $this->render('main/articles.html.twig', [
            'onPage' => 'articles',
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/article/{id}", name="article")
     */
    public function article($id, Request $request, EntityManagerInterface $entityManager) //Page d'un article
    {
        // On cherche l'article en fonction de l'id passée dans l'URL
        $article = $this->articleRepository->find($id);

        //On affiche la page si l'article existe et qu'il est validé, ou si il existe et que le user est un admin, sinon on redirige le user
        if(($article && $article->getState()) || ($article && $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))) {

            $user = $this->getUser();
            $comments = $article->getComments();
            $newComment = new Comment();

            // On crée le formulaire défini dans 'Form/Comment.php'
            $form = $this->createForm(CommentType::class, $newComment);
            $form->handleRequest($request);

            // Si le formulaire est soumis, et qu'il est valide (les validations sont dans l'entity Comment), on traite les infos
            if($form->isSubmitted() && $form->isValid()) {

                // On récupère les infos du formulaire, on ajoute le user et l'article liés au commentaire, puis on l'inscrit dans la base de données
                $newComment = $form->getData();
                $newComment->setArticle($article);
                $newComment->setUser($user);

                $entityManager->persist($newComment);
                $entityManager->flush();

                // On redirige sur la même page
                return $this->redirectToRoute('article', ['id' => $article->getId()]);
            }

            // On passe les infos de l'article, le formulaire et les commentaires à la vue
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
    public function infos() //Page des conditions d'utilisation
    {
        // Même vide, on doit faire passer onPage pour que twig ne se demande pas pourquoi il ne trouve pas la variable
        return $this->render('main/infos.html.twig', [
            'onPage' => ''
        ]);
    }
}
