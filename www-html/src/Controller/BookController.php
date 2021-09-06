<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BookController
 *
 * @package App\Controller
 * @Route("/book")
 */
class BookController extends AbstractController
{

    /**
     * @param int $id
     * @param \App\Repository\BookRepository $bookRepository
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/book/{id}", name="book", requirements={"id"="\d+"}, methods={"GET"})
     */
    public function index(int $id, BookRepository $bookRepository): Response
    {
        if ($book = $bookRepository->find($id)) {
            return new JsonResponse(['name' => $book->getName()]);
        }
        $this->createNotFoundException("Book $id not found");
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Repository\BookRepository $bookRepository
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \Psr\Log\LoggerInterface $logger
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/create", name="book_create", methods={"PUT"})
     */
    public function create(Request $request, BookRepository $bookRepository, EntityManagerInterface $entityManager, LoggerInterface $logger): Response
    {

    }

}
