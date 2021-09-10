<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\SearchExpression;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BookController
 *
 * @package App\Controller
 */
class BookController extends AbstractController
{


    /**
     * @param int $id
     * @param \App\Repository\BookRepository $bookRepository
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\SearchExpression $searchExpression
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/{_locale<%app.supported_locales%>}/book/{id}", name="book", requirements={"id"="\d+"}, methods={"GET"})
     */
    public function index(int $id, BookRepository $bookRepository, Request $request, SearchExpression $searchExpression): Response
    {
        if ($book = $bookRepository->find($id)) {
            return new JsonResponse(['id' => $book->getId(), 'Name' => $book->getName($searchExpression->getCurrentLangId($request)), 'Author' => $book->getAuthorArray()]);
        }

        return new JsonResponse(['message' => 'Not found'], Response::HTTP_NOT_FOUND);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Repository\BookRepository $bookRepository
     * @param \App\Repository\AuthorRepository $authorRepository
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     *
     * @return \Symfony\Component\  HttpFoundation\Response
     * @Route("/book/create", name="book_create", methods={"POST"})
     */
    public function create(
      Request $request,
      BookRepository $bookRepository,
      AuthorRepository $authorRepository,
      EntityManagerInterface $entityManager
    ): Response {
        if (($json = $request->getContent()) && ($json = json_decode($json)) && (property_exists($json, 'name'))) {
            if (!$book = $bookRepository->findOneBy(['Name' => $json->name])) {
                $book = new Book();
                $book->setName($json->name);
                if (property_exists($json, 'author') && is_array($json->author)) {
                    $book->searchAddAuthor($json->author, $authorRepository, $entityManager);
                }
                $entityManager->persist($book);
                $entityManager->flush();
            }

            return new JsonResponse(['id' => $book->getId()]);
        }

        return new JsonResponse(['message' => 'No required fields', "request" => $request->getContent()], Response::HTTP_NOT_ACCEPTABLE);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Repository\BookRepository $bookRepository
     * @param \App\SearchExpression $searchExpression
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/{_locale<%app.supported_locales%>}/book/search", name="book_search", methods={"SEARCH"})
     */

    public function search(Request $request, BookRepository $bookRepository, SearchExpression $searchExpression): Response
    {
        $books = [];
        if ($expression = $searchExpression->getExpression($request)) {
            $books = $bookRepository->findByNameLike($expression, $this->getParameter('app.search_items_limit'));
        }

        return new JsonResponse($books);
    }

}
