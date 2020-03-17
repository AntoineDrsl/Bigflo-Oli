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
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager)
    {
        //On vérifie si l'utilisateur est déjà connecté, si oui on le redirige vers son compte
        if ($this->getUser()) {
            return $this->redirectToRoute('account');
        }
        
        //On traite le formulaire normalement
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            //email

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
