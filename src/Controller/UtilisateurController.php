<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UtilisateurController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        $utilisateur = $this->getUser();
        if(!$utilisateur)
        {
            return $this->redirectToRoute('login');
        }
        if ($this->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('admin');
        }
        return $this->render('utilisateur/index.html.twig', [
            'controller_name' => 'UtilisateurController',
        ]);
    }
    /**
     * @Route("/new", name="utilisateur_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder, Session $session): Response
    {

        //test de sécurité, un utilisateur connecté ne peut pas s'inscrire
        $utilisateur = $this->getUser();
        if($utilisateur)
        {
            $session->set("message", "Vous ne pouvez pas créer un compte lorsque vous êtes connecté");
            return $this->redirectToRoute('membre');
        }

        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $utilisateur->setPassword($passwordEncoder->encodePassword($utilisateur, $utilisateur->getPassword()));
            /* uniquement pour créer un admin*/
            $role = ['ROLE_ADMIN'];
            $utilisateur->setRoles($role);
            $entityManager->persist($utilisateur);
            $entityManager->flush();

            return $this->redirectToRoute('utilisateur_index');
        }

        return $this->render('utilisateur/new.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form->createView(),
        ]);
    }
}
