<?php

namespace App\Controller;

use App\Repository\ChildRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{
    #[Route('/booking', name: 'app_booking')]
    public function index(): Response
    {

        // Premier jour du mois
        $firstDay = 1;

        // Dernier jour du mois
        $lastDay = date("t", strtotime($firstDay));

        return $this->render('booking/index.html.twig', [
            'child'     => $this->getUser()->getChild(),
            'firstDay'  => $firstDay,
            'lastDay'   => $lastDay,
        ]);
    }

    #[Route('/bookingrecap', name: 'app_booking_recap')]
    public function recap(Request $request, ChildRepository $childRepository): Response
    {
        $totalFacture = 0;
        $totalByChild = [];
        $tabChild = [];

        foreach ($request->request as $key => $val) {

            $totalChild = 0;

            // Je recupere l'id de l'enfant dans la chaine date_x
            $id = str_replace("date_", "", $key);

            // Je recupere l'enfant par rapport a l'id
           
            $tabChild[] = $child = $childRepository->findOneById($id);
            foreach ($val as $row) {

                
                
                $price = $child->getMenu()->getPrice();
                $totalFacture += $price;

                $totalChild += $price;
                
            }
            $totalByChild[$id] = $totalChild;
        }   

        return $this->render('booking/recap.html.twig', [
            'tabChild' => $tabChild,
            'totalByChild' => $totalByChild,
            'totalFacture' => $totalFacture,
            'tabDay' => $request->request
        ]);
    }
}
