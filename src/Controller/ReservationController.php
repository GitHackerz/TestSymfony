<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Vol;
use App\Form\ReservationType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController
{
    #[Route('/reservation', name: 'app_reservation')]
    public function index(): Response
    {
        return $this->render('reservation/index.html.twig', [
            'controller_name' => 'ReservationController',
        ]);
    }

    #[Route('/reservation/add', name: 'reservation_add')]
    public function add(ManagerRegistry $doctrine, Request $request): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation)
            ->add('save', SubmitType::class, ['label' => 'Create Reservation']);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();

            $reservation = $form->getData();
            $entityManager->persist($reservation);
            $entityManager->flush();

            $vol = $doctrine->getRepository(Vol::class)->find($reservation->getVol()->getId());
            $vol->setNbReservation($vol->getNbReservation() + 1);
            $entityManager->persist($vol);
            $entityManager->flush();

            return $this->redirectToRoute('app_reservation');

        }
        return $this->render('reservation/add.html.twig', [
            'form' => $form->createView(),
        ]);

    }


}
