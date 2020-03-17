<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    /**
     * @Route("/account", name="account")
     */
    public function index()
    {
        return $this->render('account/index.html.twig', [
            'onPage' => 'account',
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

            return $this->redirectToRoute('home');
        }

        return $this->render('account/create-article.html.twig', [
            'onPage' => '',
            'articleForm' => $form->createView()
        ]);
    }
}
