<?php



namespace App\Controller;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/admin" )
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="admin")
     */
    public function index(): Response
    {
        $utilisateur = $this->getUser();
        if(!$utilisateur)
        {
            return $this->redirectToRoute('login');
        }
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
    /**
     * @Route("/validation", name="validation")
     */
    public function validation(UtilisateurRepository $utilisateurRepository) : Response
    {
        $utilisateurs = $utilisateurRepository->findAll();
        var_dump();
        return $this->render('admin/validation.html.twig');
    }
}