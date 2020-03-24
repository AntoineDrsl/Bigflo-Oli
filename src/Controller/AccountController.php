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
    public function index() //Page du compte
    {
        // On récupère les infos du user et on les passe à la vue
        $user = $this->getUser();

        return $this->render('account/index.html.twig', [
            'onPage' => 'account',
            'user' => $user
        ]);
    }

    /**
     * @Route("/account/update", name="update-user")
     */
    public function updateUser(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager) //Page de modification des infos
    {        
        $user = $this->getUser();
        $previousAvatarName = $user->getAvatar();

        // On crée le formulaire défini dans 'Form/UserType.php' avec les infos du user actuel
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        // Si le formulaire est soumis et qu'il est valide (validations dans l'entity User), on traite les infos
        if ($form->isSubmitted() && $form->isValid()) {
    
            $user = $form->getData();

            // Si le user change son mot de passe, on l'encode de nouveau avant de l'enregistrer
            if($form->get('password')->getData() !== null) {
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
            }

            // Si le user change son avatar
            if($form->get('avatar')->getData() !== null) {

                // On supprime le fichier précédent avec la fonction définie dans l'entity User
                if($previousAvatarName) {
                    $user->deleteFileOnUpdate($previousAvatarName);
                }

                // On enregistre l'image dans le dossier défini dans 'config/services.yaml'
                $avatar = $user->getAvatar();
                $avatarName = md5(uniqid()).'.'.$avatar->guessExtension();
                $avatar->move($this->getParameter('upload_files_users'), $avatarName);
                $user->setAvatar($avatarName);        
            } else {
                // Sinon on remet l'ancien avatar
                if($previousAvatarName) {
                    $user->setAvatar($previousAvatarName);
                }
            }

            $entityManager->persist($user);
            $entityManager->flush();

            //On ajoute un msg de succès avant de rediriger vers le compte
            $this->addFlash('success', 'Vos informations ont bien été modifiées !');
            return $this->redirectToRoute('account');
        }

        // On fait apsser le formulaire et les infos du user à la vue
        return $this->render('account/update-user.html.twig', [
            'onPage' => 'account',
            'userForm' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route("/article/create", name="create-article")
     */
    public function createArticle(Request $request, EntityManagerInterface $entityManager) //Page de création d'article
    {
        $newArticle = new Article();

        // On crée le formulaire définie dans 'Form/ArticleType.php', en précisant qu'on utilise le groupe de validation 'création' défini dans l'entité Article (pour permettre au formulaire de modification d'article de ne pas demander de réupload une image à chaque fois)
        $form = $this->createForm(ArticleType::class, $newArticle, ['validation_groups' => ['creation']]);
        $form->handleRequest($request);

        // Si le formulaire est soumis et qu'il est valide (validations dans l'entity Article), on traite les infos
        if($form->isSubmitted() && $form->isValid()) {

            $newArticle = $form->getData();

             // On enregistre l'image dans le dossier défini dans 'config/services.yaml'
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

        // On passe le formulaire à la vue
        return $this->render('account/create-article.html.twig', [
            'onPage' => '',
            'articleForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/article/update/{id}", name="update-article")
     */
    public function updateArticle($id, Request $request, EntityManagerInterface $entityManager) // Page de modification d'article
    {

        // On cherche l'article en fonction de l'id
        $article = $this->articleRepository->find($id);

        // Si l'article existe, on affiche la page, sinon on redirige vers tous les articles
        if($article) {

            $previousImageName = $article->getImage();

            // On transforme l'image en objet pour pouvoir la passer dans le formulaire, puis on le crée ('Form/ArticleType.php')
            $article->setImage(new File($this->getParameter('upload_files_articles').'/'.$article->getImage()));
            $form = $this->createForm(ArticleType::class, $article);
            $form->handleRequest($request);

            // Si le formulaire est soumis et valide (validations dans l'entity Article), on traite les données
            if($form->isSubmitted() && $form->isValid()) {

                $article = $form->getData();

                // Si l'utilisateur a changé l'image, on la traite
                if($form->get('image')->getData() !== null) {

                    // On supprime le fichier précédent avec la fonction définie dans l'entity Article
                    if($previousImageName) {
                        $article->deleteFileOnUpdate($previousImageName);
                    }

                    // On upload la nouvelle image dans nos fichiers (définit dans 'config/services.yaml')
                    $image = $article->getImage();
                    $imageName = md5(uniqid()).'.'.$image->guessExtension();
                    $image->move($this->getParameter('upload_files_articles'), $imageName);
                    $article->setImage($imageName);
                } else {
                    // Sinon on remet l'ancienne image sou sforme de string
                    $article->setImage($previousImageName);
                }

                $entityManager->persist($article);
                $entityManager->flush();

                // On ajoute un message de succès avant de rediriger vers la page de l'article modifié
                $this->addFlash('success', 'L\'article a bien été modifié !');
                return $this->redirectToRoute('article', ['id' => $article->getId()]);
            }

            // On fait passer le formulaire et les infos de l'article à la vue
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
    public function validateArticles() //Page pour que l'admin voit tous les articles en attente de validation (pour rappel, les permissions sont gérées dans 'config/packages/security.yaml')
    {
        //On prend tous les articles en attente de validation (fonction définie dans ArticleRepository.php)
        $articles = $this->articleRepository->findAllWaiting();

        //On fait passer les articles à la vue
        return $this->render('account/validate-articles.html.twig', [
            'onPage' => '',
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/article/validate/{id}", name="validate-article")
     */
    public function validateArticle($id, EntityManagerInterface $entityManager) //route pour valider un article
    {   
        // On récupère l'article
        $article = $this->articleRepository->find($id);

        // Si l'article existe et n'est pas encore validé
        if($article && !$article->getState()) {

            // On passe sont état à true puis on l'enregistre
            $article->setState(true);
            $entityManager->persist($article);
            $entityManager->flush();

            // On ajoute un message de succès avant de rediriger
            $this->addFlash('success', 'L\'article a bien été validé !');
            return $this->redirectToRoute('validate-articles');

        } else {

            // Sinon, on ajoute un message d'erreur avant de rediriger
            $this->addFlash('error', 'L\'article n\'a pas été trouvé !');
            return $this->redirectToRoute('validate-articles');

        }
    }

    /**
     * @Route("/article/remove/{id}", name="remove-article")
     */
    public function removeArticle($id, EntityManagerInterface $entityManager) //route pour supprimer un article
    {
        // On récupère l'article en fonction de l'id dans l'URL
        $article = $this->articleRepository->find($id);

        // Si l'article existe
        if($article) {
            //On supprime l'image avec la fonction définie dans l'entité Article
            $article->deleteFile();

            // On supprime l'article de la bdd
            $entityManager->remove($article);
            $entityManager->flush();

            // On ajoute un message de succès avant de rediriger
            $this->addFlash('success', 'L\'article a bien été supprimé !');
            return $this->redirectToRoute('articles');

        } else {

            // Sinon, on ajoute un message d'erreur avant de rediriger
            $this->addFlash('error', 'L\'article n\'a pas été trouvé !');
            return $this->redirectToRoute('articles');

        }
    }

    /**
     * @Route("/comment/remove/{id}", name="remove-comment")
     */
    public function removeComment($id, EntityManagerInterface $entityManager) //route pour supprimer un commentaire
    {
        // On récupère le commentaire en fonction de l'id dans l'URL
        $comment = $this->commentRepository->find($id);

        // Si le commentaire existe
        if($comment) {

            // On le supprime de la bdd
            $entityManager->remove($comment);
            $entityManager->flush();

            // On ajoute un message de succès avant de rediriger
            $this->addFlash('success', 'Le commentaire a bien été supprimé !');
            return $this->redirectToRoute('article', ['id' => $comment->getArticle()->getId()]);

        } else {

            // Sinon on ajoute un message d'erreur avant de rediriger
            $this->addFlash('error', 'Le commentaire n\'a pas été trouvé');
            return $this->redirectToRoute('articles');

        }
    }
}
