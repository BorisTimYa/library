<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
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

    const SEARCH_LIMIT = 20;

    /**
     * @param int $id
     * @param \App\Repository\BookRepository $bookRepository
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/{_locale<%app.supported_locales%>}/book/{id}", name="book", requirements={"id"="\d+"}, methods={"GET"})
     */
    public function index(int $id, BookRepository $bookRepository, Request $request): Response
    {
        if ($book = $bookRepository->find($id)) {
            $locale = $request->getLocale();
            $locales = explode('|', $this->getParameter('app.supported_locales'));
            $langId = array_search($locale, $locales);
            $authors = [];
            foreach ($book->getAuthor() as $author) {
                $authors[] = ['id' => $author->getId(), 'Name' => $author->getName()];
            }

            return new JsonResponse(['id' => $book->getId(), 'Name' => $book->getName($langId), 'Author' => $authors]);
        }

        return new JsonResponse(['message' => 'Not found'], Response::HTTP_NOT_FOUND);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Repository\BookRepository $bookRepository
     * @param \App\Repository\AuthorRepository $authorRepository
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/book/create", name="book_create", methods={"PUT"})
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
                    foreach ($json->author as $authorValue) {
                        if (is_numeric($authorValue)) {
                            if ($author = $authorRepository->find($authorValue)) {
                                $book->addAuthor($author);
                            }
                        } elseif ($author = $authorRepository->findOneBy(['Name' => $authorValue])) {
                            $book->addAuthor($author);
                        } else {
                            $author = new Author();
                            $author->setName($authorValue);
                            $entityManager->persist($author);
                            $entityManager->flush();
                            $book->addAuthor($author);
                        }
                    }
                }

                $entityManager->persist($book);
                $entityManager->flush();
            }

            return new JsonResponse(['id' => $book->getId(),]);
        }

        return new JsonResponse(
          ['message' => 'No required fields', "request" => $request->getContent()],
          Response::HTTP_NOT_ACCEPTABLE
        );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Repository\BookRepository $bookRepository
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/{_locale<%app.supported_locales%>}/book/search", name="book_search", methods={"SEARCH"})
     */

    public function search(Request $request, BookRepository $bookRepository): Response
    {
        $books = [];
        if ($search = $request->getContent()) {
            $locale = $request->getLocale();
            $locales = explode('|', $this->getParameter('app.supported_locales'));
            foreach ($locales as &$loc) {
                if ($loc == $locale) {
                    $loc = "%$search%";
                } else {
                    $loc = '%';
                }
            }
            $searchExpression = implode('|', $locales);

            $books = $bookRepository->findByNameLike($searchExpression, self::SEARCH_LIMIT);
        }

        return new JsonResponse($books);
    }

}
