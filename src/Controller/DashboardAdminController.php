<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AdminEditFormType;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin')] // que les user role admin peuvent accéder à cette page
final class DashboardAdminController extends AbstractController
{
    #[Route('/dashboardAdmin', name: 'app_dashboard_admin')]
    #[IsGranted('ROLE_ADMIN')] // que les user role admin peuvent accéder à cette page
    public function index(EntityManagerInterface $entityManager): Response
    {
        $dataBs= $entityManager -> getRepository(User::class)-> findall(); // récupérer tous les utilisateurs de la base de données
        return $this->render('dashboard_admin/index.html.twig', [
            'controller_name' => 'DashboardAdminController',
            'dataBs' => $dataBs, // passer les données à la vue
        ]);
    }
    
    #[Route('/edit/{id}', name: 'app_edit_form')]
    public function editForm($id ,Request $request, EntityManagerInterface $entityManager ): Response
    {
        $users = $entityManager->getRepository(User::class)->find($id);
        $form = $this->createForm(AdminEditFormType::class, $users );
        $form->handleRequest($request);

        // dd($form->getData(), $users);
        if ($form->isSubmitted() && $form->isValid()) {  
            $entityManager->persist($users);
            $entityManager-> flush();
            
            $this->addFlash('notice','Modification réussi !!');
            return $this->redirectToRoute('app_dashboard_admin');
        }  
        // var_dump($form->isSubmitted() && $form->isValid());
        return $this->render('dashboard_admin/adminEdit.html.twig', [
            'controller_name' => 'DashboardAdminController',
            'form' => $form

        ]);
    }
    
    #[Route('/delete/{id}', name: 'app_delete_form')]
    public function deleteForm($id, EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)->find($id);
        
        if ($users) {
            $entityManager->remove($users);
            $entityManager->flush();

            $this->addFlash('notice', 'Suppression réussi !!');
        } else {
            $this->addFlash('error', 'Utilisateur non trouvé.');
        }

        return $this->redirectToRoute('app_dashboard_admin');
    }
}
