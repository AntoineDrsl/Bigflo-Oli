<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response // Page de connexion (créée par Symfony)
    {
        //On vérifie si l'utilisateur est déjà connecté, si oui on le redirige vers son compte
        if ($this->getUser()) {
            return $this->redirectToRoute('account');
        }

        //On récupère les éventuels messages d'erreur et la dernièere valeur entré par l'utilisateur
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        //On retourne la vue (toutes les vérifications se passent dans LoginFormAuthenticator)
        return $this->render('security/login.html.twig', [
            'onPage' => 'account',
            'last_username' => $lastUsername,
            'error' => $error
            ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout() // Route de déconnexion (créée par Symfony)
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
