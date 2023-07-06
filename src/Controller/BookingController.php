<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Child;
use App\Repository\BookingRepository;
use App\Repository\ChildRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
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
    public function recap(Request $request, ChildRepository $childRepository, RequestStack $requestStack): Response
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

        // On va créer la session pour récupérer plus tard les données
        $session = $requestStack->getSession();
        $session->set('tabChild', $tabChild);
        $session->set('tabDay', $request->request);

        return $this->render('booking/recap.html.twig', [
            'tabChild' => $tabChild,
            'totalByChild' => $totalByChild,
            'totalFacture' => $totalFacture,
            'tabDay' => $request->request
        ]);
    }

    #[Route('/bookingsave', name: 'app_booking_save')]
    public function save(ChildRepository $childRepository, BookingRepository $bookingRepository, RequestStack $requestStack): Response
    {

        $session = $requestStack->getSession();
        $tabChild = $session->get('tabChild');
        $tabDay = $session->get('tabDay');

        foreach ($tabChild as $rowChild) {

            
            foreach ($tabDay as $key => $rowDay) {
                
                // Condition pour ne traiter que les date de l'enfant
                // Concerné dans l'itération de la boucle
                if (str_replace("date_", "", $key) == $rowChild->getId()) {
                    

                    // Boucle sur les jours du tableau des jours
                    foreach ($rowDay as $day) {

                        $date = explode('/', $day);
                        //Inversion de la date pour etre dans la formation yyyy-mm-jj
                        $newDate = new DateTime($date[2].'-'.$date[1].'-'.$date[0]);

                        // On test pour voir si la date existe deja dans les bookings
                        $booking = $bookingRepository->findOneByDate($newDate);

                        // Si elle n'existe pas deja on la crée
                        if (empty($booking)) {

                            // Mise en place de l'objet booking
                            $booking = new Booking();
                            $booking->setDate($newDate);

                            // Sauvegarde en bdd de l'objet
                            $bookingRepository->save($booking, true);
                        }

                        $child = $childRepository->findOneById($rowChild->getId());
                        $child->addBooking($booking);
                        $childRepository->save($child, true);

                    }
                }         
            }
        }
        $this->addFlash('success', 'Votre commande a bien été prise en compte');
        return $this->redirectToRoute('app_home');
    }

    #[Route('/bookingshow', name: 'app_booking_show')]
    public function show(): Response
    {
        return $this->render('booking/show.html.twig');
    }
}
