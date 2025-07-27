<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AdminEditFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class DashboardProfilController extends AbstractController
{
    #[Route('/dashboardProfil', name: 'app_dashboard_profil')]
    public function index( EntityManagerInterface $entityManager): Response
    {
        $dataBs= $entityManager -> getRepository(User::class)->findBy(['id'=> $this->getUser()]); // récupérer les données de l'utilisateur connecté

        return $this->render('dashboard_profil/index.html.twig', [
            'controller_name' => 'DashboardProfilController',
            'dataBs' => $dataBs, // passer les données à la vue
        ]);
    }

    #[Route('/editProfil/{id}', name: 'app_editProfil_form')]
    public function editForm($id ,Request $request, EntityManagerInterface $entityManager ): Response
    {
        $users = $entityManager->getRepository(User::class)->find($id);
        $form = $this->createForm(AdminEditFormType::class, $users );
        $form->handleRequest($request);
        $photoFile = $form->get('photo')->getData();
        if ($photoFile) {
            // Generate a unique filename for the photo
            $newFilename = uniqid() . '.' . $photoFile->guessExtension();

            // Move the file to the directory where photos are stored
            $photoFile->move(
                $this->getParameter('photos_directory'),
                $newFilename
            );

            // Set the new filename in the user entity
            $users->setPhoto($newFilename);
        }

        // dd($form->getData(), $users);
        if ($form->isSubmitted() && $form->isValid()) {  
            $entityManager->persist($users);
            $entityManager-> flush();
            
            $this->addFlash('notice','Modification réussi !!');
            return $this->redirectToRoute('app_dashboard_profil');
        }  
        // var_dump($form->isSubmitted() && $form->isValid());
        return $this->render('dashboard_profil/profilEdit.html.twig', [
            'controller_name' => 'DashboardProfilController',
            'form' => $form

        ]);
    }
}
