<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager) //Page de création de compte
    {
        //On vérifie si l'utilisateur est déjà connecté, si oui on le redirige vers son compte
        if ($this->getUser()) {
            return $this->redirectToRoute('account');
        }
        
        $user = new User();
        //On crée le formulaire définie dans 'Form/RegistrationFormType' (créé automatiquement par Symfony)
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        //On traite le formulaire normalement, en encodant juste le mot de passe avant d'enregistrer le user dans la bdd
        if ($form->isSubmitted() && $form->isValid()) {
            // On encode le mot de passe
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            //On ajoute un msg de succès avant de rediriger
            $this->addFlash('success', 'Votre compte a bien été créé, vous pouvez maintenant vous connecter !');
            return $this->redirectToRoute('login');
        }

        return $this->render('registration/register.html.twig', [
            'onPage' => 'account',
            'registrationForm' => $form->createView(),
        ]);
    }
}
