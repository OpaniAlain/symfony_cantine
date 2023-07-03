<?php

namespace App\Controller;

use App\Entity\Staff;
use App\Form\StaffType;
use App\Repository\StaffRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/staff')]
class StaffController extends AbstractController
{
    #[Route('/', name: 'app_staff')]
    public function index(StaffRepository $staffRepository): Response
    {
        // Récupération des informations en bdd
        $staffs = $staffRepository->findAll();
        
        // Passage des informations vers la vue
        return $this->render('staff/index.html.twig', [
            'staffs'    => $staffs,
        ]);
    }

    #[Route('/add', name: 'app_staff_add')]
    public function add(Request $request, StaffRepository $staffRepository): Response
    {
        // définition de l'objet user qui sera remplis
        $staff = new Staff();

        // Appel de l'objet formulaire pour affichage
        $form = $this->createForm(StaffType::class, $staff);

        //Appel de la fonction qui doit faire matcher les informations en provenance du formulaire avec les attributs de l'objet
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {

            // On enregistre les données de l'user en BDD
            // TODO
            $staffRepository->save($staff, true);

            // On redirige l'utilisateur
            return $this->redirectToRoute('app_staff');

        }
        
        
        // Passage des informations vers la vue
        return $this->render('staff/add.html.twig', [
            'form'  => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'app_staff_edit')]
    public function edit(Staff $staff, Request $request, StaffRepository $staffRepository): Response
    {
        // Appel de l'objet formulaire pour affichage
        $form = $this->createForm(StaffType::class, $staff);

        //Appel de la fonction qui doit faire matcher les informations en provenance du formulaire avec les attributs de l'objet
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {

            // On enregistre les données de l'user en BDD
            // TODO
            $staffRepository->save($staff, true);

            // On redirige l'utilisateur
            return $this->redirectToRoute('app_staff');

        }
        
        
        // Passage des informations vers la vue
        return $this->render('staff/add.html.twig', [
            'form'  => $form->createView(),
        ]);
    }


    #[Route('/detail/{id}', name: 'app_staff_detail')]
    public function detail(Staff $staff, Request $request, StaffRepository $staffRepository): Response
    {
        // Passage des informations vers la vue
        return $this->render('staff/detail.html.twig', [
            'staff'    => $staff,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_staff_delete')]
    public function delete(Staff $staff, Request $request, StaffRepository $staffRepository): Response
    {
        $staffRepository->remove($staff, true);

        return $this->redirectToRoute('app_staff');
    }

}
