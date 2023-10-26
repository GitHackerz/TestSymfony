<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[Route('/book', name: 'book_list')]
    public function index(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findAll();
        return $this->render('book/index.html.twig', [
            'books' => $books,
        ]);
    }

    #[Route('/book/add', name: 'book_add')]
    public function add(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book)
            ->add('save', SubmitType::class, ['label' => 'Create Book']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $managerRegistry->getManager();

            $book = $form->getData();
            $em->persist($book);
            $em->flush();
            return $this->redirectToRoute('book_list');
        }
        return $this->render('book/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/book/one/{id}', name: 'book_show')]
    public function show(int $id, BookRepository $bookRepository): Response
    {
        $book = $bookRepository->find($id);
        return $this->render('book/show.html.twig', [
            'controller_name' => 'BookController',
            'book' => $book,
        ]);
    }

    #[Route('/book/edit/{id}', name: 'book_edit')]
    public function edit(Request $request, ManagerRegistry $managerRegistry, int $id): Response
    {
        $book = $managerRegistry->getRepository(Book::class)->find($id);
        $form = $this->createForm(BookType::class, $book)
            ->add('save', SubmitType::class, ['label' => 'Update Book']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $managerRegistry->getManager();

            $book = $form->getData();
            $em->persist($book);
            $em->flush();
            return $this->redirectToRoute('book_list');
        }
        return $this->render('book/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/book/delete/{id}', name: 'book_delete')]
    public function delete(ManagerRegistry $managerRegistry, int $id): Response
    {
        $book = $managerRegistry->getRepository(Book::class)->find($id);
        $em = $managerRegistry->getManager();

        $em->remove($book);
        $em->flush();
        return $this->redirectToRoute('book_list');
    }

    #[Route('/book/triQB', name: 'book_triQB')]
    public function triQB(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->triQB();
        return $this->render('book/index.html.twig', [
            'books' => $books,
        ]);
    }

    #[Route('/book/triDQL', name: 'book_triDQL')]
    public function triDQL(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->triDQL();
        return $this->render('book/index.html.twig', [
            'books' => $books,
        ]);
    }

    #[Route('/book/search', name: 'book_search')]
    public function searchByID(BookRepository $bookRepository): Response
    {
        $title = $_POST['title'];
        if (empty($title)) {
            return $this->redirectToRoute('book_list');
        }
        $books = $bookRepository->searchByTitle($title);
        return $this->render('book/index.html.twig', [
            'books' => $books,
        ]);
    }
}
