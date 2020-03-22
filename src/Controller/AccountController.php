<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Form\UserType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    public function __construct(ArticleRepository $articleRepository, CommentRepository $commentRepository)
    {
        $this->articleRepository = $articleRepository;
        $this->commentRepository = $commentRepository;
    }

    /**
     * @Route("/account", name="account")
     */
    public function index()
    {
        $user = $this->getUser();

        return $this->render('account/index.html.twig', [
            'onPage' => 'account',
            'user' => $user
        ]);
    }

    /**
     * @Route("/account/update", name="update-user")
     */
    public function updateUser(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager)
    {        
        $user = $this->getUser();
        $previousAvatarName = $user->getAvatar();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
    
            $user = $form->getData();

            // encode the plain password
            if($form->get('password')->getData() !== null) {
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
            }

            if($form->get('avatar')->getData() !== null) {

                // On supprime le fichier précédent avec la fonction définie dans l'entity Article
                if($previousAvatarName) {
                    $user->deleteFileOnUpdate($previousAvatarName);
                }

                $avatar = $user->getAvatar();
                $avatarName = md5(uniqid()).'.'.$avatar->guessExtension();
                $avatar->move($this->getParameter('upload_files_users'), $avatarName);
                $user->setAvatar($avatarName);        
            } else {
                if($previousAvatarName) {
                    $user->setAvatar($previousAvatarName);
                }
            }

            $entityManager->persist($user);
            $entityManager->flush();

            //On ajoute un msg de succès avant de rediriger
            $this->addFlash('success', 'Vos informations ont bien été modifiées !');
            return $this->redirectToRoute('account');
        }

        return $this->render('account/update-user.html.twig', [
            'onPage' => 'account',
            'userForm' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route("/article/create", name="create-article")
     */
    public function createArticle(Request $request, EntityManagerInterface $entityManager)
    {
        $newArticle = new Article();

        $form = $this->createForm(ArticleType::class, $newArticle, ['validation_groups' => ['creation']]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $newArticle = $form->getData();

            $image = $newArticle->getImage();
            $imageName = md5(uniqid()).'.'.$image->guessExtension();
            $image->move($this->getParameter('upload_files_articles'), $imageName);
            $newArticle->setImage($imageName);

            //Si le user est admin, l'article est directement validé, sinon il passe en attente de validation
            if($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                $newArticle->setState(true);
            } else {
                $newArticle->setState(false);
            }

            $entityManager->persist($newArticle);
            $entityManager->flush();

            //On change le message en fonction du statut du user
            if($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                $this->addFlash('success', 'Votre article a bien été créé !');
            } else {
                $this->addFlash('success', 'Votre article a bien été créé ! Un administrateur va bientôt le valider.');
            }

            return $this->redirectToRoute('articles');
        }

        return $this->render('account/create-article.html.twig', [
            'onPage' => '',
            'articleForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/article/update/{id}", name="update-article")
     */
    public function updateArticle($id, Request $request, EntityManagerInterface $entityManager)
    {

        $article = $this->articleRepository->find($id);

        if($article) {

            $previousImageName = $article->getImage();

            $article->setImage(new File($this->getParameter('upload_files_articles').'/'.$article->getImage()));
            $form = $this->createForm(ArticleType::class, $article);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {

                $article = $form->getData();

                if($form->get('image')->getData() !== null) {

                    // On supprime le fichier précédent avec la fonction définie dans l'entity Article
                    if($previousImageName) {
                        $article->deleteFileOnUpdate($previousImageName);
                    }

                    // On uplod la nouvelle image dans nos fichiers
                    $image = $article->getImage();
                    $imageName = md5(uniqid()).'.'.$image->guessExtension();
                    $image->move($this->getParameter('upload_files_articles'), $imageName);
                    $article->setImage($imageName);
                } else {
                    $article->setImage($previousImageName);
                }

                $entityManager->persist($article);
                $entityManager->flush();

                $this->addFlash('success', 'L\'article a bien été modifié !');
                return $this->redirectToRoute('article', ['id' => $article->getId()]);
            }

            return $this->render('account/update-article.html.twig', [
                'onPage' => '',
                'articleForm' => $form->createView(),
                'article' => $article
            ]);

        } else {

            return $this->redirectToRoute('articles');

        }
        
    }

    /**
     * @Route("/articles/validate", name="validate-articles")
     */
    public function validateArticles()
    {
        //On prend tous les articles en attente de validation (fonction définie dans ArticleRepository.php)
        $articles = $this->articleRepository->findAllWaiting();

        return $this->render('account/validate-articles.html.twig', [
            'onPage' => '',
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/article/validate/{id}", name="validate-article")
     */
    public function validateArticle($id, EntityManagerInterface $entityManager)
    {   
        // On récupère l'article
        $article = $this->articleRepository->find($id);

        // Si l'article existe et n'est pas encore validé
        if($article && !$article->getState()) {

            $article->setState(true);
            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('success', 'L\'article a bien été validé !');
            return $this->redirectToRoute('validate-articles');

        } else {

            $this->addFlash('error', 'L\'article n\'a pas été trouvé !');
            return $this->redirectToRoute('validate-articles');

        }
    }

    /**
     * @Route("/article/remove/{id}", name="remove-article")
     */
    public function removeArticle($id, EntityManagerInterface $entityManager)
    {
        $article = $this->articleRepository->find($id);

        if($article) {
            //On supprime l'image avec la fonction définie dans l'entité Article
            $article->deleteFile();

            $entityManager->remove($article);
            $entityManager->flush();

            $this->addFlash('success', 'L\'article a bien été supprimé !');
            return $this->redirectToRoute('articles');

        } else {

            $this->addFlash('error', 'L\'article n\'a pas été trouvé !');
            return $this->redirectToRoute('articles');

        }
    }

    /**
     * @Route("/comment/remove/{id}", name="remove-comment")
     */
    public function removeComment($id, EntityManagerInterface $entityManager)
    {
        $comment = $this->commentRepository->find($id);

        if($comment) {

            $entityManager->remove($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Le commentaire a bien été supprimé !');
            return $this->redirectToRoute('article', ['id' => $comment->getArticle()->getId()]);

        } else {

            $this->addFlash('error', 'Le commentaire n\'a pas été trouvé');
            return $this->redirectToRoute('articles');

        }
    }
}
