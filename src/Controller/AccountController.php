<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
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
     * @Route("/article/create", name="create-article")
     */
    public function createArticle(Request $request, EntityManagerInterface $entityManager)
    {
        $newArticle = new Article();
        $form = $this->createForm(ArticleType::class, $newArticle);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $newArticle = $form->getData();

            $image = $newArticle->getImage();
            $imageName = md5(uniqid()).'.'.$image->guessExtension();
            $image->move($this->getParameter('upload_files_articles'), $imageName);
            $newArticle->setImage($imageName);

            $entityManager->persist($newArticle);
            $entityManager->flush();

            $this->addFlash('success', 'Votre article à bien été créé !');
            return $this->redirectToRoute('articles');
        }

        return $this->render('account/create-article.html.twig', [
            'onPage' => '',
            'articleForm' => $form->createView()
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
            $this->addFlash('success', 'Votre compte a bien été modifié !');
            return $this->redirectToRoute('account');
        }

        return $this->render('account/update-user.html.twig', [
            'onPage' => 'account',
            'userForm' => $form->createView(),
            'user' => $user
        ]);
    }
}
