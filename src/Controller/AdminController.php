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
    public function validation() : Response
    {

        return $this->render('admin/validation.html.twig');
    }
}