<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'users' => $users
        ]);
    }

    #[Route('/user/delete/{id}', name: 'app_user_del')]
    public function delete(ManagerRegistry $doctrine, int $id): Response
    {
        $user = $doctrine->getRepository(User::class)->find($id);
        $entityManager = $doctrine->getManager();

        $entityManager->remove($user);
        $entityManager->flush();
        return $this->redirectToRoute('app_user');
    }

    #[Route('/user/add', name: 'app_user_add')]
    public function add(Request $request, ManagerRegistry $doctrine): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user)
            ->add('save', SubmitType::class, ['label' => 'Create User']);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();

            $user = $form->getData();
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_user');
        }

        return $this->render('user/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/user/edit/{id}', name: 'app_user_edit')]
    public function edit(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $user = $doctrine->getRepository(User::class)->find($id);
        $form = $this->createForm(UserType::class, $user)
            ->add('save', SubmitType::class, ['label' => 'Edit User']);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_user');
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }


}
