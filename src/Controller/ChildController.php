<?php

namespace App\Controller;

use App\Entity\Child;
use App\Form\ChildType;
use App\Repository\ChildRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/child')]
class ChildController extends AbstractController
{
    #[Route('/', name: 'app_child_index', methods: ['GET'])]
    public function index(ChildRepository $childRepository, UserRepository $userRepository): Response
    {
        $children = $childRepository->findAll();

        // je test si dans le tableau des roles de mon profil si la valeur ROLE_PARENT existe
        if (in_array('ROLE_PARENT', $this->getUser()->getRoles())) {

            // si je suis un parent, je vais récupérer dans la bdd
            // les enfants qui ont en user_id la valeur de mon id de user connecté
            $children = $childRepository->findBy(['user' => $this->getUser()]);
        }

        return $this->render('child/index.html.twig', [
            'children' => $children,
            
        ]);
    }

    #[Route('/new', name: 'app_child_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ChildRepository $childRepository): Response
    {
        $child = new Child();
        $form = $this->createForm(ChildType::class, $child);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $childRepository->save($child, true);
            // ajout message alert quand lenfant ets bien enregistrer
            $this->addFlash('success', 'vous avez ajouter un enfant avec succes');

            return $this->redirectToRoute('app_child_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('child/new.html.twig', [
            'child' => $child,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_child_show', methods: ['GET'])]
    public function show(Child $child): Response
    {
        return $this->render('child/show.html.twig', [
            'child' => $child,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_child_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Child $child, ChildRepository $childRepository): Response
    {
        $form = $this->createForm(ChildType::class, $child);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $childRepository->save($child, true);

            return $this->redirectToRoute('app_child_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('child/edit.html.twig', [
            'child' => $child,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_child_delete', methods: ['POST'])]
    public function delete(Request $request, Child $child, ChildRepository $childRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$child->getId(), $request->request->get('_token'))) {
            $childRepository->remove($child, true);
        }

        return $this->redirectToRoute('app_child_index', [], Response::HTTP_SEE_OTHER);
    }
}
