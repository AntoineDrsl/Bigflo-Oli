<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
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
}
