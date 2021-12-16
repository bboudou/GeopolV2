<?php



namespace App\Controller;
use App\Repository\UtilisateurRepository;
use App\Repository\CongesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @IsGranted("ROLE_ADMIN")
 */
/**
 * @Route("/admin" )
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="admin")
     */
    public function index(UtilisateurRepository $utilisateurRepository): Response
    {
        $utilisateur = $this->getUser();
        if(!$utilisateur)
        {
            return $this->redirectToRoute('login');
        }
        $utilisateurs = $utilisateurRepository->findAll();
        return $this->render('admin/index.html.twig', [
            'utilisateurs' => $utilisateurs,
        ]);
    }
    /**
     * @Route("/validation", name="validation")
     */
    public function validation(CongesRepository $congesRepository) : Response
    {
        $conges=$congesRepository->findAll();
        return $this->render('admin/validation.html.twig',['conges'=>$conges]);
    }
    /**
     * @Route("/valideconge", name="valideconge")
     */
    public function validations(CongesRepository $congesRepository) : Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $conge=$congesRepository->find($_GET['idget']);
        $conge->setValide(true);
        $entityManager->persist($conge);
        $entityManager->flush();
        $conges=$congesRepository->findAll();
        return $this->redirectToRoute('validation');
    }
}