<?php

namespace App\Controller;

use App\Entity\Conges;
use App\Entity\Utilisateur;
use App\Form\CongesType;
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
        $nom=$utilisateur->getNom();
        $prenom= $utilisateur->getPrenom();
        $acquis=$utilisateur->getAcquis();
        return $this->render('utilisateur/index.html.twig', [
            'nom' => $nom,
            'prenom' =>$prenom,
            'conges' => $acquis,
        ]);
    }
    /**
     * @Route("/Conges", name="conges")
     */
    public function conges(Request $request): Response
    {
        $utilisateur=$this->getUser();
        $conges = new Conges();
        $form = $this->createForm(CongesType::class, $conges);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $conges->setIdUtilisateur($utilisateur->getId());
            $conges->setValide(false);
            $conges->setExceptionel(false);
            $entityManager->persist($conges);
            $entityManager->flush();
        }
        return $this->render('utilisateur/conges.html.twig', ['form' => $form->createView()]);
    }





    //Pour ajouter un utilisateur seulement accessible avec le lien
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
            $role = ['ROLE_USER'];
            $utilisateur->setRoles($role);
            $utilisateur->setNom('TOTO');
            $utilisateur->setPrenom('Titi');
            $utilisateur->setAcquis(2.5);
            $entityManager->persist($utilisateur);
            $entityManager->flush();

            return $this->redirectToRoute('conges');
        }

        return $this->render('utilisateur/new.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form->createView(),
        ]);
    }
}
