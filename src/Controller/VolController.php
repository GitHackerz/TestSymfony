<?php

namespace App\Controller;

use App\Repository\VolRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VolController extends AbstractController
{
    #[Route('/vol', name: 'app_vol')]
    public function index(VolRepository $repository): Response
    {
        $vols = $repository->findAll();
        return $this->render('vol/index.html.twig', [
            'vols' => $vols,
        ]);
    }
}
